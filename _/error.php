<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

require_once __DIR__.'/config/init.php';

http_response_code(404);
session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "Fehler",
]);

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='error.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/error_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='error.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
include __DIR__.'/error_l.php';
echo "</form>
</div>
";

echo OlzFooter::render();
