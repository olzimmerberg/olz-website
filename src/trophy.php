<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/config/init.php';

    session_start_if_cookie_set();

    require_once __DIR__.'/admin/olz_functions.php';
    require_once __DIR__.'/components/page/olz_header/olz_header.php';
    echo olz_header([
        'title' => "Trophy",
        'description' => "Orientierungslauf-Mini-Wettkämpfe, offen für Alle, in den Dörfern und Städten unseres Vereinsgebiets organisiert durch die OL Zimmerberg.",
    ]);
}

echo "<div id='content_double'>
<form name='Formularl' method='post' action='trophy.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/trophy_d.php';
echo "</div>
</form>
</div>";

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
    echo olz_footer();
}
