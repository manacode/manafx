<?php
/**
 * MANAFX version string
 *
 * @global string $manafx_version
 */
$manafx_version = '1.6.8';

/**
 * Holds the MANAFX DB revision, increments when changes are made to the MANAFX DB schema.
 *
 * @global int $manafx_db_version
 */
$manafx_db_version = 168;

/**
 * Holds the required Phalcon version
 *
 * @global string $required_phalcon_version
 */
$required_phalcon_version = '2.0.6';

/**
 * Holds the required PHP version
 *
 * @global string $required_php_version
 */
$required_php_version = '5.4.0';

/**
 * Holds the required MySQL version
 *
 * @global string $required_mysql_version
 */
$required_mysql_version = '5.0';

/**
 * Holds the CKEditor version
 *
 * @global string $ckeditor_version
 */
$ckeditor_version = '4.4.1';

// Check phalcon framework installation.
if (!extension_loaded('phalcon')) {
	printf('Install Phalcon framework %s', $required_phalcon_version);
	exit(0);
}

if (version_compare(PHP_VERSION, $required_php_version, '<')) {
	printf('PHP version %s or above is required', $required_php_version);
	exit(0);
}

