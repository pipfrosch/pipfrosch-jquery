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

// When updating versions be sure to update the SRI string.
// Use sha256 as it has not been broken and is smaller than sha384
define( "PIPJQV", "3.5.1" );
define( "PIPJQVSRI", "sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" );
define( "PIPJQMIGRATE", "3.3.0" );
define( "PIPJQMIGRATESRI", "sha256-wZ3vNXakH9k4P00fNGAlbN0PkpKSyhRa76IFy4V1PYE=" );

/* The following two functions are only used with the jQuery CDN.
   The first is used if SRI is enabled (recommended), the second
   if SRI is disabled */
function pipfrosch_jquery_add_jquery_sri( $tag, $handle, $source ) {
  switch( $handle ) {
    case 'jquery-core':
      return '<script src="' . $source . '" integrity="' . PIPJQVSRI . '" crossorigin="anonymous"></script>' . PHP_EOL;
      break;
    case 'jquery-migrate':
      return '<script src="' . $source . '" integrity="' . PIPJQMIGRATESRI . '" crossorigin="anonymous"></script>' . PHP_EOL;
  }
  return $tag;
}
function pipfrosch_jquery_add_jquery_crossorigin( $tag, $handle, $source ) {
  switch( $handle ) {
    case 'jquery-core':
    case 'jquery-migrate':
      return '<script src="' . $source . '" crossorigin="anonymous"></script>' . PHP_EOL;
  }
  return $tag;
}

function pipfrosch_jquery_update_core_jquery() {
  //according to https://codex.wordpress.org/Writing_a_Plugin add_option does nothing if an option already exists; so...
  add_option( 'pipfrosch_jquery_migrate', 'true' );
  add_option( 'pipfrosch_jquery_cdn', 'false' );
  add_option( 'pipfrosch_jquery_sri','true' );
  $migrate = true;
  $option = trim( strtolower( get_option( 'pipfrosch_jquery_migrate' ) ) );
  if ( $migrate === "false" ) {
    $migrate = false;
  }
  $cdn = false;
  $option = trim( strtolower( get_option( 'pipfrosch_jquery_cdn' ) ) );
  if ( $option === "true" ) {
    $cdn = true;
  }
  $sri = true;
  $option = trim( strtolower( get_option( 'pipfrosch_jquery_sri' ) ) );
  if ( $option === "false" ) {
    $sri = false;
  }
  if ($cdn) {
    $path = 'https://code.jquery.com/';
  } else {
    $path = trailingslashit( dirname( __FILE__ ) );
  }
  wp_deregister_script( 'jquery-core' );
  wp_deregister_script( 'jquery-migrate' );
  wp_register_script( 'jquery-core', $path . 'jquery-' . PIPJQV . '.min.js', array(), null );
  if ( $migrate ) {
    wp_register_script( 'jquery-migrate', $path . 'jquery-migrate-' . PIPJQMIGRATE . '.min.js', array( 'jquery-core' ), null );
  }
  if ( $cdn ) {
    if ( $sri ) {
      add_filter( 'script_loader_tag', 'pipfrosch_jquery_add_jquery_sri', 10, 3 );
    } else {
      add_filter( 'script_loader_tag', 'pipfrosch_jquery_add_jquery_crossorigin', 10, 3 );
    }
  }
}

// do not include dot files in plugin zip archive
// if created, this file is deleted by uninstall.php
function pipfrosch_jquery_set_expires_header() {
  $htaccess = trailingslashit( dirname( __FILE__ ) ) . ".htaccess";
  if ( file_exists( $htaccess ) ) {
    // do not overwrite if already exists
    return;
  }
  if ( is_writeable( __DIR__ ) ) {
    $contents  = '<IfModule mod_expires.c>' . PHP_EOL;
    $contents .= '  ExpiresActive On' . PHP_EOL;
    $contents .= '  <FilesMatch "\.min\.js">' . PHP_EOL;
    $contents .= '    ExpiresDefault "access plus 1 years"' . PHP_EOL;
    $contents .= '  </FilesMatch>' . PHP_EOL;
    $contents .= '</IfModule>' . PHP_EOL . PHP_EOL;
    file_put_contents($htaccess, $contents);
  }
  return;
}
register_activation_hook( __FILE__, 'pipfrosch_jquery_set_expires_header' );

/* do not mess with jQuery if on admin pages */
if ( ! is_admin() ) {
  add_action( 'wp_enqueue_scripts', 'pipfrosch_jquery_update_core_jquery' );
}

