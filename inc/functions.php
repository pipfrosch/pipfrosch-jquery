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
 * @param bool   Optional. Defaults to true.
 *               The default value to return and set if the option is not set.
 *
 * @return bool
 */
function pipjq_get_option_as_boolean( string $option, bool $default = true ): bool
{
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

/**
 * Sanitize CDN host string
 *
 * This WordPress plugin uses a fixed set public Content Distribution Networks.
 *  This function eats an input and outputs a sanitized version that matches
 *  the case sensitivity expected, returning the default CDN if it can not
 *  identify which CDN is intended in the input.
 *
 * @param string The CDN host string to sanitize.
 *
 * @return string
 */
function pipjq_sanitize_cdnhost( string $input ): string
{
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
function pipjq_get_cdnhost_option(): string
{
  $default = pipjq_sanitize_cdnhost( 'use default' );
  $test = get_option( 'pipjq_cdnhost' );
  if ( ! is_string ( $test ) ) {
    add_option( 'pipjq_cdnhost', $default );
    return $default;
  }
  $clean = pipjq_sanitize_cdnhost( $test );
  if ( $clean !== $test ) {
    update_option( 'pipjq_cdnhost', $clean );
  }
  return $clean;
}

/**
 * Initialize options
 *
 * This function makes sure the options are defined in the WordPress options
 *  database and sets them to the default values if they are not already
 *  defined. This script is run during plugin activation.
 *
 * @return void
 */
function pipjq_initialize_options(): void
{
  $foo = pipjq_get_option_as_boolean( 'pipjq_migrate' );
  $foo = pipjq_get_option_as_boolean( 'pipjq_cdn', false );
  $foo = pipjq_get_option_as_boolean( 'pipjq_sri' );
  $foo = pipjq_get_cdnhost_option();
  $test = get_option( 'pipjq_plugin_version' );
  if ( ( is_bool ($test) ) && ( ! $test ) ) {
      add_option( 'pipjq_plugin_version', PIPJQ_PLUGIN_VERSION );
  } else {
      update_option( 'pipjq_plugin_version', PIPJQ_PLUGIN_VERSION );
  }
  // not directly used but lets other plugins know
  $test = get_option( 'pipjq_jquery_version' );
  if ( ( is_bool ($test) ) && ( ! $test ) ) {
      add_option( 'pipjq_jquery_version', PIPJQV );
  } else {
      update_option( 'pipjq_jquery_version', PIPJQV );
  }
  $test = get_option( 'pipjq_jquery_migrate_version' );
  if ( ( is_bool ($test) ) && ( ! $test ) ) {
      add_option( 'pipjq_jquery_migrate_version', PIPJQMIGRATE );
  } else {
      update_option( 'pipjq_jquery_migrate_version', PIPJQMIGRATE );
  }
}

/**
 * Creates a .htaccess file for mod_expires
 *
 * @return void
 */
function pipjq_mod_expires(): void
{
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
    file_put_contents( $htaccess, $contents );
  }
}

/**
 * Upgrade check
 *
 * Callback to check to see if the installed version of plugin is an upgrade.
 *
 * @return void
 */
function pipjq_upgrade_check(): void
{
  $test = get_option( 'pipjq_plugin_version' );
  if ( ! is_string( $test ) ) {
      pipjq_initialize_options();
      pipjq_mod_expires();
  } elseif ( $test !== PIPJQ_PLUGIN_VERSION ) {
      pipjq_initialize_options();
      pipjq_mod_expires();
  }
}

/**
 * Fallback for a CDN failure.
 *
 * In the event that a client can not retrieve the needed jQuery file from a CDN or
 *  it retrieves the file but the SRI does not match, a website will be broken without
 *  a fallback. This function provides a small HTML snippet for a script that verifies
 *  retrieval of the jQuery library or the jQuery Migrate plugin was successful, and
 *  instructs the client to download the copy served from the local website if it was
 *  not.
 *
 * @param bool $core Optional. Defaults to true. When true, the HTML snippet is for
 *             testing the main jQuery library. When false, it is for testing the Migrate plugin.
 *
 * @return string
 */
function pipjq_fallback_for_cdn_failure( bool $core = true ): string
{
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

/**
 * Add the SRI and CrossOrigin attributes
 *
 * The WordPress core function `wp_register_script()` does not provide a means
 *  for adding attributes to a script node. When a script is served from a third
 *  party resource, it really needs to have both an `integrity` and a
 *  `crossorigin` attribute set so the browser can validate both the integrity of
 *  the resource being downloaded from the third party, and what authentication
 *  should be performed (always anonymous for a public CDN, which at least with
 *  some browsers means cookies are not set or sent even if they exist. Yay for
 *  privacy). This function is used as a callback in a the WordPress
 *  `script_loader_tag` to rewrite the <script></script> node with the appropriate
 *  attributes for both SRI and CrossOrigin, along with the fallback HTML snippet
 *  from the `pipjq_fallback_for_cdn_failure()` function.
 *
 * @param string The original version of the script tag.
 * @param string The handle that was used to register the script associated with
 *               the tag in the first parameter.
 * @param string The contents of the `src` attribute in the tag from the first
 *               parameter.
 *
 * @return string
 */
function pipjq_add_sri_attributes( string $tag, string $handle, string $source ): string
{
  switch( $handle ) {
    case 'pipfrosch-jquery-core':
      $html = pipjq_fallback_for_cdn_failure();
      return '<script src="' . $source . '" integrity="' . PIPJQVSRI . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
      break;
    case 'pipfrosch-jquery-migrate':
      $sri = PIPJQMIGRATESRI;
      if ( substr_count( $source, 'cdnjs.cloudflare.com' ) !== 0 ) {
        $sri = PIPJQMIGRATESRI_CDNJS;
      } else if( substr_count( $source, 'cdn.jsdelivr.net' ) !== 0 ) {
        $sri = PIPJQMIGRATESRI_CDNJS;
      }
      $html = pipjq_fallback_for_cdn_failure( false );
      return '<script src="' . $source . '" integrity="' . $sri . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
  }
  return $tag;
}

/**
 * Add CrossOrigin attribute
 *
 * There are a valid reasons why a webmaster may not want this WordPress plugin
 *  adding the SRI tag. For example, they may have a different plugin that already
 *  does that from a database. In such cases, the CrossOrigin attribute should
 *  still be added, along with the fallback HTML snippet. This function does that.
 *  See the PHPdoc header for pipjq_add_sri_attributes().
 *
 * @param string The original version of the script tag.
 * @param string The handle that was used to register the script associated with
 *               the tag in the first parameter.
 * @param string The contents of the `src` attribute in the tag from the first
 *               parameter.
 *
 * @return string
 */
function pipjq_add_crossorigin_attribute( string $tag, string $handle, string $source ): string
{
  switch( $handle ) {
    case 'pipfrosch-jquery-core':
      $html = pipjq_fallback_for_cdn_failure();
      break;
    case 'pipfrosch-jquery-migrate':
      $html = pipjq_fallback_for_cdn_failure( false );
      break;
    default:
      return $tag;
  }
  return '<script src="' . $source . '" crossorigin="anonymous"></script>' . PHP_EOL . $html;
}

/**
 * Generate src attribute for the script nodes.
 *
 * This function creates the appropriate `src` attribute needed to register the
 *  jQuery and Migrate scripts with WordPress based upon the CDN choice. It returns
 *  an object with those strings as properties, and also a boolean property that
 *  specifies whether or not the `src` attributes are for a CDN.
 *
 * @param string $cdnhost Optional. Defaults to 'localhost'. The name of the CDN host.
 *
 * @return stdClass
 */
function pipjq_script_src( string $cdnhost="localhost" )
{
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

/**
 * Update WordPress Core JavaScript
 *
 * This is the heart of this WordPress plugin. This function will deregister the
 *  versions of jQuery and jQuery Migrate that ship with WordPress Core and
 *  register the more modern versions that ship with this plugin, either locally
 *  or from a CDN, with the appropriate attributes and fallback HTML when served
 *  from a CDN.
 *
 * @return void
 */
function pipjq_update_wpcore_jquery(): void
{
  //get the settings and validate
  $migrate = pipjq_get_option_as_boolean( 'pipjq_migrate' );
  $cdn     = pipjq_get_option_as_boolean( 'pipjq_cdn', false );
  $sri     = pipjq_get_option_as_boolean( 'pipjq_sri' );
  $cdnhost = 'localhost';
  if ( $cdn ) {
    $cdnhost = pipjq_get_cdnhost_option();
  }
  $srcuri = pipjq_script_src( $cdnhost );
  
  //de-register the WordPress jQuery
  wp_deregister_script( 'jquery' );
  wp_deregister_script( 'jquery-migrate' );
  wp_deregister_script( 'jquery-core' );
    
  wp_register_script( 'pipfrosch-jquery-core', $srcuri->jquery, array(), null );
  wp_register_script( 'pipfrosch-jquery-migrate', $srcuri->migrate, array( 'pipfrosch-jquery-core' ), null );
  wp_register_script( 'jquery-migrate', false, array('pipfrosch-jquery-migrate'), null );
  wp_register_script( 'jquery', false, array( 'pipfrosch-jquery-migrate' ), null );
  if ( $migrate ) {
    wp_register_script( 'jquery-core', false, array( 'pipfrosch-jquery-migrate' ), null );
  } else {
    wp_register_script( 'jquery-core', false, array( 'pipfrosch-jquery-core' ), null );
  }
  if ( $srcuri->cdn ) {
    if ( $sri ) {
      add_filter( 'script_loader_tag', 'pipjq_add_sri_attributes', 10, 3 );
    } else {
      add_filter( 'script_loader_tag', 'pipjq_add_crossorigin_attribute', 10, 3 );
    }
  }
}

/* For Settings API */

/**
 * Sanitize checkbox input.
 *
 * This plugin likes the faux boolean options set to be a string of "0" for false
 *  and "1" for true. The form sets a value a "1" when checked. If a string that
 *  evaluates as the integer 1 when recast to integer is supplied, this function
 *  will output the string "1". Any other value and it outputs the string "0".
 *
 * @param mixed $input The string passed to this callback from the WordPress options
 *                     form processing.
 *
 * @return string
 */
function pipjq_sanitize_checkbox( $input ): string
{
  if ( is_bool( $input ) && $input ) {
    return "1";
  }
  if ( ! is_string( $input ) ) {
    return "0";
  }
  $input = sanitize_text_field( $input );
  if ( is_numeric( $input ) ) {
    $num = intval( $input );
    if ( $num === 1 ) {
      return "1";
    }
  }
  return "0";
}

/**
 * Settings form helpers
 *
 * Callback function used by the WordPress core function `add_settings_section()`
 *  to provide some recommendations for the plugin settings.
 *
 * @return void
 */
function pipjq_settings_form_text_helpers(): void
{
  $string  = PHP_EOL . '<p>' . __( 'It is recommended that you enable the', 'pipfrosch-jquery' );
  // Translators: Migrate is in reference to jQuery Migrate plugin
  $string .= ' <em>' . __( 'Use Migrate Plugin', 'pipfrosch-jquery' ) . '</em> ';
  $string .= __( 'option (default)', 'pipfrosch-jquery' ) . '.<br />' . PHP_EOL;
  $string .= __( 'It is recommended that you enable the', 'pipfrosch-jquery' );
  $string .= ' <em>' . __( 'Use Content Distribution Network', 'pipfrosch-jquery' ) . '</em> ';
  $string .= __( 'option', 'pipfrosch-jquery' ) . '.<br />' . PHP_EOL;
  $string .= __( 'It is recommended that you enable the', 'pipfrosch-jquery' );
  $string .= ' <em>' . __( 'Use Subresource Integrity', 'pipfrosch-jquery' ) . '</em> ';
  $string .= __( 'option (default)', 'pipfrosch-jquery' ) . '.</p>' . PHP_EOL;
  echo ( $string );
}

/**
 * Generate checkbox input HTML snippet for the ‘Use Migrate Plugin’ option.
 *
 * @return void
 */
function pipjq_migrate_input_tag(): void
{
  $migrate = pipjq_get_option_as_boolean( 'pipjq_migrate' );
  $checked = '';
  if ( $migrate ) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipjq_migrate" id="pipjq_migrate" value="1"' . $checked . '>';
}

/**
 * Generate checkbox input HTML snippet for the ‘Use Content Distribution Network’ option.
 *
 * @return void
 */
function pipjq_cdn_input_tag(): void
{
  $cdn = pipjq_get_option_as_boolean( 'pipjq_cdn', false );
  $checked = '';
  if ( $cdn ) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipjq_cdn" id="pipjq_cdn" value="1"' . $checked . '>';
}

/**
 * Generate checkbox input HTML snippet for the ‘Use Subresource Integrity’ option.
 *
 * @return void
 */
function pipjq_sri_input_tag(): void
{
  $sri = pipjq_get_option_as_boolean( 'pipjq_sri' );
  $checked = '';
  if ( $sri ) {
    $checked = ' checked="checked"';
  }
  echo '<input type="checkbox" name="pipjq_sri" id="pipjq_sri" value="1"' . $checked . '>';
}

/**
 * Generate select and child option tag HTML snippet for the ‘Select Public CDN Service’ menu.
 *
 * @return void
 */
function pipjq_cdnhost_select_tag(): void
{
  $cdnhost = pipjq_get_cdnhost_option();
  // translators: This array is of proper names and they do not get translated
  $values = array( 'jQuery.com CDN',
                   'CloudFlare CDNJS',
                   'jsDelivr CDN',
                   'Microsoft CDN',
                   'Google CDN' );
  $html = '<select name="pipjq_cdnhost" id="pipjq_cdnhost">' . PHP_EOL;
  foreach( $values as $value ) {
    $selected = '';
    if ( $cdnhost === $value ) {
      $selected = ' selected="selected"';
    }
    $html .= '  <option value="' . $value . '"' . $selected . '>' . $value . '</option>' . PHP_EOL;
  }
  $html .= '</select>' . PHP_EOL;
  echo $html;
}

/**
 * Creates the HTML needed for the Settings API form.
 *
 * This function notifies the administrator what version of the core jQuery library and
 *  jQuery Migrate plugin are made available, as well as what Content Distribution Network
 *  is currently configured to be used. It then creates the HTML <form/> node that an
 *  administrator can use to change the settings associated with this WordPress plugin.
 *  This function is called as a callback by the WordPress core function `add_options_page()`
 *  to make the form available in the Settings menu.
 *
 * @return void
 */
function pipjq_options_page_form(): void
{
  $cdn = pipjq_get_option_as_boolean( 'pipjq_cdn', false );
  $parenthesis = '(' . __( 'disabled', 'pipfrosch-jquery' ) . ')';
  if ( $cdn ) {
    $parenthesis = '(' . __( 'enabled', 'pipfrosch-jquery' ) . ')';
  }
  $cdnhost = pipjq_get_cdnhost_option();
  $s = array( '/CDN$/' , '/CDNJS/' );
  $r = array( '<abbr>CDN</abbr>' , '<abbr>CDNJS</abbr>' );
  $cdnhost = preg_replace($s, $r, $cdnhost);
  $html  = '    <h2>Pipfrosch jQuery ' . __('Plugin Management', 'pipfrosch-jquery') . '</h2>' . PHP_EOL;
  $html .= '    <p>jQuery ' . __( 'Version', 'pipfrosch-jquery') . ': ' . PIPJQV . '<br />' . PHP_EOL;
  // Translators: Migrate is in reference to jQuery Migrate plugin
  $html .= 'jQuery ' . __( 'Migrate Plugin Version', 'pipfrosch-jquery') . ': ' . PIPJQMIGRATE . '<br />' . PHP_EOL;
  if ( defined( 'PIPJQUIV' ) ) {
    $html .= 'jQuery UI ' . __( 'Version', 'pipfrosch-jquery') . ': ' . PIPJQUIV . '<br />' . PHP_EOL;
  }
  $html .= __( 'Current', '') . ' <abbr title="' . esc_attr__( 'Content Distribution Network' , 'pipfrosch-jquery');
  $html .= '">CDN</abbr>: ' . $cdnhost . ' ' . $parenthesis . '</p>' . PHP_EOL;
  $html .= '    <form method="post" action="options.php">' . PHP_EOL;
  echo $html;
  settings_fields( PIPJQ_OPTIONS_GROUP );
  do_settings_sections( PIPJQ_SETTINGS_PAGE_SLUG_NAME );
  $html  = '      <p>' . __( 'Note that the', 'pipfrosch-jquery' ) . ' <em>' . __( 'Use Subresource Integrity', 'pipfrosch-jquery' );
  $html .= '</em> ' . __( 'option only has meaning when', 'pipfrosch-jquery' ) . ' <em>';
  $html .= __( 'Use Content Distribution Network', 'pipfrosch-jquery' ) . '</em> ';
  $html .= __( 'is enabled', 'pipfrosch-jquery') . '.</p>' . PHP_EOL;
  $html .= get_submit_button() . PHP_EOL;
  $html .= '    </form>' . PHP_EOL;
  echo $html;
}

/**
 * Set up the WordPress Settings API.
 *
 * This function is a callback added to the `admin_init` action by the WordPress
 *  Core function `add_action()` function used in the main plugin PHP script.
 *
 * @return void
 */
function pipjq_register_settings(): void
{
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
                        'jQuery Core Options',
                        'pipjq_settings_form_text_helpers',
                        PIPJQ_SETTINGS_PAGE_SLUG_NAME );

  // Translators: Migrate is in reference to jQuery Migrate plugin
  add_settings_field( 'pipjq_migrate',
                      __( 'Use Migrate Plugin' , 'pipfrosch-jquery' ),
                      'pipjq_migrate_input_tag',
                      PIPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PIPJQ_SECTION_SLUG_NAME,
                      array('label_for' => 'pipjq_migrate' ) );

  add_settings_field( 'pipjq_cdn',
                      __( 'Use Content Distribution Network', 'pipfrosch-jquery' ),
                      'pipjq_cdn_input_tag',
                      PIPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PIPJQ_SECTION_SLUG_NAME,
                      array( 'label_for' => 'pipjq_cdn' ) );

  add_settings_field( 'pipjq_sri',
                      __( 'Use Subresource Integrity', 'pipfrosch-jquery' ),
                      'pipjq_sri_input_tag',
                      PIPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PIPJQ_SECTION_SLUG_NAME,
                      array( 'label_for' => 'pipjq_sri' ) );

  // Translators: CDN is an abbreviation and should not be translated
  add_settings_field( 'pipjq_cdnhost',
                      __( 'Select Public CDN Service', 'pipfrosch-jquery' ),
                      'pipjq_cdnhost_select_tag',
                      PIPJQ_SETTINGS_PAGE_SLUG_NAME,
                      PIPJQ_SECTION_SLUG_NAME,
                      array( 'label_for' => 'pipjq_cdnhost' ) );
}

/**
 * Registers the Settings API form for changing options.
 *
 * Basically just wraps the WordPress Core function `add_options_page()`
 *  in a callback added to the `admin_menu` action by the WordPress
 *  Core function `add_action()` function used in the main plugin PHP script.
 *
 * @return void
 */
function pipjq_register_options_page(): void
{
  add_options_page( 'jQuery ' . PIPJQV . ' ' . __( 'Options', 'pipfrosch-jquery' ),
                    'jQuery ' . __( 'Options', 'pipfrosch-jquery' ),
                    'manage_options',
                    'pipfrosch_jquery',
                    'pipjq_options_page_form' );
}