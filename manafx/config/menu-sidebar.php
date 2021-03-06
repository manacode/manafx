<?php
return array(
  '_users' => array(
    'caption' => $this->t->_('Users'),
    'roles' => array(),
    'beforeCaption' => '<span class="glyphicon glyphicon-user"></span>',
    'submenus' => array(
    	'_users_subheader' => array(
    		'caption' => $this->t->_('Users Manager'),
    		'action' => '',
    		'roles' => array(1,2),
//    		'class' => "nav-subheader",
    		'type' => "header",
    	),
    	'_users_search' => array(
    		'caption' => $this->t->_('Search Users'),
    		'action' => $this->adminUri . '/users',
    		'roles' => array(1,2),
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-user"></span>',
    	),
    	'_users_create' => array(
    		'caption' => $this->t->_('Create User'),
    		'action' => $this->adminUri . '/users/create-user',
    		'roles' => array(1,2),
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-plus-sign"></span>',
    	),
    	'_users_roles' => array(
  			'caption' => $this->t->_('Roles'),
  			'action' => $this->adminUri . '/roles',
  			'roles' => array(1,2),
  			'divider' => true,
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-fire"></span>',
    	),
    	'_users_permissions' => array(
  			'caption' => $this->t->_('Permissions'),
  			'action' => $this->adminUri . '/permissions',
  			'roles' => array(1,2),
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-lock"></span>',
    	),
    	'_users_profile_subheader' => array(
    		'caption' => $this->t->_('My Profile'),
    		'action' => '',
    		'roles' => array(1,2),
  			'divider' => true,
    		'type' => "header",
    	),
    	'_users_profile' => array(
  			'caption' => $this->t->_('Edit Profile'),
  			// 'action' => $this->adminUri . '/profile',
  			'roles' => array(),
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-education"></span>',
    	),
    	'_users_profile_change_password' => array(
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
    	'_appearance_themes' => array(
    		'caption' => $this->t->_('Themes'),
    		'action' => $this->adminUri . '/themes',
    		'roles' => array(1,2),
    		'class' => "",
    		'beforeCaption' => '<span class="glyphicon glyphicon-phone"></span>',
    	),
    	'_appearance_menus' => array(
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
