<?php

namespace Manafx\Controllers;
use Manafx\Models\Menus;

class MenusController extends \ManafxAdminController {

	public function indexAction()
	{
		$this->auth->ced();
		$data = new Menus;
		$menus = $data->pfind(array(
	    "conditions" => 'menu_type = "i"'
		));

		$this->view->pager = $data->getPager();
		$this->view->menus = $menus;
	}

	public function getMenuAction($role_id="")
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
   echo json_encode($return);
	}

	private function isExists($conditions)
	{
	  $menu = Menus::findFirst(array("conditions"=>$conditions));
	  if (!$menu) {
	  	return false;
	  } else {
	  	return true;
  	}
	}
	
	public function createMenuAction()
	{
	  if ($this->request->isPost()) {
			$menu_key = $this->request->getPost('menu_key', 'striptags');
			
			if ($this->isExists("menu_key = '$menu_key' AND menu_type = 'i'")) {
				$return = array("status" => "0", "msg" => "Menu shortcode already exists!");
				echo json_encode($return);
				return;
			}
			$menu = new Menus();

	    $menu->assign(array(
	    	'menu_type' => "i",
        'menu_key' => $menu_key,
        'menu_title' => $this->request->getPost('menu_title', 'striptags'),
        'menu_action' => "",
        'menu_roles' => "*",
        'menu_status' => $this->request->getPost('menu_status', 'striptags'),
        'menu_description' => $this->request->getPost('menu_description', 'striptags')
	    ));
			$menu_id = "";
	    if (!$menu->create()) {
	    	$success = "0";
	    	$msg = "";
				foreach ($menu->getMessages() as $message) {
			    $msg .= $message->getMessage() . "<br/>";
				}
	    } else {
	    	$success = "1";
   			$menu_id = $menu->menu_id;
      	$msg = "New menu `" . $menu_key . "` was saved successfully";
	    }
	    $return = array("status" => $success, "msg" => $msg, "menu_id" => $menu_id);
	    echo json_encode($return);
	  }
	
	}

	public function updateMenuAction()
	{
	  if ($this->request->isPost()) {
	  	$menu_id = $this->request->getPost('menu_id', 'striptags');
			$menu_key = $this->request->getPost('menu_key', 'striptags');
			
			if ($this->isExists("menu_key = '$menu_key' AND menu_type = 'i' AND menu_id != $menu_id")) {
				$return = array("status" => "0", "msg" => "Menu shortcode already exists!");
				echo json_encode($return);
				return;
			}
			$menu = Menus::findFirst("menu_id = '" . $menu_id . "'");
	    $menu->assign(array(
        'menu_key' => $menu_key,
        'menu_title' => $this->request->getPost('menu_title', 'striptags'),
        'menu_status' => $this->request->getPost('menu_status', 'striptags'),
        'menu_description' => $this->request->getPost('menu_description', 'striptags')
	    ));
	
	    if (!$menu->update()) {
	    	$success = "0";
	    	$msg = "";
				foreach ($menu->getMessages() as $message) {
			    $msg .= $message->getMessage() . "<br/>";
				}
	    } else {
	    	$success = "1";
      	$msg = "Menu was updated successfully";
	    }
	    $return = array("status" => $success, "msg" => $msg, "menu_id" => $menu_id);
	    echo json_encode($return);
	  }
	}

  public function deleteMenusAction($menu_id="")
  {
		if ($this->request->isPost()) {
			$menu_id = $this->request->getPost('menu_id');
		}
		if (is_array($menu_id)) {
			$menu_id = implode(",", $menu_id);
		}
		
    $menu = Menus::find("menu_id IN ($menu_id) OR menu_parent IN ($menu_id)");
		if ($menu->delete() == false) {
			$success = "0";
			$msg = $this->t->_("delete_menu_failed", "Sorry, we can't delete the menu(s) right now: \n");
			foreach ($menu->getMessages() as $message) {
				$msg .= $message . "\n";
			}
		} else {
			$success = "1";
			$msg = $this->t->_("delete_menu_success", "The menu(s) was deleted successfully!");
		}
		$return = array("status" => $success, "msg" => $msg);
		echo json_encode($return);

  }

}