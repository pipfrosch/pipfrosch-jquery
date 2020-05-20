<?php

if ( ! defined( 'PIPFROSCH_JQUERY_PLUGIN_WEBPATH' ) ) { exit; }

// boolean options are stored by plugin as strings in options API
//  this function always returns a boolean and "fixes" the options value
//  if not set or set to true instead of string
function pipfrosch_jquery_getas_boolean( string $option, bool $default = true ) {
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

// the callback to sanitize cdnhost string
function pipfrosch_press_sanitize_cdnhost( $input ) {
  $input = strtolower( sanitize_text_field( $input ) );
  switch( $input ) {
    case 'microsoft cdn':
      return 'Microsoft CDN';
      break;
    case 'jsdelivr cdn':
      return 'jsDelivr CDN';
      break;
    case 'cloudflare cdnjd':
      return 'CloudFlare CDNJS';
      break;
    case 'google cdn':
      return 'Google CDN';
      break;
  }
  return 'jQuery.com CDN';
}

// returns the CDN host and sets it if it is not set
function pipfrosch_jquery_getstring_cdnhost() {
  $default = pipfrosch_press_sanitize_cdnhost( 'use default' );
  $test = get_option( 'pipfrosch_jquery_cdnhost' );
  if (! is_string ( $test ) ) {
    add_option( 'pipfrosch_jquery_cdnhost', $default );
    return $default;
  }
  $clean = pipfrosch_press_sanitize_cdnhost( $test );
  if ( $clean !== $test ) {
    update_option( 'pipfrosch_jquery_cdnhost', $clean );
  }
  return $clean;
}

// the callback to sanitize checkbox string - currently broken
function pipfrosch_press_sanitize_checkbox( $input ) {
  error_log( $input );
  $input = sanitize_text_field( $input );
  if ( is_numeric( $input ) ) {
    $num = intval( $input );
    if ( $num === 1 ) {
      return "1";
    }
  }
  return "1";
}

function pipfrosch_jquery_validate_input( $input ) {
  if (is_array($input)) {
    error_log( "is array" );
    foreach($input as $key => $value ) {
      $string = $key . " " . $value;
      error_log($string);
    }
  } else {
    $foo = gettype($input);
    error_log($foo);
  }
}

/* initiate options */
function pipfrosch_jquery_initiate_options() {
  $foo = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_migrate' );
  $foo = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_cdn', false );
  $foo = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_sri' );
  $foo = pipfrosch_jquery_getstring_cdnhost();
}

/* provide fallback if jQuery does not load from CDN */
function pipfrosch_jquery_fallback( $core = true ) {
  $html = '<script>' . PHP_EOL . '  // Fallback to load locally if CDN fails' . PHP_EOL;
  if ($core) {
    $html .= '  (window.jQuery || document.write(\'<script src="' . PIPFROSCH_JQUERY_PLUGIN_WEBPATH . 'jquery-' . PIPJQV . '.min.js"><\/script>\'));' . PHP_EOL;
  } else {
    $html .= '  if (typeof jQuery.migrateWarnings == \'undefined\') {' . PHP_EOL;
    $html .= '    document.write(\'<script src="' . PIPFROSCH_JQUERY_PLUGIN_WEBPATH . 'jquery-migrate-' . PIPJQMIGRATE . '.min.js"><\/script>\');' . PHP_EOL;
    $html .= '  }' . PHP_EOL;
  }
  $html .= '</script>' . PHP_EOL;
  return $html;
}

/* The following two functions are only used with the jQuery CDN.
   The first is used if SRI is enabled (recommended), the second
   if SRI is disabled */
function pipfrosch_jquery_add_jquery_sri( $tag, $handle, $source ) {
  switch( $handle ) {
    case 'jquery-core':
      $html = pipfrosch_jquery_fallback();
      return '<script src="' . $source . '" integrity="' . PIPJQVSRI . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
      break;
    case 'jquery-migrate':
      $html = pipfrosch_jquery_fallback( false );
      return '<script src="' . $source . '" integrity="' . PIPJQMIGRATESRI . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
  }
  return $tag;
}
function pipfrosch_jquery_add_jquery_crossorigin( $tag, $handle, $source ) {
  switch( $handle ) {
    case 'jquery-core':
      $html = pipfrosch_jquery_fallback();
      break;
    case 'jquery-migrate':
      $html = pipfrosch_jquery_fallback( false );
      break;
    default:
      return $tag;
  }
  return '<script src="' . $source . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
}

function pipfrosch_jquery_source( string $cdnhost="localhost" ) {
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
      $rs->jquery  = PIPFROSCH_JQUERY_PLUGIN_WEBPATH . 'jquery-' . PIPJQV . '.min.js';
      $rs->migrate = PIPFROSCH_JQUERY_PLUGIN_WEBPATH . 'jquery-migrate-' . PIPJQMIGRATE . '.min.js';
      $rs->cdn = false;
  }
  return $rs;
}

// defines the included jQuery to be served
function pipfrosch_jquery_update_core_jquery() {
  //get the settings and validate
  $migrate = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_migrate' );
  $cdn     = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_cdn', false );
  $sri     = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_sri' );
  $cdnhost = 'localhost';
  if ( $cdn ) {
    $cdnhost = pipfrosch_jquery_getstring_cdnhost();
  }
  $srcuri = pipfrosch_jquery_source( $cdnhost );
  //act on options
  wp_deregister_script( 'jquery-core' );
  wp_deregister_script( 'jquery-migrate' );
  wp_register_script( 'jquery-core', $srcuri->jquery, array(), null );
  if ( $migrate ) {
    wp_register_script( 'jquery-migrate', $srcuri->migrate, array( 'jquery-core' ), null );
  }
  if ( $srcuri->cdn ) {
    if ( $sri ) {
      add_filter( 'script_loader_tag', 'pipfrosch_jquery_add_jquery_sri', 10, 3 );
    } else {
      add_filter( 'script_loader_tag', 'pipfrosch_jquery_add_jquery_crossorigin', 10, 3 );
    }
  }
}

