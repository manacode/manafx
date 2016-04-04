<?php
use Phalcon\Mvc\User\Component;
use Manafx\Models\Users;
use Manafx\Models\User_Roles;
use Manafx\Models\User_Agents as Agents;
use Manafx\Models\Permissions;

/**
 * Manages Authentication / Identity Management
 */
class Auth extends Component
{
	protected $ced_actions = array();
	var $create = false;
	var $edit = false;
	var $delete = false;

  /**
   * Checks username and password
   *
   * @param string $username, $password
   * @return boolean
   */
  public function check($username, $password)
  {
    if (filter_var($username, FILTER_VALIDATE_INT)) {
    	$user = Users::findFirst("user_id = " . $username . "");
  	} elseif (filter_var($username, FILTER_VALIDATE_EMAIL)) {
			$user = Users::findFirst("user_email = '" . $username . "'");
    } else {
    	$user = Users::findFirst("user_username = '" . $username . "'");
  	}
    if ($user == false) {
			return false;
    }
    // Check the password
    if (!$this->security->checkHash($password, $user->user_password)) {
			return false;
    }
  	return true;
 	}

  /**
   * Auth process
   *
   * @param array $credentials
   * @return int
   */
  public function login($credentials)
	{
    // Check if the user exist
    $username = $credentials['username'];
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    	$user = Users::findFirst("user_username = '" . $username . "'");
  	} else {
	    $user = Users::findFirst("user_email = '" . $username . "'");
    }
    if ($user == false) {
      $this->registerUserThrottling(0);
      throw new Exception($this->t->_('Wrong Username', 'Wrong username/password combination'));
    }

    // Check the password
    if (!$this->security->checkHash($credentials['password'], $user->user_password)) {
      $this->registerUserThrottling($user->user_id);
      throw new Exception($this->t->_('Wrong Password', 'Wrong username/password combination'));
    }

    // Check if the user was flagged
    $this->checkUserFlags($user);

    // Register the successful login
    $this->saveSuccessLogin($user);

    // Check if the remember me was selected
    if (isset($credentials['remember'])) {
      $this->createRememberEnviroment($user);
    }
    $roles = $this->roles2assoc($user->user_roles);

    $this->session->set('auth-i', array(
        'user_id' => $user->user_id,
        'user_username' => $user->user_username,
        'user_email' => $user->user_email,
        'user_roles' => $roles
    ));
    
