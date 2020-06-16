<?php
/**
 * Plugin Name:       Pipfrosch jQuery
 * Plugin URI:        https://wordpress.org/plugins/pipfrosch-jquery/
 * Description:       Provides a modern jQuery environment for WordPress frontend
 * Tags:              jQuery
 * Version:           1.2.1
 * Requires at least: 4.1.0
 * Tested up to:      5.4.2
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

/*
 * WordPress says to use real tabs and not spaces. I effing hate real tabs.
 * I tried to follow the other WP coding rules. Well, sort of...
 *
 * This plugin uses the pipjq_ prefix as an emulated namespace for most
 *  things and the prefix PIPJQ_ prefix for constants *except* for the
 *  jQuery and Migrate version and SRI constants, where I do not use
 *  underscores at all so it is just prefixed with PIPJQ.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$pipjq_url_array = parse_url( plugin_dir_url( __FILE__ ) );

define( "PIPJQ_PLUGIN_DIR", plugin_dir_path( __FILE__ ) );
define( "PIPJQ_PLUGIN_WEBPATH", $pipjq_url_array['path'] );
define( "PIPJQ_PLUGIN_VERSION", '1.2.1pre' );

/*
 * When developing, the Settings API gave me trouble. By defining the
 *  various slugs as constants it made it much easier to visually see
 *  where my mistakes were. Figured might as well keep them.
 */
define( "PIPJQ_OPTIONS_GROUP", 'pipjq_opgroup');
define( "PIPJQ_SECTION_SLUG_NAME", 'pipjq_settings_form' );
define( "PIPJQ_SETTINGS_PAGE_SLUG_NAME", 'pipjq_options');

require_once( PIPJQ_PLUGIN_DIR . 'versions.php' );
require_once( PIPJQ_PLUGIN_DIR . 'inc/functions.php' );

//activation and upgrade
pipjq_upgrade_check();

/* only do settings stuff if on admin page, do not update jQuery if on admin pages */
if ( is_admin() ) {
  add_action( 'admin_init', 'pipjq_register_settings' );
  add_action( 'admin_menu', 'pipjq_register_options_page' );
} else {
  add_action( 'wp_enqueue_scripts', 'pipjq_update_wpcore_jquery' );
}

