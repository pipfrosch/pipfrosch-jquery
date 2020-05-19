<?php
// currently broken

if ( ! defined( 'ABSPATH' ) ) { exit; }
//if ( ! current_user_can( 'administrator' ) ) { exit; }

function pipfrosch_jquery_options_page() {
?>
    <h2>Pipfrosch jQuery Options</h2>
    <p>jQuery Version: <?php echo PIPJQV; ?></p>
    <p>jQuery Migrate Plugin Version: <?php echo PIPJQMIGRATE; ?></p>
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
