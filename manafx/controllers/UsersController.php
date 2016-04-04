<?php
namespace Manafx\Controllers;

use Phalcon\Tag;

use Phalcon\Mvc\Model\Criteria;
use Manafx\Forms\UsersForm;
use Manafx\Models\Users;
use Manafx\Models\User_Roles;

/**
 * Manafx\Controllers\UsersController
 * CRUD to manage users
 */
class UsersController extends \ManafxAdminController {

  /**
   * Default action, shows the search form
   */
  public function indexAction()
  {
  	$this->getAllUserStats();
    $this->view->form = new \Manafx\Forms\UsersSearchForm();
    #$this->view->roles = $this->getRoles();
  }

	function getAllUserStats() {
    $total_users = Users::count();
		$total_banned = Users::count("user_status = 'B'");
    $total_locked = Users::count("user_status = 'L'");

    $this->view->total_users = $total_users;
    $this->view->total_banned = $total_banned;
    $this->view->total_locked = $total_locked;
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

	public function getUserAction($user_id="")
	{
		if ($this->request->isPost()) {
			$user_id = $this->request->getPost('user_id');
		}
	  #$user = Users::findFirstByuser_id($user_id);
	   $user = Users::findFirst("user_id = '" . $user_id . "'");
	  if (!$user) {
    	$success = "0";
      $msg = "User was not found";
    	$return = array("status" => $success, "msg" => $msg);
	  } else {
    	$success = "1";
    	/*
	  	$roles = array();
	  	foreach($user->roles as $role) {
	  		$roles[] = array(
	  			"role_id" => $role->role_id,
	  			"role_name" => $role->role_name
	  		);
  		}
  		*/
	  	#$user->user_roles = $roles;
    	$return = array("status" => $success, "data" => $user);
  	}
		#$this->view->disable();
		echo json_encode($return);
	}

	private function isExists($field, $value)
	{
	  $user = Users::findFirst("$field = '" . $value . "'");
	  if (!$user) {
	  	return false;
	  } else {
	  	return true;
  	}
	}

	public function getRoles()
	{
		$roles_model = new User_Roles;
		$roles = $roles_model::find(array(
			'columns' => 'role_id, role_name',
			'conditions' => 'role_status != "X" AND role_status != "R" AND role_id != 1 AND role_id != 10',
		));
		# $roles = $roles_model->toAssocArray($roles, 'role_id', 'role_name');
		return $roles;
	}

  /**
   * Searches for users
   */
  public function searchAction()
  {
		$this->auth->ced();
		$query = Criteria::fromInput($this->di, 'Manafx\Models\Users', $this->request->getPost());
    $searchParams = $query->getParams();
      
    $this->persistent->searchParams = $searchParams;

    $parameters = array();
    if ($this->persistent->searchParams) {
			$parameters = $this->persistent->searchParams;
    }

 		$parameters['columns'] = "user_id, user_firstname, user_lastname, user_email, user_username, user_status, user_roles";
    if (isset($parameters['conditions'])) {
    	$parameters['conditions'] .= " AND user_id != 1";
   	} else {
   		$parameters['conditions'] = "user_id != 1";
 		}
 
		$role_filter = $this->request->getPost('roles');
		if ($role_filter) {
			$role_condition = "";
			foreach ($role_filter as $item) {
				if ($role_condition=="") {
					$role_condition .= "(user_roles LIKE '%,$item,%'";
				} else {
					$role_condition .= " OR user_roles LIKE '%,$item,%'";
				}
			}
			$role_condition .= ")";
			$parameters['conditions'] .= " AND $role_condition";
			
		}

		$users_model = new Users;
		$users = $users_model->pfind($parameters);
    
    if (count($users) == 0) {
      $this->flashSession->notice("The search did not find any users");
      return $this->dispatcher->forward(array(
	      "action" => "index"
      ));
    } else {
			$this->view->pager = $users_model->getPager();
    	$this->view->users = $users;
    	$this->view->roles = $this->getRoles();
			$this->view->default_role = $this->getDefaultRoleId();
		 	$this->view->form = new UsersForm();
    }
  }


  /**
   * Searches for users
   */
  public function searchAction_rdbms()
  {
  	$this->view->form = new UsersForm();
    $query = Criteria::fromInput($this->di, 'Manafx\Models\Users', $this->request->getPost());
    $searchParams = $query->getParams();
      
    $this->persistent->searchParams = $searchParams;

    $parameters = array();
    if ($this->persistent->searchParams) {
			$parameters = $this->persistent->searchParams;
    }

    if (isset($parameters['conditions'])) {
    	$parameters['conditions'] .= " AND user_id != 1";
   	} else {
   		$parameters['conditions'] = "user_id != 1";
 		}
 
		$role_filter = $this->request->getPost('roles');
    
		$mUsers = new Users;
		if ($role_filter) {
			$users = $mUsers->pfind($parameters)->filter(
				function($user) use ($role_filter) {
					foreach ($user->roles as $role) {
						if (in_array($role->role_id, $role_filter)) {
							return $user;
						}
					}
					
				}
			);
		} else {
			$users = $mUsers->pfind($parameters);
		}
    
    if (count($users) == 0) {
      $this->flashSession->notice("The search did not find any users");
      return $this->dispatcher->forward(array(
	      "action" => "index"
      ));
    } else {
			$this->view->pager = $mUsers->getPager();
    	$this->view->users = $users;
    }
  }
  
  /**
   * Creates a User
   */
  public function createUserAction()
  {
  	$this->adminNavbarLeftMenu->setActive(array("_users", "_users_create"));
  	$this->adminSidebarMenu->setActive(array("_users", "_users_create"));
	  if ($this->request->isPost()) {
  		$this->view->disable();
	  	$success = 0;
			$purpose =  $this->request->getPost('purpose', 'striptags');
			
			$datane['user_firstname'] = $this->request->getPost('user_firstname', 'striptags');
			$datane['user_lastname'] = $this->request->getPost('user_lastname', 'striptags');
			$datane['user_email'] = $this->request->getPost('user_email', 'email');
			$datane['user_username'] = $this->request->getPost('user_username', 'striptags');
			$datane['user_status'] = $this->request->getPost('user_status', 'striptags');
			$datane['user_registered'] = date("Y-m-d H:i:s");
			
			$password = $this->request->getPost('user_password');
			$datane['user_password'] = "";
			if ($password!="") {
				$datane['user_password'] = $this->security->hash($password);
			}
			
			$datane['user_roles'] = $this->request->getPost('user_roles');

			$form = new UsersForm();

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
				$user = new Users();
      	$user->assign($datane);
      	$cmd = $user->save();
	
	      if (!$cmd) {
		    	$msg = "";
					foreach ($user->getMessages() as $message) {
				    $msg .= $message . "<br/>";
					}
					if ($this->request->isAjax() == true) {
				    $return = array("status" => $success, "msg" => $msg);
					} else {
		        $this->flashSession->error($msg);
			  	}
				} else {
	    		$success = "1";
	        $user_id = $user->user_id;
					if ($this->request->isAjax() == true) {
		      	$msg = "New user `" . $datane['user_username'] . "` was created successfully";
			    	$return = array("status" => $success, "msg" => $msg, "user_id" => $user_id);
					} else {
		        $this->flashSession->success("New user `" . $datane['user_username'] . "` was created successfully");
					}
				}
			}
			if ($this->request->isAjax() == true) {
				echo json_encode($return);
				return;
			} else {
				if ($success == 0) {
		    	$this->session->set('create_user_data', $this->request->getPost());
				}
				return $this->response->redirect(ADMIN_ROUTE . "/users/createUser");
			}
	  }
 	 	$this->getAllUserStats();
		$entity = null;
		if ($this->session->has("create_user_data")) {
			$entity = new Users;
			$entity->assign($this->session->get("create_user_data"));
			$this->session->remove("create_user_data");
		}
		$this->view->roles = $this->getRoles();
		$this->view->default_role = $this->getDefaultRoleId();
    $this->view->form = new UsersForm($entity, "add");
  }

