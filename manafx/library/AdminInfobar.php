<?php

/**
 * Admin Information bar
 *
 * Helps to build Admin Infobar for the application
 */
class AdminInfobar extends Phalcon\Mvc\User\Component
{
	private $auth = array();
  private $_infobar = array();

	function __construct()
	{
		$auth = $this->session->has('auth-i');
		if ($auth){
			$this->auth = $this->session->get('auth-i');
		}
	}

	public function addInfobar($info)
	{
		foreach ($info as $key => $value) {
			$this->_infobar[$key] = $value;
		}
	}
	
	function checkPermission($user_roles, $info_roles)
	{
		$allowed = false;
		if (!empty($info_roles)) {
			foreach ($info_roles as $mRole) {
				if (is_numeric($mRole)) {
					foreach ($user_roles as $role) {
						$role_id = $role['role_id'];
						if ($role_id == 1 || ($role_id == $mRole)) {
							$allowed = true;
							break 2;
						}
					}
				} else {
					foreach ($user_roles as $role) {
						$role_name = $role['role_name'];
						if ($role_name == "Super Administrator" || ($role_name == $mRole)) {
							$allowed = true;
							break 2;
						}
					}
				}
			}
		} else {
			$allowed = true;
		}
		return $allowed;
	}

	public function getInfobar()
	{
		$_infobar = $this->_infobar;
		$auth = $this->session->has('auth-i');
		if (!$auth){
			// $roles = array(array('role_id' => 10, 'role_name' => 'Guests'));
			return false;
		} else {
			$roles = $this->auth['user_roles'];
		}
		
		foreach ($_infobar as $info_key => $info_items) {
			foreach($info_items as $item_key => $item_value) {
				$item_roles = array();
				if (isset($item_value['roles'])) {
					$item_roles = $item_value['roles'];
				}
				$allowed = $this->checkPermission($roles, $item_roles);
	
				if (!$allowed) {
					unset($_infobar[$info_key][$item_key]);
					continue;
				}
				
			}
		}
		$infobar = array();
		foreach ($_infobar as $key => $value) {
			$infobar[] = $value['html'];
		}
		
		return $infobar;
	}
}