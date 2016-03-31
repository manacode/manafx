<?php
use Manafx\Models\Options;


class ManafxController extends \Phalcon\Mvc\Controller
{
	var $is_backend = false;
	var $class_name = "";
	var $module_name = "System";
	var $controller_name = "";
	var $action_name = "";
	var $current_template = '';
	var $templateUri = '';
	var $current_theme = '';
	var $auth_i = array();
	var $templateBefore = array();
	var $adminurl;

	function initialize() {
		$this->loadOptions();
		$this->setConfig();
		$this->view->headerfooter = new ManafxHeaderFooter($this->di);
		$this->assets->collection('header-dynamic');
		$this->class_name = get_class($this);
		if ($this->router->getModuleName()!="") {
			$this->module_name = $this->router->getModuleName();
		}

		$this->controller_name = $this->router->getControllerName();
		$this->action_name =  $this->router->getActionName();
		$this->current_template = $this->config->application->template;
		$this->templateUri = $this->config->application->baseUrl . '/templates/' . $this->config->application->template;
		$this->current_theme = $this->config->application->theme;
		$this->adminurl = $this->config->application->baseUrl . '/' . ADMIN_ROUTE;
		$this->view->adminurl = $this->adminurl;
		$this->view->current_template = $this->current_template;
		$this->view->templateUri = $this->templateUri;
		$this->view->current_theme = $this->current_theme;
		$this->view->setVar("module_name", $this->module_name);
		$this->view->setVar("controller_name", $this->controller_name);
		$this->view->setVar("action_name", $this->action_name);

		if ($this->session->has("auth-i")) {
			$this->auth_i = $this->session->get('auth-i');
		}
		$this->initHooks();
		$this->view->setTemplateBefore('frontend');

		$this->view->getOffline = $this->getOffline();
		$this->onView();
	}

 	private function onView() {
    $eventsManager = $this->getDI()->getShared('eventsManager');
    $eventsManager->attach("view", function ($event, $view, $file) {
			if ($event->getType() == 'beforeRender') {
				$this->view->is_backend = $this->is_backend;
				if (is_file('templates/' . $this->current_template . '/themes/' . $this->current_theme . '/' . $this->module_name . '/' . $this->module_name . '.css')) {
					$this->assets->addCss($this->templateUri . '/themes/' . $this->current_theme . '/' . $this->module_name . '/' . $this->module_name . '.css');
				}
				if (is_file('templates/' . $this->current_template . '/themes/' . $this->current_theme . '/' . $this->module_name . '/' . $this->controller_name . '.css')) {
					$this->assets->addCss($this->templateUri . '/themes/' . $this->current_theme . '/' . $this->module_name . '/' . $this->controller_name . '.css');
				}
				$this->assets->collection('footer-dynamic');
				if (is_file('templates/' . $this->current_template . '/js/' . $this->module_name . '/' . $this->controller_name . '.js')) {
					$this->assets->collection('footer-dynamic')->addJs($this->templateUri . '/js/' . $this->module_name . '/' . $this->controller_name . '.js');
				}
				if (is_file('templates/' . $this->current_template . '/js/' . $this->module_name . '/' . $this->controller_name . '-' . $this->action_name . '.js')) {
					$this->assets->collection('footer-dynamic')->addJs($this->templateUri . '/js/' . $this->module_name . '/' . $this->controller_name . '-' . $this->action_name . '.js');
				}
			}
			if ($event->getType() == 'afterRender') {
				$this->view->setContent($this->view->getContent() . $this->view->getOffline);
				if ($this->config->system->profiler_mode=="on") {
					$this->view->setContent($this->view->getContent() . $this->profiler->output());
				}
			}
    });
    $this->view->setEventsManager($eventsManager);
	}

	function getOffline() {
		$offline_mode = "";
		if ($this->config->system->maintenance_mode=="on") {
			
			$offline_message_options = getOptions(array("offline_message_mode", "offline_message_backend", "offline_message_frontend"));
			
			$offline_message_mode = $offline_message_options["offline_message_mode"];
			$offline_message = "";
			if ($this->auth->is_admin()) {
				$offline_mode = "offline-backend";
			} else {
				if ($this->session->has("auth-i")) {
					$offline_mode = "offline-frontend";
				}	else {
					if ($this->is_backend) {
						$offline_mode = "offline-backend";
					}
				}
			}
			if ($offline_mode == "offline-backend") {
				if ($offline_message_mode == "custom") {
					$offline_message = $offline_message_options["offline_message_backend"];
				}
				$this->view->start();
				$this->view->partial('/../manafx/partials/offline-alert', array(
			    'message'	=> $offline_message,
				));
		
				$html = ob_get_contents();
				$this->view->finish();
				return $html;
			} else {
				if ($offline_message_mode == "custom") {
					$offline_message = $offline_message_options["offline_message_frontend"];
				}
				$this->view->message = $offline_message;
				$this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
				$this->view->render("offline", "index");
				$this->view->disable();
				return;
			}
		}
		return "";
	}

	function redirect2base() {
		$this->response->redirect($this->siteurl);
	}

	function loadOptions() {
		$filter = "option_autoload = 'Y' AND (option_identity = 'public' OR option_identity = '" . $this->module_name . "') ";
		$options = Options::find(array(
			'conditions' => $filter,
			'columns' => array("option_name", "option_value", "option_identity")
		));
		$config = array();
		foreach ($options as $option) {
			$identity = $option->option_identity;
			$opname = $option->option_name;
			$config[$identity][$opname] = $option->option_value;
		}
		$config = new Phalcon\Config($config);
		$this->config->merge($config);
	}
	
	function setConfig() {
		date_default_timezone_set($this->config->application->timezone_identifier);
		$this->setLanguage();
	}
	
	function scan_Modules($dir="") {
		if ($dir=="" || (!is_dir($dir))) {
			$dir = APP_PATH . '/modules';
		}
		$modules = array();
		if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
        if (is_dir($dir . "/" . $entry) && $entry != "." && $entry != "..") {
          $modules[] = $entry;
        }
	    }
	    closedir($handle);
		}
		return $modules;
	}

	function initHooks() {
		$functions = array(
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
	
	// After route executed event
	public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
	  if($this->request->isAjax() == true) {
			$this->response->setContentType('application/json', 'UTF-8');
			$this->view->disable();
	  }
	}

	function setLanguage() {
		$default_lang = "en-US";
	  // Ask browser what is the best language
	  $lang = $this->request->getBestLanguage();
	  $lang_dir = CORE_PATH . 'languages/' . $lang;
		if (!is_dir($lang_dir)) {
			if (isset($this->config->application->default_language)) {
				$lang = $this->config->application->default_language;
			}
			$lang_dir = CORE_PATH . 'languages/' . $lang;
			if (!is_dir($lang_dir)) {
				$lang = $default_lang;
			}
		}
		$this->config->application->language = $lang;
	}
}