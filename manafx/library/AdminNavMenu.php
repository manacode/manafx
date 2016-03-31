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

	public function addMenu($menus)
	{
		foreach ($menus as $key => $menu) {
			$parent = false;
			if (isset($menu['parent'])) {
				$parent = $menu['parent'];
			}
			if ($parent==false) {	
				$this->_menus[$key] = $menu;
			} else {	
				if (!isset($this->_menus[$parent])) {
					$this->_menus[$parent] = array('caption' => $parent);
				}
				$this->_menus[$parent]['submenus'][$key] = $menu;
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
		$this->_menus = $this->getAdminMenu();
		unset($this->_menus['_users']);
		unset($this->_menus['_appearance']);
	}
	
	function initNavbarRightMenu()
	{
		$this->_menus = array(
	    '_users' => array(
	      'caption' => $this->t->_('Hi', 'Howdy') . ', <b>' . $this->auth['user_username'] . '</b>',
	      'action' => 'javascript:void(0)',
	      'roles' => array(),
	      'submenus' => array(
	      	'profile' => array(
	      		'caption' => $this->t->_('My Profile'),
	      		'action' => $this->adminUri . '/profile',
	      		'roles' => array(),
	      		'beforeCaption' => '<span class="glyphicon glyphicon-education"></span>',
	      	),
	      	'profile_change_password' => array(
	      		'caption' => $this->t->_('Change Password'),
	      		'action' => $this->adminUri . '/profile/change-password',
	      		'roles' => array(),
	      		'beforeCaption' => '<span class="glyphicon glyphicon-asterisk"></span>',
	      	),
	      	'logout' => array(
	    			'caption' => $this->t->_('Log Out'),
	    			'action' => $this->adminUri . '/logout',
	    			'roles' => array(),
	    			'beforeCaption' => '<span class="glyphicon glyphicon-off"></span>',
	      		'divider' => true,
	      	),
	      ),
	    ),
	    '_help' => array(
	        'caption' => $this->t->_('Help'),
	        'action' => 'javascript:void(0)',
	        'roles' => array(),
	    ),
		);
	}
	
	
	function initSidebarMenu()
	{
		$this->_menus = $this->getAdminMenu();
	}

	protected function getAdminMenu()
	{
	  return array(
		  '_users' => array(
		    'caption' => $this->t->_('Users'),
		    'roles' => array(),
		    'beforeCaption' => '<span class="glyphicon glyphicon-user"></span>',
		    'submenus' => array(
		    	'users_subheader' => array(
		    		'caption' => $this->t->_('Users Manager'),
		    		'action' => '',
		    		'roles' => array(1,2),
		    		'class' => "nav-subheader",
		    	),
		    	'users_search' => array(
		    		'caption' => $this->t->_('Search Users'),
		    		'action' => $this->adminUri . '/users',
		    		'roles' => array(1,2),
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-user"></span>',
		    	),
		    	'user_create' => array(
		    		'caption' => $this->t->_('Create User'),
		    		'action' => $this->adminUri . '/users/create-user',
		    		'roles' => array(1,2),
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-plus-sign"></span>',
		    	),
		    	'roles' => array(
		  			'caption' => $this->t->_('Roles'),
		  			'action' => $this->adminUri . '/roles',
		  			'roles' => array(1,2),
		  			'divider' => true,
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-fire"></span>',
		    	),
		    	'permissions' => array(
		  			'caption' => $this->t->_('Permissions'),
		  			'action' => $this->adminUri . '/permissions',
		  			'roles' => array(1,2),
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-lock"></span>',
		    	),
		    	'profile_subheader' => array(
		    		'caption' => $this->t->_('My Profile'),
		    		'action' => '',
		    		'roles' => array(1,2),
		  			'divider' => true,
		    		'class' => "nav-subheader",
		    	),
		    	'profile' => array(
		  			'caption' => $this->t->_('Edit Profile'),
		  			'action' => $this->adminUri . '/profile',
		  			'roles' => array(),
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-education"></span>',
		    	),
		    	'profile_change_password' => array(
		  			'caption' => $this->t->_('Change Password'),
		  			'action' => $this->adminUri . '/profile/change-password',
		  			'roles' => array(),
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-asterisk"></span>',
		    	),
		    ),
		  ),
		  '_appearance' => array(
		    'caption' => $this->t->_('Appearance'),
		    'roles' => array(1,2),
		    'beforeCaption' => '<span class="glyphicon glyphicon-eye-open"></span>',
		    'submenus' => array(
		    	'themes' => array(
		    		'caption' => $this->t->_('Themes'),
		    		'action' => $this->adminUri . '/themes',
		    		'roles' => array(1,2),
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-phone"></span>',
		    	),
		    	'menus' => array(
		  			'caption' => $this->t->_('Menus'),
		  			'action' => $this->adminUri . '/menus',
		  			'roles' => array(1,2),
		  			'divider' => true,
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-list"></span>',
		    	),
		    ),
		  ),
		  '_settings' => array(
		    'caption' => $this->t->_('Settings'),
		    'roles' => array(1,2),
		    'beforeCaption' => '<span class="glyphicon glyphicon-cog"></span>',
		    'submenus' => array(
		    	'general' => array(
		    		'caption' => $this->t->_('General'),
		    		'action' => $this->adminUri . '/settings/general',
		    		'roles' => array(1,2),
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-cog"></span>',
		    	),
		    	'modules' => array(
		  			'caption' => $this->t->_('Modules'),
		  			'action' => $this->adminUri . '/settings/modules',
		  			'roles' => array(1,2),
		  			'divider' => false,
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-paperclip"></span>',
		    	),
		    	'languages' => array(
		  			'caption' => $this->t->_('Languages'),
		  			'action' => $this->adminUri . '/languages',
		  			'roles' => array(1,2),
		  			'divider' => false,
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-globe"></span>',
		    	),
		    	'options' => array(
		  			'caption' => $this->t->_('All Options'),
		  			'action' => $this->adminUri . '/options',
		  			'roles' => array(1),
		  			'divider' => true,
		    		'class' => "",
		    		'beforeCaption' => '<span class="glyphicon glyphicon-record"></span>',
		    	),
		    ),
		  ),
		);
	}
}