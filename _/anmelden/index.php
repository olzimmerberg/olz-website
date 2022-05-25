<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

global $db;
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/server.php';

session_start_if_cookie_set();

require_once __DIR__.'/../admin/olz_functions.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger('anmelden');
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
], $_GET);

require_once __DIR__.'/../components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => 'Anmelden',
    'description' => "Hier kann man sich für OLZ-Anlässe anmelden.",
]);

$js_path = "{$_CONFIG->getCodePath()}anmelden/jsbuild/main.min.js";
$js_modified = is_file($js_path) ? filemtime($js_path) : 0;
echo "<div id='content_double'><div id='react-root'>Lädt...</div></div>";
echo "<script type='text/javascript' src='{$_CONFIG->getCodeHref()}anmelden/jsbuild/main.min.js?modified={$js_modified}'></script>";

require_once __DIR__.'/../components/page/olz_footer/olz_footer.php';
echo olz_footer();
