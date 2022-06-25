<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'anfrage' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

echo OlzHeader::render([
    'title' => "Suche",
    'description' => "Stichwort-Suche auf der Website der OL Zimmerberg.",
    'norobots' => true,
]);

$search_key = $_GET['anfrage'];

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='suche.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/startseite_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='suche.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
include __DIR__.'/suche_l.php';
echo "</form>
</div>
";

echo OlzFooter::render();
