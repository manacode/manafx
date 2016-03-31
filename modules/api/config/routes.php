<?php
$router->add('/' . ADMIN_ROUTE . '/api', array(
	'module' => 'api',
	'controller' => 'index',
	'action' => 'index',
));
$router->add('/' . ADMIN_ROUTE . '/api/:controller', array(
	'module' => 'api',
	'controller' => 1,
	'action' => "index",
));

$router->add('/' . ADMIN_ROUTE . '/api/:controller/:action', array(
	'module' => 'api',
	'controller' => 1,
	'action' => 2,
));

$router->add('/' . ADMIN_ROUTE . '/api/:controller/:action/:params', array(
	'module' => 'api',
	'controller' => 1,
	'action' => 2,
	'params' => 3,
));
