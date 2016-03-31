<?php
namespace Manafx\Controllers;

use Manafx\Models\User_Roles as Roles;
use Manafx\Models\Permissions;
/**
 * View and define permissions for the various profile levels.
 * 
 * use '_actions_permissions' method to force show the actions to selected role in the manage permissions
 * ie:
 * 	public function _actions_permissions()
 *	{
 *		return array(
 *			"loginAction" => array("*"),
 *			"logoutAction" => array("*"),
 *			"recoverAction" => array("*")
 *		);
 *	}
 * 
 */
class PermissionsController extends \ManafxAdminController {

	public $permisi = array();
	
	public function indexAction() {
		$data = new Roles;
		$roles = $data->pfind(array(
	    "conditions" => '(role_status = "A" OR role_status = "S") AND role_id != 1'
		));

		$this->view->pager = $data->getPager();
		$this->view->roles = $roles;
	}
	
	public function manageAction($role_id="") {
		if ($role_id=="") {
			$this->redirect2base();
			return false;
		}
		$role = Roles::findFirst(array(
			"role_id = '" . $role_id . "'",
			"columns" => array("role_id", "role_name")
		));
		
		$result = Permissions::find("permission_role_id = '" . $role_id . "'");
		$permissions = array();
		foreach($result as $permission) {
			$permissions[] = $permission->permission_module . "." .	$permission->permission_controller . "." . $permission->permission_action;
		}
		unset($result);
		
		$resources = $this->getResources($role);

		$this->view->role = $role;
		$this->view->permissions = $permissions;
		$this->view->resources = $resources;
	}
	
	private function getResources($role) {
		$sysdir = CORE_PATH . 'controllers';
		$dir = APP_PATH . '/modules';
		$permisi = array();
		
		$permisi["manafx"] = $this->scan_Controllers($sysdir, $role);

		$app_modules = $this->scan_Modules($dir);

		$active_modules = array();
		foreach ($this->config->application->active_modules as $active_module) {
			$active_modules[] = $active_module;
		}

		foreach($app_modules as $app_module) {
			if (in_array($app_module, $active_modules)) {
				$module_path = $dir . "/" . $app_module;
				$class_files = $this->scan_Controllers($module_path, $role);
				$permisi[$app_module] = $class_files;
			}
		}
		return $this->filterResources($permisi);
		#return $permisi;
	}
	
	private function filterResources($resources) {
		$ret = array();
		$publicResources = $this->ManafxAuth->publicResources;
		
		foreach($resources as $module => $controllers) {
			foreach ($controllers as $controller => $class) {
				$actions = $class['class_actions'];
				$ctl = array();
				foreach($actions as $action) {
					$act = str_replace("Action", "", $action);
					$value = array($module, $controller, $act);
					$is_public = false;
					if (in_array($value, $publicResources)) {
						$is_public = true;
					}
					#$ret[$module][] = array('controller'=>$controller, 'action'=>$act, 'is_public'=>$is_public);
					$ret[$module][$controller][] = array('action'=>$act, 'is_public'=>$is_public);
				}
			}
		}
		return $ret;
	}

	function scan_Controllers($dir, $role) {
	  $ffs = scandir($dir);
	  $i = 0;
	  $list = array();
	  foreach ( $ffs as $ff ) {
	    if ( $ff != '.' && $ff != '..' ) {
        if ( substr(strtolower($ff), -14) == 'controller.php' ) {
        	$controller = substr(strtolower($ff), 0, -14);
        	$class_path = $dir.'/'.$ff;
        	$class_name = substr($ff,0,-4);
        	
    			if ($controller==$this->controller_name) {
						$class_namespace = $this->class_name;
					} else {
	        	$class_namespace = $this->getClassNamespace($class_path);
	    			if ($class_namespace=="") {
							$class_namespace = $this->class_name;
						}
					}
        	$class_actions = $this->scan_Actions($class_namespace, $role);
        	if (!empty($class_actions)) {
          	$list[$controller] = array("class_name" => $class_name, "class_namespace" => $class_namespace, "class_dir" => $dir, "class_path" => $class_path, "class_actions" => $class_actions);
        	}
				}
	      if( is_dir($dir.'/'.$ff) ) {
					$newlist = $this->scan_Controllers($dir.'/'.$ff, $role);
					$list = array_merge($list, $newlist);
       	}
	    }
	  }
	  return $list;
	}

