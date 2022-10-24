<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "Fragen & Antworten",
    'description' => "Antworten auf die wichtigsten Fragen rund um den OL und die OL Zimmerberg.",
]);

echo "<div class='content-right'>";
include __DIR__.'/fragen_und_antworten_r.php';
echo "</div>
<div class='content-middle'>";
include __DIR__.'/fragen_und_antworten_l.php';
echo "</div>";

echo OlzFooter::render();
