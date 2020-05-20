<?php
// currently broken

if ( ! defined( 'ABSPATH' ) ) { exit; }

function pipfrosch_jquery_options_page() {
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
  settings_fields( 'pipfrosch_jquery_options' );
  do_settings_sections( 'pipfrosch_jquery_options' );
  $html  = '      <p>Note that the ‘Use Subresource Integrity’ option only has meaning when ‘Use Content Distribution Network’ is enabled.</p>' . PHP_EOL;
  $html .= get_submit_button();
  $html .= '    </form>';
  echo $html;
}


