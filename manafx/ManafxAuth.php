<?php

use \Phalcon\Events\Event;
use \Phalcon\Mvc\Dispatcher;
use Manafx\Models\Permissions;

/**
 * Security
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class ManafxAuth extends \Phalcon\Mvc\User\Component
{
	
	var $publicResources;
	
	public function __construct($dependencyInjector) {
		$this->_dependencyInjector = $dependencyInjector;
		$this->publicResources = array(
			array('manafx', 'errors', '*'),
			array('manafx', 'dashboard', 'index'),
			array('manafx', 'profile', 'index'),
			array('manafx', 'profile', 'ChangePassword'),
			array('manafx', 'admin', 'login'),
			array('manafx', 'admin', 'logout'),
			array('manafx', 'admin', 'recover'),
			array('manafx', 'index', 'login'),
			array('manafx', 'index', 'logout'),
			array('manafx', 'index', 'index')
		);
	}
	
	public function addPublicResource($publicResources) {
		$this->publicResources = array_merge($this->publicResources, $publicResources);
	}

	private function isAllowed($the_roles, $the_module, $the_controller, $the_action) {
		$allowed = false;
		
		foreach ($this->publicResources as $resource) {
			$module = $resource[0];
			$controller = $resource[1];
			$action = $resource[2];
			if ($action == "*") {
				$action = $the_action;
			}
			if ($controller == "*") {
				$controller = $the_controller;
			}
			
			if ($module==$the_module && $controller==$the_controller && $action==$the_action) {
				$allowed = true;
				break;
			}
		}

		if (!$allowed) {
			$condition = "";
			foreach ($the_roles as $role) {
				$role_id = $role['role_id'];
				if ($role_id == "1") {
					$allowed = true;
					break;
				}
				if ($condition=="") {
					$condition = "permission_role_id = '$role_id'";
				} else {
					$condition .= " OR permission_role_id = '$role_id'";
				}
			}
			$condition = "($condition)";
			if (!$allowed) {
				$condition = $condition . " AND permission_module = '" . $the_module . "' AND permission_controller = '" . $the_controller . "' AND permission_action = '" . $the_action . "'";
				$permission_found = Permissions::findFirst($condition);
			  if (!$permission_found) {
			  	$allowed = false;
			  } else {
			  	$allowed = true;
		  	}
	  	}
  	}
	 	return $allowed;
	}

	/**
	 * This action is executed before execute any action in the application
	 */
	public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
	{
		$auth = $this->session->get('auth-i');
		if (!$auth){
			$roles = array(array('role_id' => 10, 'role_name' => 'Guests'));
		} else {
			$roles = $auth['user_roles'];
		}

		$module = $dispatcher->getModuleName();
		if ($module == "") {
			$module = "manafx";
		}
		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();
		$allowed = $this->isAllowed($roles, $module, $controller, $action);
		
		if ($allowed == false) {
			$this->flashSession->error($this->t->_("ACCESS DENIED"));
			$this->session->set("redirect_to", $this->request->getURI());
			$this->view->disable();
			$this->response->redirect("errors/access-denied"); 
			return false;
		}

	}
}
