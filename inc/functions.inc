<?php

//sanitize plugin options before setting/updating them
function pipfrosch_jquery_set_boolean_option( string $option, bool $value ) {
  if (! is_string( $option ) {
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
    set_option( $option, boolval( $value ) );
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


