// initiated options and creates the .htaccess file. I do not like including a .htaccess within a plugin zip archive.
function pipfrosch_jquery_set_expires_header() {
  pipfrosch_jquery_initiate_options();
  $htaccess = PIPFROSCH_JQUERY_PLUGIN_DIR . ".htaccess";
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
function cbfn_pipfrosch_jquery_add_settings_section() {
  echo ('<p>It is recommended that you enable the ‘Use Migrate Plugin’ option (default).</p>');
  echo ('<p>It is recommended that you enable the ‘Use Content Distribution Network’ option.</p>');
  echo ('<p>It is recommended that you enable the ‘Use Subresource Integrity’ option (default).</p>');
}

// render migrate
function pipfrosch_jquery_render_migrate() {
  $migrate = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_migrate' );
  $checked = '';
  if ($migrate) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipfrosch_jquery_migrate" id="pipfrosch_jquery_migrate" value="1"' . $checked . '>';
}

// render cdn
function pipfrosch_jquery_render_cdn() {
  $cdn = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_cdn', false );
  $checked = '';
  if ($cdn) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipfrosch_jquery_cdn" id="pipfrosch_jquery_cdn" value="1"' . $checked . '>';
}

// render sri
function pipfrosch_jquery_render_sri() {
  $sri = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_sri' );
  $checked = '';
  if ($sri) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipfrosch_jquery_sri" id="pipfrosch_jquery_sri" value="1"' . $checked . '>';
}

function cbfn_pipfrosch_jquery_options_page() {
  $cdn = pipfrosch_jquery_getas_boolean( 'pipfrosch_jquery_cdn', false );
  $parenthesis = '(disabled)';
  if ( $cdn ) {
    $parenthesis = '(enabled)';
  }
  $cdnhost = pipfrosch_jquery_getstring_cdnhost();
  $s = array( '/CDN$/' , '/CDNJS/' );
  $r = array( '<abbr>CDN</abbr>' , '<abbr>CDNJS</abbr>' );
  $cdnhost = preg_replace($s, $r, $cdnhost);
  $html  = '    <h2>Pipfrosch jQuery Plugin Management</h2>' . PHP_EOL;
  $html .= '    <p>jQuery Version: ' . PIPJQV . '</p>' . PHP_EOL;
  $html .= '    <p>jQuery Migrate Plugin Version: ' . PIPJQMIGRATE . '</p>' . PHP_EOL;
  $html .= '    <p>Current <abbr title="Content Distribution Network">CDN</abbr>: ' . $cdnhost . ' ' . $parenthesis . '</p>' . PHP_EOL;
  $html .= '    <form method="post" action="options.php">' . PHP_EOL;
  echo $html;
  settings_fields( PPJQ_OPTIONS_GROUP );
  do_settings_sections( PPJQ_SETTINGS_PAGE_SLUG_NAME );
  $html  = '      <p>Note that the ‘Use Subresource Integrity’ option only has meaning when ‘Use Content Distribution Network’ is enabled.</p>' . PHP_EOL;
  $html .= get_submit_button() . PHP_EOL;
  $html .= '    </form>' . PHP_EOL;
  echo $html;
}

function pipfrosch_jquery_register_settings() {
  register_setting( PPJQ_OPTIONS_GROUP,
                    'pipfrosch_jquery_migrate',
                    array( 'sanitize_callback' => 'pipfrosch_jquery_validate_input' ) );
  register_setting( PPJQ_OPTIONS_GROUP,
                    'pipfrosch_jquery_cdn',
                    array( 'sanitize_callback' => 'pipfrosch_jquery_validate_input' ) );
  register_setting( PPJQ_OPTIONS_GROUP,
                    'pipfrosch_jquery_sri',
                    array( 'sanitize_callback' => 'pipfrosch_jquery_validate_input' ) );
  //register_setting( PPJQ_OPTIONS_GROUP,
  //                  'pipfrosch_jquery_cdnhost',
  //                  array( 'sanitize_callback' => 'pipfrosch_press_sanitize_cdnhost' ) );

  add_settings_section( PPJQ_SECTION_SLUG_NAME,
                        'Plugin Options',
                        'cbfn_pipfrosch_jquery_add_settings_section',
                        PPJQ_SETTINGS_PAGE_SLUG_NAME );

  add_settings_field( 'pipfrosch_jquery_migrate',
                      'Use Migrate Plugin',
                      'pipfrosch_jquery_render_migrate',
                      PPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PPJQ_SECTION_SLUG_NAME,
                      array('label_for' => 'pipfrosch_jquery_migrate' ) );

  add_settings_field( 'pipfrosch_jquery_cdn',
                      'Use Content Distribution Network',
                      'pipfrosch_jquery_render_cdn',
                      PPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PPJQ_SECTION_SLUG_NAME,
                      array( 'label_for' => 'pipfrosch_jquery_cdn' ) );

  add_settings_field( 'pipfrosch_jquery_sri',
                      'Use Subresource Integrity',
                      'pipfrosch_jquery_render_sri',
                      PPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PPJQ_SECTION_SLUG_NAME,
                      array( 'label_for' => 'pipfrosch_jquery_sri' ) );
}

function pipfrosch_jquery_register_options_page() {
  add_options_page( 'jQuery ' . PIPJQV . ' Options',
                    'jQuery Options',
                    'manage_options',
                    'pipfrosch_jquery',
                    'cbfn_pipfrosch_jquery_options_page' );
}