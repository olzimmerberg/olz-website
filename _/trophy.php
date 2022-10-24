<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "Trophy",
    'description' => "Orientierungslauf-Mini-Wettkämpfe, offen für Alle, in den Dörfern und Städten unseres Vereinsgebiets organisiert durch die OL Zimmerberg.",
]);

echo "<div class='content-full'>
<form name='Formularl' method='post' action='trophy.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/trophy_d.php';
echo "</div>
</form>
</div>";

echo OlzFooter::render();
