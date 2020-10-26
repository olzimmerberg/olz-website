<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    session_start();

    require_once __DIR__.'/admin/olz_init.php';
    require_once __DIR__.'/admin/olz_functions.php';
    $html_titel = " - Kontakt";
    include __DIR__.'/components/page/olz_header/olz_header.php';
}

echo "<div id='content_double'>
<form name='Formularl' method='post' action='kontakt.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/kontakt_d.php';
echo "</div>
</form>
</div>";

if (!defined('CALLED_THROUGH_INDEX')) {
    include __DIR__.'/components/page/olz_footer/olz_footer.php';
}
