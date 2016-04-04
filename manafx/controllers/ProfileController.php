<?php
namespace Manafx\Controllers;

use Phalcon\Tag;

use Phalcon\Mvc\Model\Criteria;
use Manafx\Forms\UsersForm;
use Manafx\Models\Users;
use Manafx\Models\User_Roles;
use Manafx\Models\Usermeta;

/**
 * Manafx\Controllers\ProfileController
 * CRUD to manage user profile
 */
class ProfileController extends \ManafxAdminController {

  /**
   * Default action, shows the search form
   */
  public function indexAction()
  {
		if ($this->session->has("auth-i")) {
			$identity = $this->session->get('auth-i');
		} else {
			$this->view->disable();
			$this->response->redirect(ADMIN_ROUTE);
			return;
		}
		$user_id = $identity["user_id"];
		$user = Users::findFirst(array(
			"user_id = $user_id",
			"columns" => "user_id, user_firstname, user_lastname, user_email, user_username"
		));
		
		$usermeta = Usermeta::find(array(
			"conditions" => "usermeta_user_id = $user_id AND usermeta_tag = 'profile'",
			"columns" => "usermeta_id, usermeta_key, usermeta_value"
		));

		$profile = array();
		if ($usermeta) {
			foreach($usermeta as $meta) {
				$usermeta_key = $meta->usermeta_key;
				$usermeta_value = $meta->usermeta_value;
				$profile[$usermeta_key] = $usermeta_value;
			}
		}
    $this->view->user = $user;
    $this->view->profile = $profile;
    $this->view->form = new \Manafx\Forms\ProfileForm($user);
  }

  /**
   * Users must use this action to change its password
   */
  public function changePasswordAction()
  {
    $this->adminNavbarRightMenu->setActive(array("_users", "_users_profile_change_password"));
    $this->adminSidebarMenu->setActive(array("_users", "_users_profile_change_password"));
	  $form = new \Manafx\Forms\ChangePasswordForm();
	  if ($this->request->isPost()) {
			if (!$form->isValid($this->request->getPost())) {
				foreach ($form->getMessages() as $message) {
					$this->flashSession->error($message);
				}
			} else {
				$old_password = $this->request->getPost('old_password');
				$new_password = $this->request->getPost('user_password');
				$this->auth->changeUserPassword("*", $old_password, $new_password);
			}
	  }
		Tag::resetInput();
	  $this->view->form = $form;
  }

  public function updateProfileAction()
  {
  	$this->view->disable();
	  if ($this->request->isPost()) {
			if ($this->session->has("auth-i")) {
				$identity = $this->session->get('auth-i');
			} else {
				$this->response->redirect(ADMIN_ROUTE);
				return;
			}
			$user_id = $identity["user_id"];
			$user_name = $identity["user_username"];
			$password = $this->request->getPost('user_password');
			
			if (!$this->auth->check($user_id, $password)) {
				$this->flashSession->error("Password not match!");
				return $this->response->redirect(ADMIN_ROUTE . "/profile");
				return false;
			}
			
			$datane['user_firstname'] = $this->request->getPost('user_firstname', 'striptags');
			$datane['user_lastname'] = $this->request->getPost('user_lastname', 'striptags');
			$datane['user_email'] = $this->request->getPost('user_email', 'email');
			$datane['user_username'] = $this->request->getPost('user_username', 'striptags');
			
			$form = new \Manafx\Forms\ProfileForm();

			if (!$form->isValid($this->request->getPost())) {
				$msg = "";
		    foreach ($form->getMessages() as $message) {
					if ($this->request->isAjax() == true) {
						$msg .= $message . "<br/>";
					} else {
						$this->flashSession->error($message);
					}
		    }
        if ($this->request->isAjax() == true) {
					$return = array("status" => "0", "msg" => $msg);
				}
			} else {
				$user = Users::findFirst("user_id = '" . $user_id . "'");
      	$user->assign($datane);
				$cmd = $user->update();
	
	      if (!$cmd) {
		    	$msg = "";
					foreach ($user->getMessages() as $message) {
				    $msg .= $message . "<br/>";
					}
					if ($this->request->isAjax() == true) {
			    	$success = "0";
				    $return = array("status" => $success, "msg" => $msg);
					} else {
		        $this->flashSession->error($user->getMessages());
			  	}
				} else {
	        $user_id = $user->user_id;
	        // save usermeta
	        $metaposts = $this->request->getPost("usermeta");
	        $usermeta = array();
	        foreach ($metaposts as $key => $val) {
	        	$usermeta[] = array("usermeta_user_id" => $user_id, "usermeta_key" => $key, "usermeta_value" => $val, "usermeta_tag" => "profile");
        	}
	        $this->saveUsermeta($usermeta);
	        
	        
	        Tag::resetInput();
					if ($this->request->isAjax() == true) {
		    		$success = "1";
		      	$msg = "Profile was updated successfully";
			    	$return = array("status" => $success, "msg" => $msg, "user_id" => $user_id);
					} else {
		        $this->flashSession->success("Profile was updated successfully");
					}
				}
			}
			if ($this->request->isAjax() == true) {
				echo json_encode($return);
			} else {
				return $this->response->redirect(ADMIN_ROUTE . "/profile");
			}
	  }
  }
  
  function saveUsermeta($usermeta) {
  	foreach ($usermeta as $meta) {
  		$metane = Usermeta::findFirst("usermeta_user_id = $meta[usermeta_user_id] AND usermeta_key = '$meta[usermeta_key]'");
			
  		
  		if ($metane !== false) {
  			$metane->assign($meta);
  			$ret = $metane->update();
 			} else {
 				$dbmeta = new Usermeta();
 				$dbmeta->assign($meta);
 				$ret = $dbmeta->save();
			}
  		
			if ($ret == false) {
				$msg = "Something wrong with usermeta data: <br />";
				$msg .= "<ul>";
		    foreach ($dbmeta->getMessages() as $message) {
	        $msg .= "<li>" . $message . "</li>";
		    }
				$msg .= "</ul>";
        $this->flashSession->error($message);
			}
 		}
  	
 	}
  
  
}
