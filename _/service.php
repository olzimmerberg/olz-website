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
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'buttonlinks' => new FieldTypes\StringField(['allow_null' => true]),
    'buttondownloads' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

echo OlzHeader::render([
    'title' => "Service",
    'description' => "Diverse Online-Tools rund um OL und die OL Zimmerberg.",
]);

echo "<div class='content-full'>";
include __DIR__.'/service_d.php';
echo "</div>";

echo OlzFooter::render();
