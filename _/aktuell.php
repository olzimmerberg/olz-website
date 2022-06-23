<?php

use Olz\News\Components\OlzNewsDetail\OlzNewsDetail;
use Olz\News\Components\OlzNewsList\OlzNewsList;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

global $db;
require_once __DIR__.'/config/init.php';
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/config/paths.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'archiv' => new FieldTypes\BooleanField(['allow_null' => true]),
    'buttonaktuell' => new FieldTypes\StringField(['allow_null' => true]),
    'filter' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

$id = $_GET['id'] ?? null;

require_once __DIR__.'/components/page/olz_header/olz_header.php';

require_once __DIR__.'/file_tools.php';
require_once __DIR__.'/image_tools.php';

$db_table = 'aktuell';

if ($id === null) {
    echo OlzNewsList::render();
} else {
    echo OlzNewsDetail::render();
}
