<?php

if ( ! defined( 'PIPFROSCH_JQUERY_PLUGIN_DIR' ) ) { exit; }

//sanitize plugin options before setting/updating them
function pipfrosch_jquery_set_boolean_option( string $option, bool $value ) {
  if (! is_string( $option ) ) {
    return;
  }
  $stub = substr( $option, 0, 17);
  if ( $stub !== "pipfrosch_jquery_" ) {
    return;
  }
  if ( ! is_bool( $value ) ) {
    // shamelessly stolen from wp-includes/rest-api.php
    //  I added trim
    if ( is_string( $value ) ) {
      $value = trim( strtolower( $value ) );
      if ( in_array( $value, array( 'false', '0' ), true ) ) {
        $value = false;
      }
    }
  }
  if ( ! get_option( $option ) ) {
    //option not yet set
    add_option( $option, boolval( $value ) );
    return;
  }
  update_option( $option, boolval( $value ) );
}

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

// defines the included jQuery to be served
function pipfrosch_jquery_update_core_jquery() {
  //get the settings and validate
  $migrate = get_option( 'pipfrosch_jquery_migrate', true );
  if ( ! is_bool( $migrate ) ) {
    $migrate = true;
    pipfrosch_jquery_set_boolean_option( 'pipfrosch_jquery_migrate', true );
  }
  $cdn = get_option( 'pipfrosch_jquery_cdn', false );
  if ( ! is_bool( $cdn ) ) {
    $cdn = false;
    pipfrosch_jquery_set_boolean_option( 'pipfrosch_jquery_cdn', false );
  }
  $sri = get_option( 'pipfrosch_jquery_sri', true );
  if ( ! is_bool( $sri ) ) {
    $sri = true;
    pipfrosch_jquery_set_boolean_option( 'pipfrosch_jquery_sri', true );
  }

  //act on options
  if ($cdn) {
    $path = 'https://code.jquery.com/';
  } else {
    $path = PIPFROSCH_JQUERY_PLUGIN_DIR;
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

// creates the .htaccess file. I do not like including a .htaccess within a plugin zip archive.
function pipfrosch_jquery_set_expires_header() {
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
function pipfrosh_jquery_show_recommend() {
  echo ('<p>It is recommended that you enable the ‘Use Migrate Plugin’ option (default).</p>');
  echo ('<p>It is recommended that you enable the ‘Use Content Distribution Network’ option.</p>');
  echo ('<p>It is recommended that you enable the ‘Use Subresource Integrity’ option (default).</p>');
  echo ('<p>Current <abbr title="Content Distribution Network">CDN</abbr>: code.jquery.com</p>');
}

// render migrate
function pipfrosh_jquery_render_migrate() {
  $migrate = get_option( 'pipfrosch_jquery_migrate', true );
  if ( ! is_bool( $migrate ) ) {
    $migrate = true;
  }
  $checked = '';
  if ($migrate) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipfrosh_jquery_migrate" id="pipfrosh_jquery_migrate" value="true"' . $checked . '>';
}

// render cdn
function pipfrosh_jquery_render_cdn() {
  $cdn = get_option( 'pipfrosch_jquery_cdn', false );
  if ( ! is_bool( $cdn ) ) {
    $cdn = false;
  }
  $checked = '';
  if ($cdn) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipfrosh_jquery_cdn" id="pipfrosh_jquery_cdn" value="true"' . $checked . '>';
}

// render sri
function pipfrosh_jquery_render_sri() {
  $sri = get_option( 'pipfrosch_jquery_sri', true );
  if ( ! is_bool( $sri ) ) {
    $sri = true;
  }
  $checked = '';
  if ($sri) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipfrosh_jquery_sri" id="pipfrosh_jquery_sri" value="true"' . $checked . '>';
}

function pipfrosch_jquery_register_settings() {
  add_option( 'pipfrosch_jquery_migrate' );
  add_option( 'pipfrosch_jquery_cdn' );
  add_option( 'pipfrosch_jquery_sri' );
  register_setting( 'pipfrosch_jquery_options',
                    'pipfrosch_jquery_migrate',
                    array('type' => 'boolean',
                          'description' => 'Load jQuery migrate ' . PIPJQMIGRATE  . ' plugin',
                          'sanitize_callback' => 'rest_sanitize_boolean',
                          'show_in_rest' => false,
                          'default' => true ) );
  register_setting( 'pipfrosh_jquery_options',
                    'pipfrosch_jquery_cdn',
                    array('type' => 'boolean',
                          'description' => 'Use code.jqeery.com CDN for jQuery',
                          'sanitize_callback' => 'rest_sanitize_boolean',
                          'show_in_rest' => false,
                          'default' => false ) );
  register_setting( 'pipfrosch_jquery_options',
                    'pipfrosch_jquery_sri',
                    array('type' => 'boolean',
                          'description' => 'Use Subresource Integrity when using jQuery CDN',
                          'sanitize_callback' => 'rest_sanitize_boolean',
                          'show_in_rest' => false,
                          'default' => true ) );
  add_settings_section( 'pipfrosh_jquery_settings_form',
                        'Plugin Options',
                        'pipfrosh_jquery_show_recommend',
                        'pipfrosch_jquery_options' );

  add_settings_field( 'pipfrosh_jquery_migrate',
                      'Use Migrate Plugin',
                      'pipfrosh_jquery_render_migrate',
                      'pipfrosch_jquery_options',
                      'pipfrosh_jquery_settings_form',
                      array('label_for' => 'pipfrosh_jquery_migrate' ) );

  add_settings_field( 'pipfrosh_jquery_cdn',
                      'Use Content Distribution Network',
                      'pipfrosh_jquery_render_cdn',
                      'pipfrosch_jquery_options',
                      'pipfrosh_jquery_settings_form',
                      array( 'label_for' => 'pipfrosh_jquery_cdn' ) );

  add_settings_field( 'pipfrosh_jquery_sri',
                      'Use Subresource Integrity',
                      'pipfrosh_jquery_render_sri',
                      'pipfrosch_jquery_options',
                      'pipfrosh_jquery_settings_form',
                      array( 'label_for' => 'pipfrosh_jquery_sri' ) );
}

function pipfrosch_jquery_register_options_page() {
  add_options_page( 'jQuery ' . PIPJQV . ' Options',
                    'jQuery Options',
                    'manage_options',
                    'pipfrosch_jquery',
                    'pipfrosch_jquery_options_page' );
}


















