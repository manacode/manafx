<?php
$g_config = include "../config/config.php";
include("version.php");
include("functions.php");

/**
 * Composer autoloading
 */
if (file_exists(VENDOR_PATH . 'autoload.php')) {
	$vendor = include VENDOR_PATH . 'autoload.php';
}

/**
 * Register registry
 */
$registry = new \Phalcon\Registry();

/**
 * Register Loader
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs(array(
	CORE_PATH,
	CORE_PATH . "/helpers/",
	CORE_PATH . "/library/",
));

$loader->registerNamespaces(array(
	'Manafx' => __DIR__ . "/",
  'Manafx\Controllers' => __DIR__ . "/controllers/",
  'Manafx\Models' => __DIR__ . "/models/",
  'Manafx\Forms' => __DIR__ . "/forms/",
));
$loader->register();

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new \Phalcon\DI\FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', new \Phalcon\Config($g_config));

/**
 * Loading routes from the routes.php file
 */

$di->set('router', function() use ($g_config) {
	return include APP_PATH . "/config/routes.php";
}, true);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function() {
  $url = new \Phalcon\Mvc\Url();
  $url->setBaseUri(BASE_URI);
  return $url;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$adapter = '\Phalcon\Db\Adapter\Pdo\\' . $g_config['database']['adapter'];
$connection = new $adapter(array(
	"host" => $g_config['database']['host'],
	"username" => $g_config['database']['username'],
	"password" => $g_config['database']['password'],
	"dbname" => $g_config['database']['dbname'],
	"tableprefix" => $g_config['database']['tableprefix'],
));
$di->set('db', $connection);

/**
 * Register Model Manager
 */
$di->set('modelsManager', function() {
	return new \Phalcon\Mvc\Model\Manager();
});

/**
 * Debuging
 */
if ($g_config['system']['debug_mode']=="on") {
	error_reporting(E_ALL);
	// load Whoops error handler
	new Whoops\Provider\Phalcon\WhoopsServiceProvider($di);
} else {
	error_reporting(0);
}

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function(){
	$session = new Phalcon\Session\Adapter\Files(array('uniqueId' => 'manafx'));
	$session->start();
	return $session;
});

/**
 * Register ManafxTranslator
 */
$di->set('t', function() {
	return new ManafxTranslator();
});

/**
 * Register DateTime
 */
$di->set('dt', function() use ($g_config) {
	$df = $g_config['application']['date_format'];
	$tf = $g_config['application']['time_format'];
	$tz = $g_config['application']['timezone_identifier'];
	return new \Manacode\Helpers\DateTime(array("dateFormat"=>$df, "timeFormat"=>$tf, "timeZone"=>$tz));
});

/**
 * Register the flash service with custom CSS classes
 */
$di->set('flash', function(){
  return new ManafxFlashDirect();
});
$di->set('flashSession', function(){
  return new ManafxFlashSession();
});

/**
 * Register ManafxProfiler
 */
if ($g_config['system']['profiler_mode']=="on") {
	$di->set('profiler', new ManafxProfiler);
	$di['profiler']->setEventDB('db', "bootstrap");
}

/**
 * Register ManafxResponse
 */
$di->set('response', function(){
  return new ManafxResponse();
});

$reqUri = $_SERVER["REQUEST_URI"];
if ($g_config['system']['frontpage_mode'] == "redirect_to") {
	$redirect_to = $g_config['system']['redirect_to'];
	if ($reqUri == "/" || $reqUri == "/index.php") {
		$di['response']->redirect($redirect_to);
	}
}

/**
 * Register dispatcher
 */