	function getClassNamespace($class_path) {
		$classes = get_declared_classes();
		$include = include_once ($class_path);
		$diff = array_diff(get_declared_classes(), $classes);
		$class = reset($diff);
		return $class;
	}

	function scan_Actions($class, $role) {
		$fclass = new $class;
		// check if _actions_permissions method exists
		$actions_permissions = array();
  	if (method_exists($fclass, "_actions_permissions")) {
  		$actions_permissions = $fclass->_actions_permissions();
		}
		$f = new \ReflectionClass($class);
		$methods = $f->getMethods();
		if ($methods) {
			$actionMethods = array();

			foreach ($methods as $method) {
				if ($method->class == $class) {
					$method_name = $method->name;
					if (substr($method_name,-6) == "Action") {
						$allowed = false;
						if (isset($actions_permissions[$method_name])) {
							$action_permit = $actions_permissions[$method_name];
							foreach ($action_permit as $action_role) {
								if (is_numeric($action_role)) {
									$role_id = $role->role_id;
									if ($action_role==$role_id) {
										$allowed = true;
										break;
									}
								} else {
									$role_name = $role->role_name;
									if ($action_role==$role_name || $action_role=="*") {
										$allowed = true;
										break;
									}
								}
							}
						} else {
							$allowed = true;
						}
						if ($allowed) {	
							$actionMethods[] = $method_name;
						}
					}
				}
			}
			return $actionMethods;
		}
	}

	function old_scan_Actions($class, $role) {
		$fclass = new $class;
		// check if _actions_permissions method exists
		$actions_permissions = array();
  	if (method_exists($fclass, "_actions_permissions")) {
  		$actions_permissions = $fclass->_actions_permissions();
		}
		
		$methods = get_class_methods($fclass);
		if ($methods) {
			$actionMethods = array();
			foreach ($methods as $method) {
				if (substr($method,-6) == "Action") {
					$allowed = false;
					if (isset($actions_permissions[$method])) {
						$action_permissions = $actions_permissions[$method];
						foreach ($action_permissions as $action_role) {
							if (is_numeric($action_role)) {
								$role_id = $role->role_id;
								if ($action_role==$role_id) {
									$allowed = true;
									break;
								}
							} else {
								$role_name = $role->role_name;
								if ($action_role==$role_name) {
									$allowed = true;
									break;
								}
							}
						}
					} else {
						$allowed = true;
					}
					if ($allowed) {	
						$actionMethods[] = $method;
					}
				}
			}
			return $actionMethods;
		}
	}
	
	public function updateAction() {
    $this->view->disable();
	  if ($this->request->isPost()) {
			$role_id = $this->request->getPost('role_id');

      if ($this->request->hasPost('permissions')) {
        // Deletes the current permissions
        $this->delete_permissions($role_id);
        // Save the new permissions
        $error = false;
	    	$msg = "";
        foreach ($this->request->getPost('permissions') as $permission) {
            $resources = explode('.', $permission);

            $permission = new Permissions();
            $permission->permission_role_id = $role_id;
            $permission->permission_module = $resources[0];
            $permission->permission_controller = $resources[1];
            $permission->permission_action = $resources[2];

            if (!$permission->save()) {
            	$error = true;
							foreach ($permission->getMessages() as $message) {
						    $msg .= $message->getMessage() . "<br/>";
							}
           	}
        }
        if ($error) {
					$this->flashSession->error("Permissions were updated with error: <br />" . $msg);
			 		$this->view->pick(ADMIN_ROUTE . "/permissions/manage/$role_id");
				} else {
	        $this->flashSession->success('Permissions were updated with success');
					$this->response->redirect(ADMIN_ROUTE . "/permissions");
					return false;
				}
      }
	  }
	}
	
	public function delete_permissions($role_id="") {
		if ($role_id=="") {
			$this->redirect2base();
			return false;
		}

    $permission = Permissions::find("permission_role_id = '" . $role_id . "'");
		if ($permission->delete() == false) {
			$success = "0";
			$msg = "Sorry, we can't delete the permission(s) right now: \n";
			foreach ($permission->getMessages() as $message) {
				$msg .= $message . "\n";
			}
		} else {
			$success = "1";
			$msg = "The role(s) was deleted successfully!";
		}
		$return = array("status" => $success, "msg" => $msg);
		echo json_encode($return);
	
	}
	
}
