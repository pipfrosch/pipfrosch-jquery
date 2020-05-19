<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

// deletes the .htaccess if it exists
$htaccess = dirname(__FILE__) . "/.htaccess";
if ( file_exists( $htaccess ) ) {
  unlink( $htaccess );
}
