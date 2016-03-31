<?php
$router = new Phalcon\Mvc\Router(FALSE);
$router->removeExtraSlashes(true);
// $router->setDefaultModule("manafx");

$router->add('/', array(
	'module' => $g_config['system']['route_to']['module'],
	'controller' => $g_config['system']['route_to']['controller'],
	'action' => $g_config['system']['route_to']['action'],
));

//-- Begin general backend routes
$router->add('/' . ADMIN_ROUTE, array(
	'module' => 'manafx',
	'controller' => 'dashboard',
	'action' => 'index',
));

$router->add('/' . ADMIN_ROUTE . '/:controller', array(
	'module' => 'manafx',
	'controller' => 1,
	'action' => "index",
));

$router->add('/' . ADMIN_ROUTE . '/:controller/:action', array(
	'module' => 'manafx',
	'controller' => 1,
	'action' => 2,
));

$router->add('/' . ADMIN_ROUTE . '/:controller/:action/:params', array(
	'module' => 'manafx',
	'controller' => 1,
	'action' => 2,
	'params' => 3,
));

//-- End general backend routes


//-- Begin backend special routes

$router->add('/logout', array(
	'module' => 'manafx',
	'controller' => 'index',
	'action' => 'logout',
));

$router->add('/accounts/recover', array(
	'module' => 'manafx',
	'controller' => 'admin',
	'action' => 'recover',
));

$router->add('/' . ADMIN_ROUTE . '/login', array(
	'module' => 'manafx',
	'controller' => 'admin',
	'action' => 'login',
));

$router->add('/' . ADMIN_ROUTE . '/logout', array(
	'module' => 'manafx',
	'controller' => 'admin',
	'action' => 'logout',
));

$router->add('/' . ADMIN_ROUTE . '/recover', array(
	'module' => 'manafx',
	'controller' => 'admin',
	'action' => 'recover',
));

$router->add('/' . ADMIN_ROUTE . '/accessDenied', array(
	'module' => 'manafx',
	'controller' => 'admin',
	'action' => 'accessDenied',
));


$router->add('/users/:action', array(
	'module' => 'manafx',
	'controller' => 'users',
	'action' => 1,
));

$router->add('/profile/:action', array(
	'module' => 'manafx',
	'controller' => 'profile',
	'action' => 1,
));

$router->add('/roles/:action', array(
	'module' => 'manafx',
	'controller' => 'roles',
	'action' => 1,
));

$router->add('/options/:action', array(
	'module' => 'manafx',
	'controller' => 'options',
	'action' => 1,
));

$router->add('/settings/:action', array(
	'module' => 'manafx',
	'controller' => 'settings',
	'action' => 1,
));

$router->add('/themes/:action', array(
	'module' => 'manafx',
	'controller' => 'themes',
	'action' => 1,
));

$router->add('/menus/:action', array(
	'module' => 'manafx',
	'controller' => 'menus',
	'action' => 1,
));

$router->add('/languages/:action', array(
	'module' => 'manafx',
	'controller' => 'languages',
	'action' => 1,
));

$router->add('/errors/:action', array(
	'module' => 'manafx',
	'controller' => 'errors',
	'action' => 1,
));

//-- End special backend routes

global $active_modules;

foreach ($active_modules as $active_module) {
	$routePath = "../modules/$active_module/config/routes.php";
	if (is_file($routePath)) {
		include ($routePath);
	}
}
 
// Set 404 paths
$router->notFound(array(
	'module' => 'manafx',
	'controller' => 'errors',
	'action'     => 'error404'
));

return $router;