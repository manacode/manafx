<?php
return array (
  '_users' => array(
    'caption' => $this->t->_('Hi', 'Howdy') . ', <b>' . $this->auth['user_username'] . '</b>',
    'action' => 'javascript:void(0)',
    'roles' => array(),
    'submenus' => array(
    	'_users_profile' => array(
    		'caption' => $this->t->_('My Profile'),
    		'action' => $this->adminUri . '/profile',
    		'roles' => array(),
    		'beforeCaption' => '<span class="glyphicon glyphicon-education"></span>',
    	),
    	'_users_profile_change_password' => array(
    		'caption' => $this->t->_('Change Password'),
    		'action' => $this->adminUri . '/profile/change-password',
    		'roles' => array(),
    		'beforeCaption' => '<span class="glyphicon glyphicon-asterisk"></span>',
    	),
    	'_users_logout' => array(
  			'caption' => $this->t->_('Log Out'),
  			'action' => $this->adminUri . '/logout',
  			'roles' => array(),
  			'beforeCaption' => '<span class="glyphicon glyphicon-off"></span>',
    		'divider' => true,
    	),
    ),
  ),
  '_help' => array(
      'caption' => $this->t->_('Help'),
      'action' => 'javascript:void(0)',
      'roles' => array(),
  ),

);