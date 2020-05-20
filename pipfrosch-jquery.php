<?php
/**
 * Plugin Name:       Pipfrosch jQuery
 * Plugin URI:        https://github.com/pipfrosch/pipfrosch-jquery
 * Description:       Provides a modern jQuery environment for WordPress frontend
 * Tags:              jQuery
 * Version:           3.5.1pip0
 * Requires at least: 4.1.0
 * Tested up to:      5.4.1
 * Author:            Pipfrosch Press
 * Author URI:        https://pipfrosch.com/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       pipfrosch-jquery
 * Domain Path:       /languages
 */

/*
Pipfrosch jQuery is basically just jQuery with some PHP to make it easy to replace the version
that is shipped as part of WordPress itself. jQuery is MIT license, and so this plugin is as
well. See the LICENSE file.
*/

// WordPress says to use real tabs and not spaces. I effing hate real tabs.
//
// I tried to follow the other WP coding rules.

if ( ! defined( 'ABSPATH' ) ) { exit; }

//include_once(ABSPATH . 'wp-includes/pluggable.php');
//include(ABSPATH . "wp-admin/includes/user.php.");

$pipfrosch_jquery_url = parse_url( plugin_dir_url( __FILE__ ) );

define( "PIPFROSCH_JQUERY_PLUGIN_DIR", plugin_dir_path( __FILE__ ) );
define( "PIPFROSCH_JQUERY_PLUGIN_WEBPATH", $pipfrosch_jquery_url['path'] );

require_once( PIPFROSCH_JQUERY_PLUGIN_DIR . 'versions.php' );
require_once( PIPFROSCH_JQUERY_PLUGIN_DIR . 'inc/functions.php' );
require_once( PIPFROSCH_JQUERY_PLUGIN_DIR . 'inc/options.php' );

register_activation_hook( __FILE__, 'pipfrosch_jquery_set_expires_header' );

/* only do settings stuff if on admin page, do not update jQuery if on admin pages */
if ( is_admin() ) {
  add_action( 'admin_init', 'pipfrosch_jquery_register_settings' );
  add_action( 'admin_menu', 'pipfrosch_jquery_register_options_page' );
} else {
  add_action( 'wp_enqueue_scripts', 'pipfrosch_jquery_update_core_jquery' );
}

