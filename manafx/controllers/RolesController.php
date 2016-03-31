<?php

namespace Manafx\Controllers;
use Manafx\Models\User_Roles as Roles;

class RolesController extends \ManafxAdminController {

	public function indexAction()
	{
		$this->auth->ced();
		$data = new Roles;
		$roles = $data->pfind(array(
	    // "conditions" => '(role_status = "A" OR role_status = "S") AND role_id != 1'
	    "conditions" => 'role_id != 1'
		));

		$this->view->pager = $data->getPager();
		$this->view->roles = $roles;
	}

	public function getRoleAction($role_id="")
	{
		if ($this->request->isPost()) {
			$role_id = $this->request->getPost('role_id');
		}
	  # $role = Roles::findFirstByrole_id($role_id);
	  $role = Roles::findFirst("role_id = '" . $role_id . "'");
	  if (!$role) {
    	$success = "0";
      $msg = "Role was not found";
    	$return = array("status" => $success, "msg" => $msg);
	  } else {
    	$success = "1";
	  	$data = $role;
    	$return = array("status" => $success, "data" => $data);
  	}
  	#$this->view->disable();
    echo json_encode($return);
	}

	private function isExists($field, $value)
	{
	  $role = Roles::findFirst("$field = '" . $value . "'");
	  if (!$role) {
	  	return false;
	  } else {
	  	return true;
  	}
	}
	
	public function createRoleAction()
	{
	  if ($this->request->isPost()) {
			$purpose =  $this->request->getPost('purpose', 'striptags');
			$role_name = $this->request->getPost('role_name', 'striptags');
			
			if ($this->isExists("role_name", $role_name)) {
				$return = array("status" => "0", "msg" => "Role name already exists!");
				$this->view->disable();
				echo json_encode($return);
				return;
			}
			$role = new Roles();

	    $role->assign(array(
        'role_name' => $role_name,
        'role_status' => $this->request->getPost('role_status', 'striptags'),
        'role_description' => $this->request->getPost('role_description', 'striptags')
	    ));
	
	    if (!$role->save()) {
	    	$success = "0";
	    	$msg = "";
				foreach ($role->getMessages() as $message) {
			    $msg .= $message->getMessage() . "<br/>";
				}
	    } else {
	    	$success = "1";
   			$role_id = $role->role_id;
      	$msg = "New role . `" . $role_name . "` was saved successfully";
	    }
	    $return = array("status" => $success, "msg" => $msg, "role_id" => $role_id);
	    $this->view->disable();
	    echo json_encode($return);
	  }
	
	}

	public function updateRoleAction()
	{
    $this->view->disable();
	  if ($this->request->isPost()) {
			$purpose =  $this->request->getPost('purpose', 'striptags');
			$role_name = $this->request->getPost('role_name', 'striptags');
			
			$role_id = $this->request->getPost('role_id');
			$role = Roles::findFirst("role_id = '" . $role_id . "'");

	    $role->assign(array(
        'role_name' => $role_name,
        'role_status' => $this->request->getPost('role_status', 'striptags'),
        'role_description' => $this->request->getPost('role_description', 'striptags')
	    ));
	
	    if (!$role->save()) {
	    	$success = "0";
	    	$msg = "";
				foreach ($role->getMessages() as $message) {
			    $msg .= $message->getMessage() . "<br/>";
				}
	    } else {
	    	$success = "1";
      	$msg = "Role was updated successfully";
	    }
	    $return = array("status" => $success, "msg" => $msg, "role_id" => $role_id);
	    echo json_encode($return);
	  }
	
	}

  public function deleteRolesAction($role_id="")
  {
		if ($this->request->isPost()) {
			$role_id = $this->request->getPost('role_id');
		}
		if (is_array($role_id)) {
			$role_id = implode(",", $role_id);
		}
		
		// convert to array and check if system default role ids included.
		$isSystem = false;
		$aRoleIds = explode(",", $role_id);
		foreach ($aRoleIds as $RoleId) {
			if ($RoleId<11) {
				$isSystem = true;
				break;
			}
		}
		if ($isSystem) {
			$return = array("status" => "0", "msg" => "Sorry, we can't delete the default system role(s).");
			$this->view->disable();
			echo json_encode($return);
			return;
		}
		//----
		
    $role = Roles::find("role_id IN ($role_id)");
		if ($role->delete() == false) {
			$success = "0";
			$msg = "Sorry, we can't delete the role(s) right now: \n";
			foreach ($role->getMessages() as $message) {
				$msg .= $message . "\n";
			}
		} else {
			$success = "1";
			$msg = "The role(s) was deleted successfully!";
		}
		$return = array("status" => $success, "msg" => $msg);
		$this->view->disable();
		echo json_encode($return);

  }

}