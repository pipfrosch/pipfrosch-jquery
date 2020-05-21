<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

# remove options set by the plugin
delete_option( 'pipjq_migrate' );
delete_option( 'pipjq_cdn' );
delete_option( 'pipjq_sri' );
delete_option( 'pipjq_cdnhost' );
