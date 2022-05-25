<?php

use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
require_once __DIR__.'/config/doctrine_db.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'buttonbild_der_woche' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

echo olz_header([
    'description' => "Eine Übersicht der Neuigkeiten und geplanten Anlässe der OL Zimmerberg.",
]);

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='startseite.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/startseite_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>";
include __DIR__.'/startseite_l.php';
echo "</div>";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
