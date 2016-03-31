<?php
class ManafxAdminController extends \ManafxController
{
	var $is_navbar = true;
	var $is_sidebar = true;
	var $is_infobar = true;
	var $adminNavbarLeftMenu;
	var $adminNavbarRightMenu;
	var $adminSidebarMenu;
	var $adminInfobar = "";
	
	function onConstruct() {
		$this->is_backend = true;
	}

	function initialize() {
		parent::initialize();

		$this->view->setTemplateBefore('backend');

		$this->adminNavbarLeftMenu = new AdminNavMenu('navbar-left');
		$this->adminNavbarRightMenu = new AdminNavMenu('navbar-right');
		$this->adminSidebarMenu = new AdminNavMenu('sidebar');
		$this->adminInfobar = new AdminInfobar();
		$this->initAdminHooks();
		$this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
		$this->di['gridToolbar'] = new \ManafxGridToolbar();
		$this->onView();
	}

 	private function onView() {
    $eventsManager = $this->getDI()->getShared('eventsManager');
    $eventsManager->attach("view", function ($event, $view, $file) {
			if ($event->getType() == 'beforeRender') {
				$this->view->is_navbar = $this->is_navbar;
				$this->view->is_sidebar = $this->is_sidebar;
				$this->view->is_infobar = $this->is_infobar;
	    	$this->view->adminNavbarLeftMenu = $this->adminNavbarLeftMenu->getMenu();
	    	$this->view->adminNavbarRightMenu = $this->adminNavbarRightMenu->getMenu();
	    	$this->view->adminSidebarMenu = $this->adminSidebarMenu->getMenu();
	    	$this->view->adminInfobar = $this->adminInfobar->getInfobar();
			}
			if ($event->getType() == 'afterRender') {
			}
    });
    $this->view->setEventsManager($eventsManager);
	}

	function initAdminHooks() {
		$functions = array(
			'_adminNavbarLeftMenu' => array($this),
			'_adminNavbarRightMenu' => array($this),
			'_adminSidebarMenu' => array($this),
			'_adminInfobar' => array($this),
		);
		foreach ($functions as $function => $params) {
			foreach($this->config->application->active_modules as $active_module) {
				$func = $active_module . $function;
				if (function_exists($func)) {
					call_user_func_array($func, $params);
				}
			}
		}
	}
	
	protected function updateGlobalConfigVar($identity = "", $key, $val, $addIfNotFound=false) {
		global $g_config;
		if ($identity=="") {
			if (!$addIfNotFound) {
				if (!isset($g_config[$key])) {
					return false;
				}
			}
			$g_config[$key] = $val;
			$this->config->$key = $val;
		} else {
			if (!$addIfNotFound) {
				if (!isset($g_config[$identity][$key])) {
					return false;
				}
			}
			$g_config[$identity][$key] = $val;
			$this->config->$identity->$key = $val;
		}
	}
	
	protected function updateGlobalConfigFile() {
		global $g_config;
		return file_put_contents(APP_PATH . '/config/config.php', '<?php return ' . var_export($g_config, true) . ';');
	}
	
}