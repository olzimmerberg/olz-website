<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/config/doctrine_db.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'buttonkarten' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

echo OlzHeader::render([
    'title' => "Karten",
    'description' => "Die OL-Karten, die die OL Zimmerberg aufnimmt, unterhält und verkauft.",
]);

$db_table = 'karten';

$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
    $id = $_GET['id'] ?? null;
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='karten.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/karten_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>";
include __DIR__.'/karten_l.php';
echo "</div>";

echo OlzFooter::render();
