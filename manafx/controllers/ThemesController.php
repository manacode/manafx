<?php

namespace Manafx\Controllers;
use Manafx\Models\Options;

class ThemesController extends \ManafxAdminController {
	public $messages;
	
	public function indexAction() {
		$templates = $this->grabTemplates();
		$this->view->templates = $templates;
	}
	
	public function activateAction() {
		if ($this->request->isPost()) {
			$template2activate = $this->request->getPost('template2activate', 'striptags');
			$theme2activate = $this->request->getPost('theme2activate', 'striptags');
			
			if ($template2activate=="") {
				$template2activate = $this->current_template;
			} else {
				$data["template"] = $template2activate;
			}
			$data["theme"] = $theme2activate;
			$this->updateSettings($data, "application");
			if ($this->messages=="") {
				$this->flashSession->success("Template \"". $template2activate . " with Theme \"" . $theme2activate . "\" was activated successfully");
			} else {
				$this->flashSession->error($this->messages);
			}
			$this->response->redirect(ADMIN_ROUTE . "/themes");
		
		}
	}

	public function deleteAction() {
		if ($this->request->isPost()) {
			$template2delete = $this->request->getPost('template2delete', 'striptags');
			$template_dir = APP_PATH . '/public/templates/' . $template2delete;
			$ret = false;
			if (is_dir($template_dir)) {
				$ret = deleteDir($template_dir);
				if ($ret) {
					$this->flashSession->success("Theme \"" . $theme2delete . "\" was deleted successfully");
				} else {
					$this->flashSession->error("Delete failed.");
				}
			} else {
				$this->flashSession->error("Theme not found.");
			}
			$this->response->redirect(ADMIN_ROUTE . "/themes");
		}
	}

	public function updateSettings($settings, $option_identity = "public") {
  	$msg = "";
		foreach ($settings as $option_name => $value) {
			$filter = "option_name = '" . $option_name . "'";
			$option = Options::findFirst($filter);
			if (!$option) {
				$newSetting = array("option_name" => $option_name, "option_value" => $value, "option_autoload" => "Y", "option_identity" => $option_identity, "option_description" => "");
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

	public function xupdateSettings($settings) {
  	$msg = "";
		foreach ($settings as $setting => $value) {
			$filter = "option_name = '" . $setting . "'";
			$setting = Options::findFirst($filter);
			if (!$setting) {
				$newSetting = array("option_name" => $setting, "option_value" => $value, "option_autoload" => "Y", "option_identity" => "system", "option_description" => "");
				if (!$this->createSetting($newSetting)) {
					$msg .= $this->messages . "<br/>";
				}
			} else {
				$datane["option_value"] = $value;
      	$setting->assign($datane);
				if (!$setting->update()) {
					foreach ($user->getMessages() as $message) {
				    $msg .= $message . "<br/>";
					}
				}
			}
		}
		$this->messages = $msg;
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

	function grabTemplates() {
		$dir = APP_PATH . '/public/templates';
		$uri = $this->config->application->baseUrl . '/templates';
		$imgfs = array(".png", ".gif", ".jpg");
		$templates = array();
		if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
        if (is_dir($dir . "/" . $entry) && $entry != "." && $entry != "..") {
        	$template_info_path = $dir . "/" . $entry . "/template.xml";
        	if (is_file($template_info_path)) {
	          $info_xml = simplexml_load_file($template_info_path);
	          $info_json = json_encode($info_xml);
						$info_array = json_decode($info_json, true);

        		$screenshot_path = $dir . "/" . $entry . "/screenshot";
        		$screenshot_uri = $uri . "/" . $entry . "/screenshot";
        		$screenshot = "";
        		foreach($imgfs as $imgf) {
        			if (is_file($screenshot_path . $imgf)) {
        				$screenshot = $screenshot_uri . $imgf;
        				break;
       				}
       			}
						$info_array["screenshot"] = $screenshot;
						$info_array["themes"] = $this->grabThemes($entry);
	          $templates[$entry] = $info_array;
         	}
        }
	    }
	    closedir($handle);
		}
		return $templates;
	}

	function grabThemes($template) {
		$dir = APP_PATH . '/public/templates/' . $template . '/themes';
		$uri = $this->config->application->baseUrl . '/templates/' . $template . '/themes';
		$imgfs = array(".png", ".gif", ".jpg");
		$themes = array();
		if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
        if (is_dir($dir . "/" . $entry) && $entry != "." && $entry != "..") {
					$info_array = array();
      		$screenshot_path = $dir . "/" . $entry . "/screenshot";
      		$screenshot_uri = $uri . "/" . $entry . "/screenshot";
      		$screenshot = "";
      		foreach($imgfs as $imgf) {
      			if (is_file($screenshot_path . $imgf)) {
      				$screenshot = $screenshot_uri . $imgf;
      				break;
     				}
     			}
					$info_array["screenshot"] = $screenshot;
          $themes[$entry] = $info_array;
        }
	    }
	    closedir($handle);
		}
		return $themes;
	}
}