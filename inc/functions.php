<?php

if ( ! defined( 'PIPJQ_PLUGIN_WEBPATH' ) ) { exit; }

/**
 * Get an option as boolean value.
 *
 * This function queries the option and returns a boolean value representing
 *  the option if the option has been set and returns a boolean default if the
 *  option has not been set. It also sets the option to a string equivalent of
 *  the boolean default (a "0" for false or a "1" for true) if the option is
 *  not set.
 *
 * @param string The name of the option to query.
 * @param bool   The default value to return and set if the option is not set.
 *
 * @return bool
 */
function pipjq_get_option_as_boolean( string $option, bool $default = true ) {
  $test = get_option( $option );
  if ( is_bool( $test ) ) {
    if ( $test ) {
       update_option( $option, '1');
       return $test;
    } else {
       $value = '0';
       if ( $default ) {
         $value = '1';
       }
       add_option( $option, $value );
       return $default;
    }
  }
  $q = intval( $test );
  if ( $q === 0 ) {
    return false;
  }
  return true;
}

// the sanitizes cdnhost string

/**
 * Sanitize CDN host string
 *
 * This WordPress uses a fixed set public Content Distribution Networks.
 *  This function eats an input and outputs a sanitized version that matches
 *  the case sensitivity expected, returning the default CDN if it can not
 *  identify which CDN is intended in the input.
 *
 * @param string The CDN host string to sanitize.
 *
 * @return string
 */
function pipjq_sanitize_cdnhost( string $input ) {
  $input = strtolower( sanitize_text_field( $input ) );
  switch( $input ) {
    case 'microsoft cdn':
      return 'Microsoft CDN';
      break;
    case 'jsdelivr cdn':
      return 'jsDelivr CDN';
      break;
    case 'cloudflare cdnjs':
      return 'CloudFlare CDNJS';
      break;
    case 'google cdn':
      return 'Google CDN';
      break;
  }
  return 'jQuery.com CDN';
}

/**
 * Get CDN host option and return as sanitized string.
 *
 * This function queries the option setting for the CDN host to use
 *  and sanitizes the result. In the event that the option is not
 *  yet set, it sets the option to the default value as returned by
 *  the pipjq_sanitize_cdnhost() function. In the event that the
 *  sanitized option returned differs from what is stored as in the
 *  WordPress options database for this setting, the WordPress option
 *  is updated with the sanitized version.
 *
 * @return string
 */
function pipjq_get_cdnhost_option() {
  $default = pipjq_sanitize_cdnhost( 'use default' );
  $test = get_option( 'pipjq_cdnhost' );
  if (! is_string ( $test ) ) {
    add_option( 'pipjq_cdnhost', $default );
    return $default;
  }
  $clean = pipjq_sanitize_cdnhost( $test );
  if ( $clean !== $test ) {
    update_option( 'pipjq_cdnhost', $clean );
  }
  return $clean;
}

// the callback to sanitize checkbox string
function pipjq_sanitize_checkbox( $input ) {
  $input = sanitize_text_field( $input );
  if ( is_numeric( $input ) ) {
    $num = intval( $input );
    if ( $num === 1 ) {
      return "1";
    }
  }
  return "0";
}

/* initiate options */
function pipjq_initialize_options() {
  $foo = pipjq_get_option_as_boolean( 'pipjq_migrate' );
  $foo = pipjq_get_option_as_boolean( 'pipjq_cdn', false );
  $foo = pipjq_get_option_as_boolean( 'pipjq_sri' );
  $foo = pipjq_get_cdnhost_option();
}

/* provide fallback if jQuery does not load from CDN */
function pipjq_fallback_for_cdn_failure( bool $core = true ) {
  $html = '<script>' . PHP_EOL . '  // Fallback to load locally if CDN fails' . PHP_EOL;
  if ($core) {
    $html .= '  (window.jQuery || document.write(\'<script src="' . PIPJQ_PLUGIN_WEBPATH . 'jquery-' . PIPJQV . '.min.js"><\/script>\'));' . PHP_EOL;
  } else {
    $html .= '  if (typeof jQuery.migrateWarnings == \'undefined\') {' . PHP_EOL;
    $html .= '    document.write(\'<script src="' . PIPJQ_PLUGIN_WEBPATH . 'jquery-migrate-' . PIPJQMIGRATE . '.min.js"><\/script>\');' . PHP_EOL;
    $html .= '  }' . PHP_EOL;
  }
  $html .= '</script>' . PHP_EOL;
  return $html;
}

/* The following two functions are only used with a CDN.
   The first is used if SRI is enabled (recommended), the second
   if SRI is disabled */
function pipjq_add_sri_attributes( $tag, $handle, $source ) {
  switch( $handle ) {
    case 'jquery-core':
      $html = pipjq_fallback_for_cdn_failure();
      return '<script src="' . $source . '" integrity="' . PIPJQVSRI . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
      break;
    case 'jquery-migrate':
      $html = pipjq_fallback_for_cdn_failure( false );
      return '<script src="' . $source . '" integrity="' . PIPJQMIGRATESRI . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
  }
  return $tag;
}
function pipjq_add_crossorigin_attribute( $tag, $handle, $source ) {
  switch( $handle ) {
    case 'jquery-core':
      $html = pipjq_fallback_for_cdn_failure();
      break;
    case 'jquery-migrate':
      $html = pipjq_fallback_for_cdn_failure( false );
      break;
    default:
      return $tag;
  }
  return '<script src="' . $source . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
}