		return $user->user_id;
  }
  
  function roles2assoc($s)
	{
  	$arrs = CSVExplode($s);
  	$user_roles = User_Roles::find(array("columns" => "role_id, role_name"));
  	$userRoles = array();
  	foreach ($user_roles as $user_role) {
  		$userRoles[$user_role->role_id] = $user_role->role_name;
 		}
  	
  	$roles = array();
  	foreach ($arrs as $arr) {
  		$roles[] = array(
  			"role_id" => $arr,
  			"role_name" => $userRoles[$arr]
  		);
 		}
 		return $roles;
 	}

  /**
   * Change user password
   *
   * @param Manafx\Models\Users $user
   */
  public function changeUserPassword($username, $old_password, $new_password)
	{
		$user = $this->getUser($username, $old_password);
		if ($user) {
			$ip = $this->request->getClientAddress();
			$agent = $this->request->getUserAgent();
			$hash = $this->md5($agent);
			$ua_id = $this->getUserAgentId($hash);
			
			$user->user_password = $this->security->hash($new_password);
			
			$passwordChange = new \Manafx\Models\User_Password_Changes();
			$passwordChange->user = $user;
			$passwordChange->password_change_ip_address = $ip;
			$passwordChange->password_change_user_agent_id = $ua_id;
			
			if (!$passwordChange->save()) {
		    foreach ($passwordChange->getMessages() as $message) {
	        $this->flashSession->error($this->t->_($message));
		    }
			} else {
				$this->flashSession->success($this->t->_('password_changed_succesfully', 'Your password was successfully changed'));
			}
		} else {
			$this->flashSession->error("Current password did not match!");
		}
  }

  /**
   * Save success login client data
   *
   * @param Manafx\Models\Users $user
   */
  public function saveSuccessLogin($user)
	{
		$ip = $this->request->getClientAddress();
		$agent = $this->request->getUserAgent();
		$hash = $this->md5($agent);
		
		$ua_id = $this->getUserAgentId($hash);
		
    $successLogin = new \Manafx\Models\User_Success_Logins();
    $successLogin->success_login_user_id = $user->user_id;
    $successLogin->success_login_ip_address = $ip;
    $successLogin->success_login_user_agent_id = $ua_id;
    if (!$successLogin->save()) {
	    $messages = $successLogin->getMessages();
	    throw new Exception($messages[0]);
    }
  }

	function getUserAgentId($hash) {
		$ua_id = 0;
		$ua = Agents::findFirst("hash = '$hash'");
		if ($ua) {
			$ua_id = $ua->id;
		} else {
			$ua = new Agents();
			$ua->user_agent = $this->request->getUserAgent();
			$ua->hash = $hash;
			if ($ua->create() == false) {
		    $this->flashSession->error($this->t->_("saving_user_agent_failed", "Saving user agent failed!"));
		    foreach ($ua->getMessages() as $message) {
	        $this->flashSession->error($this->t->_($message));
		    }
			} else {
				$ua_id = $ua->id;
			}
		}
		return $ua_id;
	}

  /**
   * Implements login throttling
   * Reduces the efectiveness of brute force attacks
   *
   * @param int $userId
   */
  public function registerUserThrottling($userId)
	{
		$time = time();

		$ip = $this->request->getClientAddress();
		$agent = $this->request->getUserAgent();
		$hash = $this->md5($agent);
		$ua_id = $this->getUserAgentId($hash);

    $failedLogin = new \Manafx\Models\User_Failed_Logins();
    $failedLogin->failed_login_user_id = $userId;
    $failedLogin->failed_login_ip_address = $this->request->getClientAddress();
    $failedLogin->failed_login_user_agent_id = $ua_id;
    $failedLogin->failed_login_time = $time;
    $failedLogin->save();

    $attempts = \Manafx\Models\User_Failed_Logins::count(array(
      'failed_login_ip_address = ?0 AND failed_login_time >= ?1',
      'bind' => array(
          $this->request->getClientAddress(),
          $time - 3600 * 6
      )
    ));

    switch ($attempts) {
      case 1:
        // no delay
        break;
      case 2:
        sleep(2);
        break;
      case 3:
        sleep(4);
        break;
      case 4:
        sleep(6);
        break;
      default:
        sleep(8);
        break;
    }
  }

  /**
   * Creates the remember me environment settings the related cookies and generating tokens
   *
   * @param Manafx\Models\Users $user
   */
  public function createRememberEnviroment(Users $user)
	{
		$agent = $this->request->getUserAgent();
		$hash = $this->md5($agent);
		$ua_id = $this->getUserAgentId($hash);

    $token = $this->md5($user->user_email . $user->user_password . $agent);

    $remember = new \Manafx\Models\User_Remembers();
    $remember->remember_user_id = $user->user_id;
    $remember->remember_token = $token;
    $remember->remember_user_agent_id = $ua_id;

    if ($remember->save() != false) {
      $expire = time() + 86400 * 8;
      $this->cookies->set('RMU', $user->user_id, $expire);
      $this->cookies->set('RMT', $token, $expire);
    }
  }

  /**
   * Check if the session has a remember me cookie
   *
   * @return boolean
   */
  public function hasRememberMe()
	{
    return $this->cookies->has('RMU');
  }

  /**
   * Logs on using the information in the coookies
   *
   * @return Phalcon\Http\Response
   */
  public function loginWithRememberMe($redirect_to="/")
	{
    $user_id = $this->cookies->get('RMU')->getValue();
    $cookieToken = $this->cookies->get('RMT')->getValue();

    $user = Users::findFirstByuser_id($user_id);
    if ($user) {
      $agent = $this->request->getUserAgent();
      $token = $this->md5($user->user_email . $user->user_password . $agent);
      if ($cookieToken == $token) {
        $remember = \Manafx\Models\User_Remembers::findFirst(array(
            'remember_user_id = ?0 AND remember_token = ?1',
            'bind' => array(
                $user->user_id,
                $token
            )
        ));
        if ($remember) {
          // Check if the cookie has not expired
          if ((time() - (86400 * 8)) < $remember->remember_created) {

            // Check if the user was flagged
            $this->checkUserFlags($user);
            
            $roles = $this->roles2assoc($user->user_roles);
						
				    $this->session->set('auth-i', array(
				        'user_id' => $user->user_id,
				        'user_username' => $user->user_username,
				        'user_email' => $user->user_email,
				        'user_roles' => $roles
				    ));

            // Register the successful login
            $this->saveSuccessLogin($user);
            
						if ($this->session->has("redirect_to")) {
							$redirect_to = $this->session->get("redirect_to");
		        }

            return $this->response->redirect($redirect_to);
          }
        }
      }
    }

		$this->remove();
    return $this->response->redirect(ADMIN_ROUTE . "/login");
  }

  /**
   * Checks if the user is banned/locked/pending
   *
   * @param Manafx\Models\Users $user
   */
  public function checkUserFlags(Users $user)
  {
  	// A=Active, B=Banned, L=Locked, X=Activation Link
  	switch ($user->user_status) {
  		case "B":
  			throw new Exception($this->t->_('account_status_banned', 'The user is banned'));
  			break;
  		case "L":
   			throw new Exception($this->t->_('account_status_locked', 'The user is locked'));
  			break;
  		case "X":
				throw new Exception($this->t->_('account_status_pending_activation_link', 'Your account status is pending activation, please activate you account.'));   		
  			break;
		}
  }

  /**
   * Returns the current identity
   *
   * @return array
   */
  public function getIdentity($identity_name="")
  {
  	$identity = $this->session->get('auth-i');
  	if ($identity_name=="") {
      return $identity;
		} else {
			if (isset($identity[$identity_name])) {
				return $identity[$identity_name];
			} else {
				return false;
			}
		}
  }

  /**
   * Removes the user identity information from session
   */
  public function remove()
  {
    if ($this->cookies->has('RMU')) {
	    $this->cookies->get('RMU')->delete();
    }
    if ($this->cookies->has('RMT')) {
	    $this->cookies->get('RMT')->delete();
    }

    $this->session->remove('auth-i');
    $this->session->destroy(true);
  }

  /**
   * Auths the user by his/her id
   *
   * @param int $id
   */
  public function authUserById($id)
  {
    $user = Users::findFirstByuser_id($id);
    if ($user == false) {
			throw new Exception($this->t->_('user_not_exists', 'The user does not exist'));
    }
    $this->checkUserFlags($user);
		$roles = $this->roles2assoc($user->user_roles);
    $this->session->set('auth-i', array(
        'user_id' => $user->user_id,
        'user_username' => $user->user_username,
        'user_email' => $user->user_email,
        'user_roles' => $roles
    ));

  }

  /**
   * Get current user
   *
   */
  public function getCurrentUser()
  {
    $identity = $this->session->get('auth-i');
    if (isset($identity['user_id'])) {
      $user = Users::findFirstByuser_id($identity['user_id']);
      if ($user !== false) {
        return $user;
      }
    }
    return false;
  }

  /**
   * Get user data by validating username and password
   *
   * @param string $username, default value '*' = current user
   * @return Manafx\Models\Users $user or bool false
   */
  public function getUser($username="*", $password)
  {
  	if ($username == "*") {
  		$user = $this->getCurrentUser();
 		} else {
	    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
	    	$user = Users::findFirst("user_username = '" . $username . "'");
	  	} else {
		    $user = Users::findFirst("user_email = '" . $username . "'");
	    }
    }
    if ($user == false) {
    	return false;
    }
    // Check the password
    if (!$this->security->checkHash($password, $user->user_password)) {
			return false;
    }
  	return $user;
 	}

	public function is_admin($user_id = "")
	{
		$ret = false;
		if ($user_id=="") {
    	$identity = $this->session->get('auth-i');
      if (isset($identity['user_roles'])) {
				$roles = $identity['user_roles'];
				foreach ($roles as $role) {
					$role_id = $role['role_id'];
					if ($role_id==1 || $role_id==2) {
						$ret = true;
						break;
					}
				}
			}
		}
		return $ret;
	}

	public function user_has_role($has_roles)
	{
		$ret = false;
		$aroles = $has_roles;
		if (!is_array($has_roles)) {
			$aroles = array($has_roles);
		}
		
    $identity = $this->session->get('auth-i');
    if (isset($identity['user_roles'])) {
			$roles = $identity['user_roles'];
			foreach ($roles as $role) {
				foreach ($aroles as $the_role) {
					if (is_numeric($the_role)) {
						$role_id = $role['role_id'];
						if ($role_id == $the_role) {
							$ret = true;
							break 2;
						}
					} else {
						$role_name = $role['role_name'];
						if ($role_name == $the_role) {
							$ret = true;
							break 2;
						}
					}

				}
			}
		}
		return $ret;
	}

	public function getRoles()
	{
		$roles_model = new User_Roles;
		$roles = $roles_model::find(array(
			'columns' => 'role_id, role_name',
			'conditions' => 'role_status != "X" AND role_status != "R" AND role_id != 1',
		));
		return $roles;
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

/**
 * CED (Create Edit Delete) AUTH
 **/
 

	function ced_get_action($is) {
		if (isset($this->ced_actions[$is])) {
			return $this->ced_actions[$is];
		}
		return false;
	}

	function ced($ced_actions=array()) {
		$user_roles = $this->getIdentity('user_roles');
		if (!$user_roles) {
			return;
		}
		if (!empty($ced_actions)) {
			$this->ced_actions = $ced_actions;
		}

		$module_name = "manafx";
		if ($this->router->getModuleName()!="") {
			$module_name = $this->router->getModuleName();
		}
		$controller_name = $this->router->getControllerName();
		$action_name = $this->router->getActionName();
		
		/* get Create permission */
		$ga = $this->ced_get_action('create');
		if ($ga) {
			$is_condition = "permission_action = '$ga'";
		} else {
			$is_condition = "permission_action LIKE 'Create%'";
		}
		foreach ($user_roles as $role) {
			$role_id = $role['role_id'];
			if ($role_id == "1") {
				$this->create = true;
				break;
			}
			$condition = "permission_role_id = '" . $role_id . "' AND permission_module = '" . $module_name . "' AND permission_controller = '" . $controller_name . "' AND " . $is_condition;
			$found = Permissions::findFirst($condition);
		  if ($found) {
		  	$this->create = true;
		  	break;
	  	}
		}

		/* get Edit permission */
		$ga = $this->ced_get_action('edit');
		if ($ga) {
			$is_condition = "permission_action = '$ga'";
		} else {
			$is_condition = "(permission_action LIKE 'Edit%' OR permission_action LIKE 'Update%' OR permission_action LIKE 'Manage%')"; 
		}
		foreach ($user_roles as $role) {
			$role_id = $role['role_id'];
			if ($role_id == "1") {
				$this->edit = true;
				break;
			}
			$condition = "permission_role_id = '" . $role_id . "' AND permission_module = '" . $module_name . "' AND permission_controller = '" . $controller_name . "' AND " . $is_condition;
			$found = Permissions::findFirst($condition);
		  if ($found) {
		  	$this->edit = true;
		  	break;
	  	}
		}

		/* get Delete permission */
		$ga = $this->ced_get_action('delete');
		if ($ga) {
			$is_condition = "permission_action = '$ga'";
		} else {
			$is_condition = "permission_action LIKE 'Delete%'"; 
		}
		foreach ($user_roles as $role) {
			$role_id = $role['role_id'];
			if ($role_id == "1") {
				$this->delete = true;
				break;
			}
			$condition = "permission_role_id = '" . $role_id . "' AND permission_module = '" . $module_name . "' AND permission_controller = '" . $controller_name . "' AND " . $is_condition;
			$found = Permissions::findFirst($condition);
		  if ($found) {
		  	$this->delete = true;
		  	break;
	  	}
		}
	}

	function md5($s) {
		$len = substr("00000000" . strlen($s), -8);
		$ins = "{{" . $len . "}}";
		return md5($s . $ins);
	}

	function base62_encode ($data) {
		$outstring = '';
		$len = strlen($data);
		for ($i = 0; $i < $len; $i += 8) {
			$chunk = substr($data, $i, 8);
			$outlen = ceil((strlen($chunk) * 8) / 6);
			$x = bin2hex($chunk);
			$number = ltrim($x, '0');
			if ($number === '') $number = '0';
			$w = gmp_strval(gmp_init($number, 16), 62);
			$pad = str_pad($w, $outlen, '0', STR_PAD_LEFT);
			$outstring .= $pad;
		}
		return $outstring;
	}	
	
	function base62_decode ($data) {
		$outstring = '';
		$len = strlen($data);
		for ($i = 0; $i < $len; $i += 11) {
			$chunk = substr($data, $i, 11);
			$outlen = floor((strlen($chunk) * 6) / 8);
			$number = ltrim($chunk, '0');
			if ($number === '') $number = '0';
			$y = gmp_strval(gmp_init($number, 62), 16);
			$pad = str_pad($y, $outlen * 2, '0', STR_PAD_LEFT);
			$outstring .= pack('H*', $pad);
		}
		return $outstring;
	}
}