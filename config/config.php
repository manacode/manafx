<?php return array (
  'database' => 
  array (
    'adapter' => 'Mysql',
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'root',
    'password' => '',
    'dbname' => 'manafx',
    'tableprefix' => 'fx_',
    'query_limit' => '200',
  ),
  'system' => 
  array (
    'debug_mode' => 'on',
    'profiler_mode' => 'off',
    'maintenance_mode' => 'off',
    'frontpage_mode' => 'route_to',
    'redirect_to' => 'http://manafx.com',
    'route_to' => 
    array (
      'module' => 'manafx',
      'controller' => 'index',
      'action' => 'index',
    ),
  ),
  'application' => 
  array (
    'baseUrl' => 'http://manafx.dev',
    'title' => 'MANA FRAMEWORK',
    'description' => 'Mana Application Framework, powered by PhalconPHP',
    'admin_email' => 'admin@manafx.com',
    'template' => 'manafx-bootstrap',
    'theme' => 'default',
    'timezone_identifier' => 'Asia/Jakarta',
    'default_language' => 'en-US',
    'users_can_register' => '1',
    'default_role' => '3',
    'date_format' => 'Y/m/d',
    'time_format' => 'H:i:s',
    'start_of_week' => '0',
    'active_modules' => 
    array (
    ),
  ),
  'mail' => 
  array (
    'mail_sending' => 'on',
    'mail_massmail' => 'off',
    'mail_from_name' => 'ManaFx',
    'mail_from_email' => 'donotreply@manafx.com',
    'mail_mailer' => 'phpmail',
    'mail_sendmail_path' => '/usr/sbin/sendmail',
    'mail_smtp_auth' => 'no',
    'mail_smtp_security' => 'none',
    'mail_smtp_host' => 'localhost',
    'mail_smtp_port' => '25',
    'mail_smtp_username' => '',
    'mail_smtp_password' => '',
  ),
);