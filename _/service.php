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
    'buttonlinks' => new FieldTypes\StringField(['allow_null' => true]),
    'buttondownloads' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

echo OlzHeader::render([
    'title' => "Service",
    'description' => "Diverse Online-Tools rund um OL und die OL Zimmerberg.",
]);

echo "<div id='content_rechts'>";
include __DIR__.'/service_r.php';
echo "</div>
<div id='content_mitte'>";
include __DIR__.'/service_l.php';
echo "</div>";

echo OlzFooter::render();
