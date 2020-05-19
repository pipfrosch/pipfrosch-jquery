<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

# remove options
delete_option( 'pipfrosch_jquery_compat' ); //temporary nuke after testing
delete_option( 'pipfrosch_jquery_migrate' );
delete_option( 'pipfrosch_jquery_cdn' );
delete_option( 'pipfrosch_jquery_sri' );

// deletes the .htaccess if it exists
$pipfrosch_jquery_htaccess = trailingslashit( dirname( __FILE__ ) ) . ".htaccess";
if ( file_exists( $pipfrosch_jquery_htaccess ) ) {
  unlink( $htaccess );
}
