<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/config/init.php';

    session_start_if_cookie_set();

    require_once __DIR__.'/admin/olz_functions.php';
    require_once __DIR__.'/components/page/olz_header/olz_header.php';
    echo olz_header([
        'title' => "Fragen & Antworten",
        'description' => "Antworten auf die wichtigsten Fragen rund um den OL und die OL Zimmerberg.",
    ]);
}

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='fragen_und_antworten.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/fragen_und_antworten_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='fragen_und_antworten.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>";
include __DIR__.'/fragen_und_antworten_l.php';
echo "</form>
</div>
";

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
    echo olz_footer();
}
