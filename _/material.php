<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([], $_GET);

echo OlzHeader::render([
    'title' => "Material & Kleider",
    'description' => "Material und OLZ-Kleider, die die OL Zimmerberg vermietet bzw. verkauft.",
]);

echo "<div class='content-full'>
<form name='Formularl' method='post' action='material.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/material_d.php';
echo "</div>
</form>
</div>";

echo OlzFooter::render();
