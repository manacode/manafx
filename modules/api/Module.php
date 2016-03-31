<?php

namespace Manafx\Api;

class Module
{
	public function registerAutoloaders()
	{
		$loader = new \Phalcon\Loader();
		
		$loader->registerDirs(array(
			__DIR__,
			__DIR__ . "/controllers",
			__DIR__ . "/helpers",
			__DIR__ . "/library",
			__DIR__ . "/models",
			__DIR__ . "/plugins",
		));

		$loader->registerNamespaces(array(
			'Manafx\Api' => '../modules/api/',
			'Manafx\Api\Controllers' => '../modules/api/controllers/',
			'Manafx\Api\Models' => '../modules/api/models/',
	    'Manafx\Api\Forms' => '../modules/api/forms',
		));

		$loader->register();
	}

	/**
	 * Register the services here to make them module-specific
	 */
	public function registerServices($di)
	{
		$di['dispatcher']->setDefaultNamespace("Manafx\Api\Controllers\\");
		$di['view']->setViewsDir('../views/' . $di->get("config")->application->template . '/api/');
		
		$di->set('dbApi', function () {
			return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
				"host" => "localhost",
				"username" => "root",
				"password" => "",
				"dbname" => "manafx"
			));
		});
		
		
	}

}