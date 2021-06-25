<?php

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/fields/IntegerField.php';
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
    new IntegerField('id', ['allow_null' => true]),
    new StringField('buttonforum', ['allow_null' => true]),
], $_GET, ['just_log' => true]);

echo olz_header([
    'title' => "Forum",
    'description' => "Ein Forum für Nutzer-Beiträge über alles rund um den OL und/oder die OL Zimmerberg.",
]);

$db_table = 'forum';
$id = $_GET['id'] ?? null;
$uid = $_POST['uid'] ?? null;

$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

echo "<div id='content_rechts'>";
include __DIR__.'/forum_r.php';
echo "</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='forum.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
include __DIR__.'/forum_l.php';
echo "</form>
</div>
";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
