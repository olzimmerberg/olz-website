<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    session_start();

    require_once __DIR__.'/admin/olz_init.php';
    require_once __DIR__.'/admin/olz_functions.php';
    $html_titel = " - Forum";
    include __DIR__.'/components/page/olz_header/olz_header.php';
}

$db_table = 'forum';

$button_name = 'button'.$db_table;
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='forum.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/forum_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='forum.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>";
include __DIR__.'/forum_l.php';
echo "</form>
</div>
";

if (!defined('CALLED_THROUGH_INDEX')) {
    include __DIR__.'/components/page/olz_footer/olz_footer.php';
}