$di->set('dispatcher', function() use ($di) {
	// Create an EventsManager
	$eventsManager = new Phalcon\Events\Manager();
  $eventsManager->attach("dispatch", function($event, $dispatcher, $exception) use ($di) {
		if ($event->getType() == 'beforeDispatchLoop') {
	   	$action_name = $dispatcher->getActionName();
			// Remove extension
	    $action_name = preg_replace('/\.html$/', '', $action_name);
	  	if (preg_match('/[-_]/', $action_name)==1 ) {
	      $dispatcher->setActionName(\Phalcon\Text::camelize($dispatcher->getActionName()));
	 		}
 		}
		if ($event->getType() == 'beforeExecuteRoute') {
			// get translation by controller name tag
			$di['t']->getTranslation($dispatcher->getControllerName());
		}
		
		if ($event->getType() == 'beforeNotFoundAction') {
			$di['response']->redirect('errors/error404');
			return false;
		}
		
    if ($event->getType() == 'beforeException') {
	    switch ($exception->getCode()) {
	      case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
					$di['flashSession']->error("EXCEPTION_HANDLER_NOT_FOUND: " . $dispatcher->getControllerName());
	      case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
					$di['flashSession']->error("EXCEPTION_ACTION_NOT_FOUND: " . $dispatcher->getActionName());
	      	$di['response']->redirect('errors/error404');
					return false;
	    }
    }
		
 		
  });
	$dispatcher = new Phalcon\Mvc\Dispatcher();
  //Listen for events produced in the dispatcher using the Security plugin
  $eventsManager->attach('dispatch', $di['ManafxAuth']);

	$dispatcher->setDefaultNamespace("Manafx\Controllers\\");
  //Bind the EventsManager to the Dispatcher
  $dispatcher->setEventsManager($eventsManager);
  return $dispatcher;
});

/**
 * Register ManafxAuth
 */
$di->set('ManafxAuth', function() use ($di) {
	return new ManafxAuth($di);
});


/**
 * Register crypt component
 */
$di->set('crypt', function() {
  $crypt = new Phalcon\Crypt();
  $crypt->setKey(AUTH_KEY);
  $crypt->setMode(MCRYPT_MODE_CBC);
  return $crypt;
});

/**
 * Custom authentication component
 */
$di->set('auth', function () {
  return new Auth();
});

/**
 * Register security component
 */
$di->set('security', function(){
  $security = new Phalcon\Security();
  //Set the password hashing factor to 12 rounds
  $security->setWorkFactor(12);
  return $security;
}, true);

/**
 * Registering mail service
 */
$di->set('mail', function () {
	return new Mail();
});

/**
 * Registering a shared view component
 */
if (!isset($di['view'])) {
	$di->set('view', function() use ($g_config) {

    //Create an events manager
    $eventsManager = new Phalcon\Events\Manager();

	  //Attach a listener for type "view"
	  $eventsManager->attach("view", function($event, $view) {
	    if ($event->getType() == 'notFoundView') {
      	throw new Exception('View not found' . $view->getActiveRenderPath());
      }
    });
			
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir(VIEW_PATH . $g_config['application']['template'] . '/manafx/');
		
    //Bind the eventsManager to the view component
    $view->setEventsManager($eventsManager);
		
		return $view;
	});
} else {
	$di['view']->setViewsDir(VIEW_PATH . $g_config['application']['template']  . '/manafx/');
}
$di['view']->setLayoutsDir('../common/layouts/');

/**
 * Register application
 */
$application = new ManafxApplication();

/**
 * Register all active modules
 */
$active_modules = $g_config["application"]["active_modules"];
$registeredModules = array();
$registeredModules["manafx"] = function ($di){};
foreach ($active_modules as $active_module) {
	$className = "Manafx\\" . ucfirst($active_module) . "\Module";
	$modulePath = "../modules/$active_module/Module.php";
	$registeredModules[$active_module] = array(
		"className" => $className,
		"path" => $modulePath
	);
	if (is_file("../modules/$active_module/functions.php")) {
		include ("../modules/$active_module/functions.php");
	}
}
$application->registerModules($registeredModules);

/**
 * Pass the DI to the application
 */
$application->setDI($di);

echo $application->handle()->getContent();
