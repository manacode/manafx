<?php

namespace Manafx\Controllers;
use Manafx\Models\Options;
use Manafx\Models\User_Roles;

class SettingsController extends \ManafxAdminController {
	public $messages;
	
	public function indexAction() {
	}
	
	public function generalAction() {
		$msg = "";
		$option_identity = "application";
		if ($this->request->isPost()) {
			$option_identity = $this->request->getPost("option_identity");
			$data = $this->request->getPost("data");
			$this->updateSettings($data, $option_identity);
			if ($this->messages=="") {
				$this->flashSession->success(ucfirst($option_identity) . " setting was updated successfully");
			} else {
				$this->flashSession->error($this->messages);
			}
			$this->view->disable();
			return $this->response->redirect($this->router->getRewriteUri());
		}
		$roles = User_Roles::find("role_id != 1");
		$this->view->fx_timezone = $this->dt->get_timezones();
		$this->view->roles = $roles;
		$this->view->default_role = $this->getDefaultRoleId();
		$this->view->option_identity = $option_identity;
		$this->view->data = $GLOBALS['g_config'];
		
		$pr = $this->ManafxAuth->publicResources;
		/* sort publicResources */
		$pr_mod = array();
		$lastmod = '';
		foreach($pr as $r) {
			$mod = $r[0];
			if ($lastmod !== $mod) {
				$lastmod = $mod;
				$pr_mod[$mod] = $lastmod;
			}
		}
		$pr_con = array();
		$lastcon = '';
		foreach($pr_mod as $key => $val) {
			$mod = $key;
			foreach($pr as $r) {
				if ($r[0] == $mod) {
					$con = $r[1];
					if ($lastcon !== $con) {
						$lastcon = $con;
						$pr_con[$mod][$con] = $lastcon;
					}
				}
			}
		}
		$pr_act = array();
		foreach($pr_con as $mod => $conts) {
			foreach($conts as $con => $val) {
				foreach($pr as $r) {
					if ($r[0] == $mod && $r[1]== $con) {
						$act = $r[2];
						if ($act == "*") {
							$act = "index";
						}
						$pr_act[$mod][$con][] = $act;
					}
				}
			}
		}
		$this->view->actions = $pr_act;
	}


	private function isExists($field, $value)
	{
	  $option = Options::findFirst("$field = '" . $value . "'");
	  if (!$option) {
	  	return false;
	  } else {
	  	return true;
  	}
	}

  function getDefaultRoleId() {
  	$default_role = $this->config->application->default_role;
  	if (!is_numeric($default_role)) {
			$role = User_Roles::findFirst("role_name = '" . $default_role . "'");
  		if ($role) {
  			$default_role = $role->role_id;
 			}
 		}
  	return $default_role;
 	}

	public function getSettingsValue($settings) {
	  if ($settings=="" || !is_array($settings)) {
	  	return false;
		}
	  $data = array();
 		foreach ($settings as $setting) {
	  	$condition = "option_name = '" . $setting . "'";
	  	// $dat = Options::findFirst(array($condition, array("field" => array("option_value"))));
	  	$dat = Options::findFirst(array(
				$condition, 
				"columns" => array("option_value")
			));
	  	if (!$dat) {
	  		$newSetting = array("option_name" => $setting, "option_value" => " ", "option_autoload" => "N", "option_description" => "");
	  		$this->createSetting($newSetting);
	  		$data[$setting] = "";
  		} else {
	  		$data[$setting] = $dat->option_value;
  		}
  	}
	  return $data;
	}

	function createSetting($data) {
		$setting = new Options();
		$setting->assign($data);
		if (!$setting->save()) {
			$msg = '';
			foreach ($setting->getMessages() as $message) {
				$msg .= $message->getMessage() . "<br/>";
			}
			$this->messages = $msg;
			return false;
		}
		return true;
	}

	public function getTimezoneDateAction() {
		$this->view->disable();
		$date_format = "";
		$timezone = "";
		if ($this->request->isPost()) {
			$date_format = $this->request->getPost('date_format', 'striptags');
			$timezone = $this->request->getPost('timezone', 'striptags');
		}
		$ret = $this->dt->mdatetime("", $timezone, $date_format);
		if ($this->request->isAjax() == true) {
	    // echo json_encode($ret);
			echo $ret;
		} else {
			return $ret;
		}
	}

