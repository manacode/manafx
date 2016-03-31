<?php  if ( ! defined('CORE_PATH')) exit('No direct script access allowed');
/**
 * MANAFX
 *
 * Application Framework
 *
 * @package		Profiler
 * @author		Leonardus Agung
 * @copyright	Copyright (c) 2014, Manafx, Inc.
 * @license		http://manafx.com/license.html
 * @link		http://manafx.com
 * @since		Version 1.0
 */

class ManafxProfiler extends \Phalcon\Mvc\User\Component 
{
	var $time_start;
	var $time_end;
	var $rendered_views = array();
	var $dbprofiler;

	function __construct()
	{
		$this->time_start = $this->startTimer();
		$this->dbprofiler = new \Phalcon\Db\Profiler();
	  $this->setViewProfiler();
#	  $this->setEventDB('db');
 	}

	function setEventDB($conn, $msg="")
	{
		$eventsManager = $this->getDI()->getShared('eventsManager');

    // Listen all the database events
    $eventsManager->attach($conn, function ($event, $connection) {
        if ($event->getType() == 'beforeQuery') {
            $this->dbprofiler->startProfile($connection->getSQLStatement());
        }

        if ($event->getType() == 'afterQuery') {
            $this->dbprofiler->stopProfile();
        }
    });
    $this->getDI()[$conn]->setEventsManager($eventsManager);
	}


 	function setViewProfiler()
	{
    $eventsManager = $this->getDI()->getShared('eventsManager');
    $eventsManager->attach("view:beforeRenderView", function ($event, $view, $file) {
			$params = array();
			$toView = $view->getParamsToView();
			$toView = !$toView? array() : $toView;
			foreach ($toView as $k=>$v) {
				if (is_object($v)) {
					$params[$k] = "(object) " . get_class($v);
				} elseif(is_array($v)) {
					$array = array();
					foreach ($v as $key=>$value) {
						if (is_object($value)) {
							$array[$key] = "(object) " . get_class($value);
						} elseif (is_array($value)) {
							$array[$key] = 'array(..)';
						} else {
							$array[$key] = htmlspecialchars($value);
						}
					}
					$params[$k] = $array;
				} else {
					$params[$k] = htmlspecialchars((string)$v);
				}
			}
	
			$this->rendered_views[] = array(
				'path'=>$view->getActiveRenderPath(),
				'params'=>$params,
				'controller'=>$view->getControllerName(),
				'action'=>$view->getActionName(),
			);
    });
    
    if (!isset($this->getDI()['view'])) {
			$this->getDI()->set('view', function() {
				$view = new \Phalcon\Mvc\View();
				return $view;
			});
   	}
    $view = $this->getDI()->getShared('view');
    // Bind the eventsManager to the view component
    $view->setEventsManager($eventsManager);
	}
	
	public function output()
	{
		#$profiler = $this->getDI()->get('profiler');
		$profiler = $this->dbprofiler;
		$this->view->start();
		$this->view->partial('../manafx/partials/profiler', array(
		    'db_profiler'	=> $profiler,
		    'time_end'	=> $this->endTimer(),
		    'rendered_views'	=> $this->rendered_views,
		    'router_info'	=> $this->getRouter(),
		    'url_info' => $this->getUrlInfo(),
		    'server_info' => $this->getServerInfo()
		));
		$html = ob_get_contents();
		$this->view->finish();
		
		return $html;
	
	}

	function startTimer()
	{
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		return $time;
	}
	
	function endTimer()
	{
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $this->time_start), 4);
		$this->time_end = $total_time;
		return $total_time;
	}

	function getRouter()
	{
		$router = $this->getDI()->get('router');
		$router->handle();
		$router_info = array();
		$router_info['ModuleName'] = $router->getModuleName();
		$router_info['ControllerName'] = $router->getControllerName();
		$router_info['ActionName'] = $router->getActionName();
		$router_info['Params'] = $router->getParams();
		$router_info['wasMatched'] = $router->wasMatched();
		
		$mr = $router->getMatchedRoute();
		$amr = array();
		if ($mr !== NULL) {
			$amr['Name'] = $mr->getName();
			$amr['BeforeMatch'] = $mr->getBeforeMatch();
			$amr['RouteId'] = $mr->getRouteId();
			$amr['Pattern'] = $mr->getPattern();
			$amr['CompiledPattern'] = $mr->getCompiledPattern();
			$amr['RoutePaths'] = $mr->getRoutePaths();
			$amr['Paths'] = $mr->getPaths();
			# $amr['ReversedPaths'] = $mr->getReversedPaths();
			$amr['HttpMethods'] = $mr->getHttpMethods();
			$amr['Hostname'] = $mr->getHostname();
			$amr['Group'] = $mr->getGroup();
			$amr['Converters'] = $mr->getConverters();
		}
		$router_info['MatchedRoute'] = $amr;
		return $router_info;
	}
	
	function getUrlInfo()
	{
		$url = $this->getDI()->get('url');
		$uinfo = array();
		$uinfo['base_uri'] = $url->getBaseUri(); 
		$uinfo['static_base_uri'] = $url->getStaticBaseUri(); 
		$uinfo['base_path'] = $url->getBasePath(); 
		return $uinfo;
	}
	
	function getServerInfo()
	{
		$asi = array(
			'PHP_SELF',
			'argv',
			'argc',
			'GATEWAY_INTERFACE',
			'SERVER_ADDR',
			'SERVER_NAME',
			'SERVER_SOFTWARE',
			'SERVER_PROTOCOL',
			'REQUEST_METHOD',
			'REQUEST_TIME',
			'REQUEST_TIME_FLOAT',
			'QUERY_STRING',
			'DOCUMENT_ROOT',
			'HTTP_ACCEPT',
			'HTTP_ACCEPT_CHARSET',
			'HTTP_ACCEPT_ENCODING',
			'HTTP_ACCEPT_LANGUAGE',
			'HTTP_CONNECTION',
			'HTTP_HOST',
			'HTTP_REFERER',
			'HTTP_USER_AGENT',
			'HTTPS',
			'REMOTE_ADDR',
			'REMOTE_HOST',
			'REMOTE_PORT',
			'REMOTE_USER',
			'REDIRECT_REMOTE_USER',
			'SCRIPT_FILENAME',
			'SERVER_ADMIN',
			'SERVER_PORT',
			'SERVER_SIGNATURE',
			'PATH_TRANSLATED',
			'SCRIPT_NAME',
			'REQUEST_URI',
			'PHP_AUTH_DIGEST',
			'PHP_AUTH_USER',
			'PHP_AUTH_PW',
			'AUTH_TYPE',
			'PATH_INFO',
			'ORIG_PATH_INFO'
		);
		
		$aret = array();
		foreach ($asi as $si) {
			if (isset($_SERVER[$si])) {
				$aret[$si] = $_SERVER[$si];
			}
		}

		$aret["dirname"] = dirname($_SERVER['REQUEST_URI']);
		$aret["basename"] = basename($_SERVER['PHP_SELF']);
		$aret["pathinfo"] = pathinfo($_SERVER['PHP_SELF']);
		$aret["realpath"] = realpath($_SERVER['PHP_SELF']);
		return $aret;
	}

}