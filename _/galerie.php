<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

$db = DbUtils::fromEnv()->getDb();

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'jahr' => new FieldTypes\IntegerField(['allow_null' => true]),
    'archiv' => new FieldTypes\BooleanField(['allow_null' => true]),
    'buttongalerie' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

if (isset($_GET['datum']) || isset($_GET['foto'])) {
    $http_utils->dieWithHttpError(404);
}

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();

$html_title = "Galerie";
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT titel FROM galerie WHERE id='{$id}'";
    $res = $db->query($sql);
    if ($res->num_rows == 0) {
        $http_utils->dieWithHttpError(404);
    }
    while ($row = $res->fetch_assoc()) {
        $html_title = $row['titel'];
    }
    $aktuell_id = $id + 1200;
    $new_url = "{$code_href}news/{$aktuell_id}";
    http_response_code(301);
    header("Location: {$new_url}");
} else {
    $news_filter_utils = NewsFilterUtils::fromEnv();
    $filter = $news_filter_utils->getDefaultFilter();
    $filter['format'] = 'galerie';
    $enc_json_filter = urlencode(json_encode($filter));
    $new_url = "{$code_href}news?filter={$enc_json_filter}";
    http_response_code(301);
    header("Location: {$new_url}");
}

echo OlzHeader::render([
    'title' => $html_title,
    'description' => "Bilder und Videos von Anlässen der OL Zimmerberg.",
    'norobots' => true,
    'canonical_url' => $new_url,
]);

$db_table = 'galerie';
$id = $_GET['id'] ?? null;

$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

echo "
<div class='content-right'>
<form name='Formularr' method='post' action='galerie.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/galerie_r.php';
echo "</div>
</form>
</div>
<div class='content-middle'>
<form name='Formularl' method='post' action='galerie.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
include __DIR__.'/galerie_l.php';
echo "</form>
</div>
";

echo OlzFooter::render();