	public function updateSettings($settings, $option_identity = "public") {
  	$msg = "";
		foreach ($settings as $option_name => $value) {
			$filter = "option_name = '" . $option_name . "'";
			$option = Options::findFirst($filter);
			if (!$option) {
				$newSetting = array("option_name" => $option_name, "option_value" => $value, "option_autoload" => "N", "option_identity" => $option_identity, "option_description" => "");
				if (!$this->createSetting($newSetting)) {
					$msg .= $this->messages . "<br/>";
				}
			} else {
				$datane["option_value"] = $value;
      	$option->assign($datane);
				if (!$option->update()) {
					foreach ($option->getMessages() as $message) {
				    $msg .= $message . "<br/>";
					}
				}
			}
			$this->updateGlobalConfigVar($option_identity, $option_name, $value);
		}
		$this->messages = $msg;
		$this->updateGlobalConfigFile();
	}
	
	public function modulesAction() {
		$this->view->active_modules = $this->config->application->active_modules;

		$modules = $this->grabModules();
		$this->view->modules = $modules;
	}
	
	public function activateModuleAction($module) {
		$am = $this->config->application->active_modules;
		$am = $am->toArray();
	
		$am[] = $module;
		
		$this->config->application->active_modules = $am;
		
		$data["active_modules"] = $am;
		
		$this->updateSettings($data, "application");
		if ($this->messages=="") {
			$this->flashSession->success("Module \"" . $module . "\" was activated successfully");
		} else {
			$this->flashSession->error($this->messages);
		}
		$this->response->redirect(ADMIN_ROUTE . "/settings/modules/");
	}

	public function deactivateModuleAction($module) {
		$am = $this->config->application->active_modules;
		
		$am = array_diff($am->toArray(), array($module));

		$this->config->application->active_modules = $am;
		
		$data["active_modules"] = $am;
		
		$this->updateSettings($data, "application");
		if ($this->messages=="") {
			$this->flashSession->success("Module \"" . $module . "\" was deactivated successfully");
		} else {
			$this->flashSession->error($this->messages);
		}
		$this->response->redirect(ADMIN_ROUTE . "/settings/modules/");
		
		
	}

	public function deleteModuleAction($module) {
		$module_dir = APP_PATH . '/modules/' . $module;
		$ret = false;
		if ($this->request->isPost()) {		
			if (is_dir($module_dir)) {
				$ret = deleteDir($module_dir);
			}
			return $ret;
		} else {
			if (is_dir($module_dir)) {
				$ret = deleteDir($module_dir);
				if ($ret) {
					$this->flashSession->success("Module \"" . $module . "\" was deleted successfully");
				} else {
					$this->flashSession->error("Delete failed.");
				}
			} else {
				$this->flashSession->error("Module not found.");
			}
			$this->response->redirect(ADMIN_ROUTE . "/settings/modules/");
		}
	}

	function grabModules() {
		$dir = APP_PATH . '/modules';
		$img_dir = APP_PATH . '/public/templates/' . $this->config->application->template . '/img';
		$img_uri = $this->config->application->baseUrl . '/templates/' . $this->config->application->template . '/img';
		$imgfs = array(".png", ".gif", ".jpg");
		$modules = array();
		if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
        if (is_dir($dir . "/" . $entry) && $entry != "." && $entry != "..") {
        	$module_info_path = $dir . "/" . $entry . "/module.xml";
        	if (is_file($module_info_path)) {
	          $info_xml = simplexml_load_file($module_info_path);
	          $info_json = json_encode($info_xml);
						$info_array = json_decode($info_json, true);

        		$screenshot_path = $img_dir . '/mod_' . $entry;
        		$screenshot_uri = $img_uri . '/mod_' . $entry;
        		$screenshot = $img_uri . '/mod_96x96.png';
        		foreach($imgfs as $imgf) {
        			if (is_file($screenshot_path . $imgf)) {
        				$screenshot = $screenshot_uri . $imgf;
        				break;
       				}
       			}
						$info_array["screenshot"] = $screenshot;
	          $modules[$entry] = $info_array;
         	}
        }
	    }
	    closedir($handle);
		}
		return $modules;
	}
	


}