  public function updateUserAction()
  {
		$this->view->disable();
	  if ($this->request->isPost()) {
	  	$success = 0;
			$user_id = $this->request->getPost('user_id');
			$user = Users::findFirst("user_id = '" . $user_id . "'");
			if (!$user) {
		    $msg = "User was not found";
				if ($this->request->isAjax() == true) {
			    $return = array("status" => $success, "msg" => $msg);
				} else {
	        $this->flashSession->error($msg);
		  	}
			} else {
				$form = new UsersForm($user, array('purpose' => "edit", 'datane' => $this->request->getPost()));
				if (!$form->isValid($this->request->getPost())) {
					$msg = "";
			    foreach ($form->getMessages() as $message) {
						if ($this->request->isAjax() == true) {
							$msg .= $message . "<br/>";
						} else {
							$this->flash->error($message);
						}
			    }
	        if ($this->request->isAjax() == true) {
						$return = array("status" => "0", "msg" => $msg);
					}
				} else {
					$datane['user_firstname'] = $this->request->getPost('user_firstname', 'striptags');
					$datane['user_lastname'] = $this->request->getPost('user_lastname', 'striptags');
					$datane['user_email'] = $this->request->getPost('user_email', 'email');
					$datane['user_username'] = $this->request->getPost('user_username', 'striptags');

					$password = $this->request->getPost('user_password');
					$confirm_pass = $this->request->getPost('confirm_pass');
					
					if (!($password == "" && $confirm_pass == "")) {
						$datane['user_password'] = $this->security->hash($password);
					}
					
					$datane['user_roles'] = $this->request->getPost('user_roles');
					$datane['user_status'] = $this->request->getPost('user_status', 'striptags');


	      	$user->assign($datane);
					$cmd = $user->update();
		
		      if (!$cmd) {
			    	$success = "0";
			    	$msg = "";
						foreach ($user->getMessages() as $message) {
					    $msg .= $message . "<br/>";
						}
						if ($this->request->isAjax() == true) {
					    $return = array("status" => $success, "msg" => $msg);
						} else {
			        $this->flashSession->error($msg);
				  	}
					} else {
						$success = "1";						
		        $user_id = $user->user_id;
		        Tag::resetInput();
						if ($this->request->isAjax() == true) {
			      	$msg = "User was updated successfully";
				    	$return = array("status" => $success, "msg" => $msg, "user_id" => $user_id);
						} else {
			        $this->flashSession->success("User was updated successfully");
						}
					}
				}
			}
			if ($this->request->isAjax() == true) {
				echo json_encode($return);
			} else {
				if ($success == 0) {
		    	$this->session->set('update_user_data', $this->request->getPost());
				}
				return $this->response->redirect(ADMIN_ROUTE . "/users/edit");
			}
	  }
  }


