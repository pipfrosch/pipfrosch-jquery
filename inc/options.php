<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }
//if ( ! current_user_can( 'administrator' ) ) { exit; }

function pipfrosch_jquery_options_page() {
?>
  <div>
    <h2>Pipfrosch jQuery Options</h1>
    <form method="post" action="options.php">
<?php settings_fields(  ); ?>
      <h3>End of the world as we know it</h3>
    </form>
  </div>


<?php
}
