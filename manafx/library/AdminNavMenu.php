<?php

/**
 * Admin Nav Menu
 *
 * Helps to build Administration Navigation Menus for the application
 */
class AdminNavMenu extends Phalcon\Mvc\User\Component
{
	var $adminUri;
	var $defaultTags = array("navbar-left", "navbar-right", "sidebar");
	var $tag;
	var $_menus = array();
  var $_activeMenu = array();

	private $auth = array();

	function __construct($tag)
	{
		$this->adminUri = "/" . ADMIN_ROUTE;
		$this->tag = $tag;
		$auth = $this->session->has('auth-i');
		if ($auth){
			$this->auth = $this->session->get('auth-i');
			$this->initMenus();
		}
	}
	
	public function setActive($active=array())
	{
		$this->_activeMenu = $active;
	}

	public function addMenu($menus, $after = null)
	{
		$afterKey = $after;
		foreach ($menus as $key => $menu) {
			$parent = false;
			if (isset($menu['parent'])) {
				$parent = $menu['parent'];
			}
			if ($parent==false) {
				// $this->_menus[$key] = $menu;
				$this->_menus = array_insert_after($this->_menus, $afterKey, array($key => $menu));
			} else {
				if (!isset($this->_menus[$parent])) {
					// $this->_menus[$parent] = array('caption' => $parent, 'submenus' => array());
					$this->_menus = array_insert_after($this->_menus, $afterKey, array($parent => array('caption' => $parent, 'submenus' => array())));
					// $afterKey = null;
				}
				// if (isset($this->_menus[$parent]['submenus'][$after])) {
				if (array_key_exists($after, $this->_menus[$parent]['submenus'])) {
					$afterKey = $after;
				} else {
					$afterKey = null;
				}
				// $this->_menus[$parent]['submenus'][$key] = $menu;
				$this->_menus[$parent]['submenus']= array_insert_after($this->_menus[$parent]['submenus'], $afterKey, array($key => $menu));
				
			}
		}
	}

	function checkPermission($user_roles, $menu_roles)
	{
		$allowed = false;
		if (!empty($menu_roles)) {
			foreach ($menu_roles as $mRole) {
				if ($mRole=="*") {
					$allowed = true;
					break;
				}
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


	function roles_filter()
	{
		$_menus = $this->_menus;
		
		$roles = $this->auth['user_roles'];
		
		foreach($_menus as $menu_key => $menu) {
			$menu_roles = array();
			if (isset($menu['roles'])) {
				$menu_roles = $menu['roles'];
			}
			$allowed = $this->checkPermission($roles, $menu_roles);

			if (!$allowed) {
				unset($this->_menus[$menu_key]);
				continue;
			}
			
			if (isset($menu['submenus'])) {
				$submenus = $menu['submenus'];
				foreach ($submenus as $submenu_key => $submenu) {
					$submenu_roles = array();
					if (isset($submenu['roles'])) {
						$submenu_roles = $submenu['roles'];
					}
					$allowed = $this->checkPermission($roles, $submenu_roles);
					if (!$allowed) {
						unset($this->_menus[$menu_key]['submenus'][$submenu_key]);
					}
				}
			}
		}
	}

	public function getMenu()
	{

		$auth = $this->session->has('auth-i');
		if (!$auth){
			return false;
		}
		$this->roles_filter();
		
		$active = $this->_activeMenu;
		
		if (!empty($active)) {
			foreach ($this->_menus as $menu_key => $menu) {
				if ($menu_key == $active[0]) {
					$this->_menus[$menu_key]['active'] = true;
					if (isset($menu['submenus'])) {
						foreach ($menu['submenus'] as $submenu_key => $submenu) {
							if ($submenu_key == $active[1]) {
								$this->_menus[$menu_key]['submenus'][$submenu_key]['active'] = true;
								break;
							}
						}
					}
					break;
				}
			}
		} else {
			if ($active=="" || empty($active)) {
				$active = $_SERVER['REQUEST_URI'];
			}

			foreach ($this->_menus as $menu_key => $menu) {
				$action = isset($menu['action']) ? $menu['action'] : "";
				$pos = stripos($active, $action);
				if ($pos !== false) {
					$this->_menus[$menu_key]['active'] = true;
				} else {
					if (isset($menu['submenus'])) {
						foreach ($menu['submenus'] as $submenu_key => $submenu) {
							$sub_action = isset($submenu['action']) ? $submenu['action'] : "";
							$sub_pos = stripos($active, $sub_action);
							if ($sub_pos !== false) {
								$this->_menus[$menu_key]['active'] = true;
								$this->_menus[$menu_key]['submenus'][$submenu_key]['active'] = true;
								break 2;
							}
						}
					}
				}
			}
		}
		return $this->_menus;
	}

	private function initMenus()
	{
		$tag = $this->tag;
		if ($tag == 'navbar-left') $this->initNavbarLeftMenu();
		if ($tag == 'navbar-right') $this->initNavbarRightMenu();
		if ($tag == 'sidebar') $this->initSidebarMenu();
	}

	function initNavbarLeftMenu()
	{
		$this->_menus = include CORE_PATH . "/config/menu-navbar-left.php";
	}
	
	function initNavbarRightMenu()
	{
		$this->_menus = include CORE_PATH . "/config/menu-navbar-right.php";
	}
	
	
	function initSidebarMenu()
	{
		$this->_menus = include CORE_PATH . "/config/menu-sidebar.php";
	}
}