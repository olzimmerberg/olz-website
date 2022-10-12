<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
// TODO: Remove `just_log` once we are sure we know all the GET variables.
$http_utils->validateGetParams([
    'typ' => new FieldTypes\StringField(['allow_null' => true]),
    'lang' => new FieldTypes\EnumField(['allow_null' => true, 'allowed_values' => ['de', 'fr']]),
], $_GET, ['just_log' => true]);

echo OlzHeader::render([
    'title' => "Zimmerberg OL",
    'description' => "Informationen zum jährlich stattfindenden Zimmerberg OL.",
]);

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='index.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/zimmerbergol_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='index.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
include __DIR__.'/zimmerbergol_l.php';
echo "</form>
</div>
";

echo OlzFooter::render();
