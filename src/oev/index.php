<?php

global $db;
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/paths.php';

session_start_if_cookie_set();

require_once __DIR__.'/../admin/olz_functions.php';

require_once __DIR__.'/../fields/BooleanField.php';
require_once __DIR__.'/../fields/IntegerField.php';
require_once __DIR__.'/../fields/StringField.php';
require_once __DIR__.'/../utils/client/HttpUtils.php';
require_once __DIR__.'/../utils/env/EnvUtils.php';
$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'id' => new IntegerField(['allow_null' => true]),
    'archiv' => new BooleanField(['allow_null' => true]),
    'buttonaktuell' => new StringField(['allow_null' => true]),
    'filter' => new StringField(['allow_null' => true]),
], $_GET);

$id = $_GET['id'] ?? null;

require_once __DIR__.'/../components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "ÖV-Tool",
    'description' => "Tool für die Suche von gemeinsamen ÖV-Verbindungen.",
]);

echo <<<'ZZZZZZZZZZ'
<div id='content_double'>
    <div id='oev-root'></div>
    <script>initOlzTransportConnectionSearch();</script>
</div>
ZZZZZZZZZZ;

require_once __DIR__.'/../components/page/olz_footer/olz_footer.php';
echo olz_footer();
