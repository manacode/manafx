<?php
return array (
  '_settings' => array(
    'caption' => $this->t->_('Settings'),
    'roles' => array(1,2),
    'beforeCaption' => '<span class="glyphicon glyphicon-cog"></span>',
    'submenus' => array(
    	'_settings_general' => array(
    		'caption' => $this->t->_('General'),
    		'action' => $this->adminUri . '/settings/general',
    		'roles' => array(1,2),
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-cog"></span>',
    	),
    	'_settings_modules' => array(
  			'caption' => $this->t->_('Modules'),
  			'action' => $this->adminUri . '/settings/modules',
  			'roles' => array(1,2),
  			'divider' => false,
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-paperclip"></span>',
    	),
    	'_settings_languages' => array(
  			'caption' => $this->t->_('Languages'),
  			'action' => $this->adminUri . '/languages',
  			'roles' => array(1,2),
  			'divider' => false,
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-globe"></span>',
    	),
    	'_settings_browse_options' => array(
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