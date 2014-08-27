<?php
/**
 * Database connection settings.
 */
define( 'DB_NAME', '' );
define( 'DB_HOST', '' );
define( 'DB_USER', '' );
define( 'DB_PASS', '' );

/**
 * Load the database class.
 */
require( '/includes/opdb-class.php' );

/**
 * Instanciate the class
 */
$opdb = new OPDB;