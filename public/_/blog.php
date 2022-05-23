<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';
require_once __DIR__.'/utils/client/HttpUtils.php';
require_once __DIR__.'/utils/env/EnvUtils.php';
$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'id' => new FieldTypes\IntegerField(['allow_null' => true]),
    'buttonblog' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

echo olz_header([
    'title' => "Leistungssport",
    'description' => "Beiträge der Spitzensportler und der Leistungssport-Trainingsgruppe \"Team Gold\" der OL Zimmerberg.",
]);

require_once __DIR__.'/file_tools.php';
require_once __DIR__.'/image_tools.php';

$db_table = 'blog';
$def_folder = 'downloads';

$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
    $id = $_GET['id'] ?? null;
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='blog.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/blog_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='blog.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
include __DIR__.'/blog_l.php';
echo "</form>
</div>
";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
