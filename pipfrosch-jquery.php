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

// WordPress says to use real tabs and not spaces. I effing hate real tabs. seriously hate them.
//  The claim that real tabs allow flexibility across clients is a fracking lie. What do they
//  mean by clients anyway? Code is done with a text editor, not a client. And the space used
//  by a tab character varies widely by text editor, some even using spaces when you hit the
//  tab key. This file uses two spaces for each level of indentation. When you use tabs, files
//  that end up being mixed spaces and tabs are common. When you only use spaces and do not
//  use tabs, that does not happen. If using spaces is a deal breaker I will use tabs but I
//  really dislike them, my editors are smart and automatically indent so I would have to then
//  reconfigure then to use tabs instead of spaces only when maintaining WP stuff because I do
//  not use tabs elsewhere. I end up with mixed spaces and tabs when I try to use tabs.
//
// I tried to follow the other WP coding rules.

if ( ! defined( 'ABSPATH' ) ) { exit; }
define( "PIPFROSCH_JQUERY_PLUGIN_DIR", plugin_dir_path( __FILE__ ) );

require_once( PIPFROSCH_JQUERY_PLUGIN_DIR . 'versions.php' );
require_once( PIPFROSCH_JQUERY_PLUGIN_DIR . 'inc/functions.php' );
require_once( PIPFROSCH_JQUERY_PLUGIN_DIR . 'inc/options.php' );

register_activation_hook( __FILE__, 'pipfrosch_jquery_set_expires_header' );

add_action( 'admin_init', 'pipfrosch_jquery_register_settings' );
add_action( 'admin_menu', 'pipfrosch_jquery_register_options_page' );

/* do not mess with jQuery if on admin pages */
if ( ! is_admin() ) {
  add_action( 'wp_enqueue_scripts', 'pipfrosch_jquery_update_core_jquery' );
}

