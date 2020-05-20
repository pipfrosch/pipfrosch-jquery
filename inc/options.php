<?php
// currently broken

if ( ! defined( 'ABSPATH' ) ) { exit; }

function pipfrosch_jquery_options_page() {
  $cdn = get_option( 'pipfrosch_jquery_cdn', false );
  if ( ! is_bool( $cdn ) ) {
    $cdn = false;
  }
  $parenthesis = '(disabled)';
  if ( $cdn ) {
    $parenthesis = '(enabled)';
  }
  // right now only one option
  //$cdnhost = 'code.jquery.com';
  $cdnhost = pipfrosch_press_sanitize_cdnhost( get_option( 'pipfrosch_jquery_cdnhost' ) );
  $s = array( '/CDN$/' , '/CDNJS/' );
  $r = array( '<abbr>CDN</abbr>' , '<abbr>CDNJS</abbr>' );
  $cdnhost = preg_replace($s, $r, $cdnhost);
?>
    <h2>Pipfrosch jQuery Plugin Management</h2>
    <p>jQuery Version: <?php echo PIPJQV; ?></p>
    <p>jQuery Migrate Plugin Version: <?php echo PIPJQMIGRATE; ?></p>
    <p>Current <abbr title="Content Distribution Network">CDN</abbr>: <?php echo $cdnhost . ' ' . $parenthesis;?></p>
    <form method="post" action="options.php">
<?php
settings_fields( 'pipfrosch_jquery_options' );
do_settings_sections( 'pipfrosch_jquery_options' );
?>
      <p>Note that the ‘Use Subresource Integrity’ option only has meaning when ‘Use Content Distribution Network’ is enabled.</p>
      <?php  submit_button(); ?>
    </form>
<?php
}
