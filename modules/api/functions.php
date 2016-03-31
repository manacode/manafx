<?php

$publicResources = array(
	array('api', '*', '*'),
);
$di['ManafxAuth']->addPublicResource($publicResources);

function api_adminSidebarMenu($o) {
	$t = $o->t;
	$newMenu = array(
  	'_api' => array(
  		'parent' => '_settings',
  		'caption' => $t->_('API'),
  		'action' => '/' . ADMIN_ROUTE . '/api',
  		'roles' => array(),
			'divider' => true,
  		'class' => "",
  		'beforeCaption' => '<span class="glyphicon glyphicon-fire"></span>',
	  ),
	);
	$o->adminSidebarMenu->addMenu($newMenu);
}