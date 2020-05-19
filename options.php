<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! current_user_can( 'administrator' ) ) { exit; }

function pipfrosch_jquery_setoption( string $option, bool $value ) {
  $string = 'false';
  if ( $value ) {
    $string = 'true';
  }
  if ( ! get_option( $option ) ) {
    //option not yet set
    set_option( $option, $string );
    return;
  }
  update_option( $option, $string );
}

function pipfrosch_jquery_settings() {
  register_setting( 'options',
                    'pipfrosch_jquery_migrate',
                    array('type' => 'boolean',
                          'description' => 'Load jQuery migrate plugin',
                          'sanitize_callback' => 'rest_sanitize_boolean',
                          'show_in_rest' => false,
                          'default' => true ) );
  register_setting( 'options',
                    'pipfrosch_jquery_cdn',
                    array('type' => 'boolean',
                          'description' => 'Use code.jqeery.com CDN for jQuery',
                          'sanitize_callback' => 'rest_sanitize_boolean',
                          'show_in_rest' => false,
                          'default' => false ) );
  register_setting( 'options',
                    'pipfrosch_jquery_sri',
                    array('type' => 'boolean',
                          'description' => 'Use Subresource Integrity when using jQuery CDN',
                          'sanitize_callback' => 'rest_sanitize_boolean',
                          'show_in_rest' => false,
                          'default' => true ) );
}

//initiate defaults
$pipfrosch_jquery_migrate = true;
$pipfrosch_jquery_option = get_option( 'pipfrosch_jquery_migrate' );
if ( is_string( $pipfrosch_jquery_option ) && ( trim( strtolower( $pipfrosch_jquery_option ) ) === "false" ) ) {
  $pipfrosch_jquery_migrate = false;
}
$pipfrosch_jquery_cdn = false;
$pipfrosch_jquery_option = get_option( 'pipfrosch_jquery_cdn' );
if ( is_string( $pipfrosch_jquery_option ) && ( trim( strtolower( $pipfrosch_jquery_option ) ) === "true" ) ) {
  $pipfrosch_jquery_cdn = true;
}
$pipfrosch_jquery_sri = true;
$pipfrosch_jquery_option = get_option( 'pipfrosch_jquery_sri' );
if ( is_string( $pipfrosch_jquery_option ) && ( trim( strtolower( $pipfrosch_jquery_option ) ) === "false" ) ) {
  $pipfrosch_jquery_sri = false;
}

?>
<div class="wrap">
<h1>Pipfrosch jQuery Options</h1>
<form method="post" action="options.php">



















// save the options
pipfrosch_jquery_setoption( 'pipfrosch_jquery_migrate', $pipfrosch_jquery_migrate );
pipfrosch_jquery_setoption( 'pipfrosch_jquery_cdn', $pipfrosch_jquery_cdn );
pipfrosch_jquery_setoption( 'pipfrosch_jquery_sri', $pipfrosch_jquery_sri );


?>
