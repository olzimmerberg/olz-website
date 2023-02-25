<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'code' => new FieldTypes\StringField(['allow_null' => true]),
    'buttonforum' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();
$news_filter_utils = NewsFilterUtils::fromEnv();
$filter = $news_filter_utils->getDefaultFilter();
$filter['format'] = 'forum';
$enc_json_filter = urlencode(json_encode($filter));
$new_url = "{$code_href}aktuell.php?filter={$enc_json_filter}";
header("Location: {$new_url}");

echo OlzHeader::render([
    'title' => "Forum",
    'description' => "Ein Forum für Nutzer-Beiträge über alles rund um den OL und/oder die OL Zimmerberg.",
    'norobots' => true,
    'canonical_url' => $new_url,
]);

$db_table = 'forum';
$id = $_GET['id'] ?? null;
$uid = $_POST['uid'] ?? null;

$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

echo "<div class='content-right'>";
include __DIR__.'/forum_r.php';
echo "</div>
<div class='content-middle'>
<form name='Formularl' method='post' action='forum.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
include __DIR__.'/forum_l.php';
echo "</form>
</div>
";

echo OlzFooter::render();
