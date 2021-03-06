<?php

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/fields/EnumField.php';
require_once __DIR__.'/fields/StringField.php';
require_once __DIR__.'/model/index.php';
require_once __DIR__.'/utils/client/HttpUtils.php';
require_once __DIR__.'/utils/env/EnvUtils.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
// TODO: Remove `just_log` once we are sure we know all the GET variables.
$http_utils->validateGetParams([
    new StringField('typ', ['allow_null' => true]),
    new EnumField('lang', ['allow_null' => true, 'allowed_values' => ['de', 'fr']]),
], $_GET, ['just_log' => true]);

echo olz_header([
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

echo olz_footer();
