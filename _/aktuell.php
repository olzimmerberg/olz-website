<?php

use Olz\News\Components\OlzNewsDetail\OlzNewsDetail;
use Olz\News\Components\OlzNewsList\OlzNewsList;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';
require_once __DIR__.'/config/paths.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'archiv' => new FieldTypes\BooleanField(['allow_null' => true]),
    'buttonaktuell' => new FieldTypes\StringField(['allow_null' => true]),
    'filter' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

$id = $_GET['id'] ?? null;

$db_table = 'aktuell';

if ($id === null) {
    echo OlzNewsList::render();
} else {
    echo OlzNewsDetail::render();
}