function pipjq_script_src( string $cdnhost="localhost" ) {
  $rs = new stdClass();
  switch( $cdnhost ) {
    case 'jQuery.com CDN':
      $rs->jquery  = 'https://code.jquery.com/jquery-' . PIPJQV . '.min.js';
      $rs->migrate = 'https://code.jquery.com/jquery-migrate-' . PIPJQMIGRATE . '.min.js';
      $rs->cdn = true;
      break;
    case 'Microsoft CDN':
      $rs->jquery  = 'https://ajax.aspnetcdn.com/ajax/jQuery/jquery-' . PIPJQV . '.min.js';
      $rs->migrate = 'https://ajax.aspnetcdn.com/ajax/jquery.migrate/jquery-migrate-' . PIPJQMIGRATE . '.min.js';
      $rs->cdn = true;
      break;
    case 'jsDelivr CDN':
      $rs->jquery  = 'https://cdn.jsdelivr.net/npm/jquery@' . PIPJQV . '/dist/jquery.min.js';
      $rs->migrate = 'https://cdn.jsdelivr.net/npm/jquery-migrate@' . PIPJQMIGRATE . '/dist/jquery-migrate.min.js';
      $rs->cdn = true;
      break;
    case 'CloudFlare CDNJS':
      $rs->jquery  = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/' . PIPJQV . '/jquery.min.js';
      $rs->migrate = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/' . PIPJQMIGRATE . '/jquery-migrate.min.js';
      $rs->cdn = true;
      break;
    // Google seems to only have core, not migrate, so use jquery.com for migrate with them
    case 'Google CDN':
      $rs->jquery =  'https://ajax.googleapis.com/ajax/libs/jquery/' . PIPJQV . '/jquery.min.js';
      $rs->migrate = 'https://code.jquery.com/jquery-migrate-' . PIPJQMIGRATE . '.min.js';
      $rs->cdn = true;
      break;
    default:
      $rs->jquery  = PIPJQ_PLUGIN_WEBPATH . 'jquery-' . PIPJQV . '.min.js';
      $rs->migrate = PIPJQ_PLUGIN_WEBPATH . 'jquery-migrate-' . PIPJQMIGRATE . '.min.js';
      $rs->cdn = false;
  }
  return $rs;
}

// defines the included jQuery to be served
function pipjq_update_wpcore_jquery() {
  //get the settings and validate
  $migrate = pipjq_get_option_as_boolean( 'pipjq_migrate' );
  $cdn     = pipjq_get_option_as_boolean( 'pipjq_cdn', false );
  $sri     = pipjq_get_option_as_boolean( 'pipjq_sri' );
  $cdnhost = 'localhost';
  if ( $cdn ) {
    $cdnhost = pipjq_get_cdnhost_option();
  }
  $srcuri = pipjq_script_src( $cdnhost );
  //act on options
  wp_deregister_script( 'jquery-core' );
  wp_deregister_script( 'jquery-migrate' );
  wp_register_script( 'jquery-core', $srcuri->jquery, array(), null );
  if ( $migrate ) {
    wp_register_script( 'jquery-migrate', $srcuri->migrate, array( 'jquery-core' ), null );
  }
  if ( $srcuri->cdn ) {
    if ( $sri ) {
      add_filter( 'script_loader_tag', 'pipjq_add_sri_attributes', 10, 3 );
    } else {
      add_filter( 'script_loader_tag', 'pipjq_add_crossorigin_attribute', 10, 3 );
    }
  }
}