  /**
   * Deletes a User
   *
   * @param int $id
   */
  public function deleteUsersAction($user_id="")
  {
		$this->view->disable();
  	$ajax = $this->request->isAjax();
		$success = 0;
		if ($this->request->isPost()) {
			$user_id = $this->request->getPost('user_id');
		}
		if (is_array($user_id)) {
			$user_id = implode(",", $user_id);
		}
		
		// convert to array and check if system default user ids included.
		$isSystem = false;
		$aUserIds = explode(",", $user_id);
		foreach ($aUserIds as $UserId) {
			if ($UserId<2) {
				$isSystem = true;
				break;
			}
		}
		if ($isSystem) {
			$msg = "Sorry, we can't delete the default Super Administrator.";
		} else {
	    $user = Users::find("user_id IN ($user_id)");
			if ($user->delete() == false) {
				$msg = "Sorry, we can't delete the user(s) right now: \n";
				foreach ($user->getMessages() as $message) {
					$msg .= $message . "\n";
				}
			} else {
				$success = "1";
				$msg = "The user(s) was deleted successfully!";
			}
		}
		if ($ajax == true) {
			$return = array("status" => $success, "msg" => $msg);
			echo json_encode($return);
		} else {
			$this->flashSession->warning($msg);
			return $return;
		}
  }

}