// initiated options and creates the .htaccess file. I do not like including a .htaccess within a plugin zip archive.
function pipjq_activation() {
  pipjq_initialize_options();
  $htaccess = PIPJQ_PLUGIN_DIR . ".htaccess";
  if ( file_exists( $htaccess ) ) {
    // do not overwrite if already exists
    return;
  }
  if ( is_writeable( dirname( $htaccess ) ) ) {
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

//settings

// the callback for add_settings_section
function pipjq_settings_form_text_helpers() {
  echo ('<p>It is recommended that you enable the ‘Use Migrate Plugin’ option (default).</p>');
  echo ('<p>It is recommended that you enable the ‘Use Content Distribution Network’ option.</p>');
  echo ('<p>It is recommended that you enable the ‘Use Subresource Integrity’ option (default).</p>');
}

// input tag for migrate
function pipjq_migrate_input_tag() {
  $migrate = pipjq_get_option_as_boolean( 'pipjq_migrate' );
  $checked = '';
  if ($migrate) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipjq_migrate" id="pipjq_migrate" value="1"' . $checked . '>';
}

// input tag for use cdn
function pipjq_cdn_input_tag() {
  $cdn = pipjq_get_option_as_boolean( 'pipjq_cdn', false );
  $checked = '';
  if ($cdn) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipjq_cdn" id="pipjq_cdn" value="1"' . $checked . '>';
}

// select tag for cdnhost
function pipjq_cdnhost_select_tag() {
  $cdnhost = pipjq_get_cdnhost_option();
  $values = array('jQuery.com CDN',
                  'CloudFlare CDNJS',
                  'jsDelivr CDN',
                  'Microsoft CDN',
                  'Google CDN');
  $html = '<select name="pipjq_cdnhost" id="pipjq_cdnhost">' . PHP_EOL;
  foreach($values as $value) {
    $selected = '';
    if ($cdnhost === $value) {
      $selected = ' selected="selected"';
    }
    $html .= '  <option value="' . $value . '"' . $selected . '>' . $value . '</option>' . PHP_EOL;
  }
  $html .= '</select>' . PHP_EOL;
  echo $html;
}

// input tag for SRI
function pipjq_sri_input_tag() {
  $sri = pipjq_get_option_as_boolean( 'pipjq_sri' );
  $checked = '';
  if ($sri) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipjq_sri" id="pipjq_sri" value="1"' . $checked . '>';
}

function pipjq_options_page_form() {
  $cdn = pipjq_get_option_as_boolean( 'pipjq_cdn', false );
  $parenthesis = '(disabled)';
  if ( $cdn ) {
    $parenthesis = '(enabled)';
  }
  $cdnhost = pipjq_get_cdnhost_option();
  $s = array( '/CDN$/' , '/CDNJS/' );
  $r = array( '<abbr>CDN</abbr>' , '<abbr>CDNJS</abbr>' );
  $cdnhost = preg_replace($s, $r, $cdnhost);
  $html  = '    <h2>Pipfrosch jQuery Plugin Management</h2>' . PHP_EOL;
  $html .= '    <p>jQuery Version: ' . PIPJQV . '</p>' . PHP_EOL;
  $html .= '    <p>jQuery Migrate Plugin Version: ' . PIPJQMIGRATE . '</p>' . PHP_EOL;
  $html .= '    <p>Current <abbr title="Content Distribution Network">CDN</abbr>: ' . $cdnhost . ' ' . $parenthesis . '</p>' . PHP_EOL;
  $html .= '    <form method="post" action="options.php">' . PHP_EOL;
  echo $html;
  settings_fields( PIPJQ_OPTIONS_GROUP );
  do_settings_sections( PIPJQ_SETTINGS_PAGE_SLUG_NAME );
  $html  = '      <p>Note that the ‘Use Subresource Integrity’ option only has meaning when ‘Use Content Distribution Network’ is enabled.</p>' . PHP_EOL;
  $html .= get_submit_button() . PHP_EOL;
  $html .= '    </form>' . PHP_EOL;
  echo $html;
}

function pipjq_register_settings() {
  register_setting( PIPJQ_OPTIONS_GROUP,
                    'pipjq_migrate',
                    array( 'sanitize_callback' => 'pipjq_sanitize_checkbox' ) );
  register_setting( PIPJQ_OPTIONS_GROUP,
                    'pipjq_cdn',
                    array( 'sanitize_callback' => 'pipjq_sanitize_checkbox' ) );
  register_setting( PIPJQ_OPTIONS_GROUP,
                    'pipjq_sri',
                    array( 'sanitize_callback' => 'pipjq_sanitize_checkbox' ) );
  register_setting( PIPJQ_OPTIONS_GROUP,
                    'pipjq_cdnhost',
                    array( 'sanitize_callback' => 'pipjq_sanitize_cdnhost' ) );

  add_settings_section( PIPJQ_SECTION_SLUG_NAME,
                        'Plugin Options',
                        'pipjq_settings_form_text_helpers',
                        PIPJQ_SETTINGS_PAGE_SLUG_NAME );

  add_settings_field( 'pipjq_migrate',
                      'Use Migrate Plugin',
                      'pipjq_migrate_input_tag',
                      PIPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PIPJQ_SECTION_SLUG_NAME,
                      array('label_for' => 'pipjq_migrate' ) );

  add_settings_field( 'pipjq_cdn',
                      'Use Content Distribution Network',
                      'pipjq_cdn_input_tag',
                      PIPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PIPJQ_SECTION_SLUG_NAME,
                      array( 'label_for' => 'pipjq_cdn' ) );

  add_settings_field( 'pipjq_sri',
                      'Use Subresource Integrity',
                      'pipjq_sri_input_tag',
                      PIPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PIPJQ_SECTION_SLUG_NAME,
                      array( 'label_for' => 'pipjq_sri' ) );

  add_settings_field( 'pipjq_cdnhost',
                      'Select Public CDN Service',
                      'pipjq_cdnhost_select_tag',
                      PIPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PIPJQ_SECTION_SLUG_NAME,
                      array( 'label_for' => 'pipjq_cdnhost' ) );
}

function pipjq_register_options_page() {
  add_options_page( 'jQuery ' . PIPJQV . ' Options',
                    'jQuery Options',
                    'manage_options',
                    'pipfrosch_jquery',
                    'pipjq_options_page_form' );
}