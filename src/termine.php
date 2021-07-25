<?php

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
require_once __DIR__.'/fields/IntegerField.php';
require_once __DIR__.'/fields/StringField.php';
require_once __DIR__.'/utils/client/HttpUtils.php';
require_once __DIR__.'/utils/env/EnvUtils.php';
require_once __DIR__.'/utils/TermineUtils.php';

$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$validated_get_params = $http_utils->validateGetParams([
    'filter' => new StringField(['allow_null' => true]),
    'id' => new IntegerField(['allow_null' => true]),
    'buttontermine' => new StringField(['allow_null' => true]),
], $_GET);

$current_filter = json_decode($_GET['filter'] ?? '{}', true);
$termine_utils = TermineUtils::fromEnv();

if (!$termine_utils->isValidFilter($current_filter)) {
    $enc_json_filter = urlencode(json_encode($termine_utils->getDefaultFilter()));
    require_once __DIR__.'/utils/client/HttpUtils.php';
    HttpUtils::fromEnv()->redirect("termine.php?filter={$enc_json_filter}", 308);
}

echo olz_header([
    'title' => $termine_utils->getTitleFromFilter($current_filter),
    'description' => "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.",
]);

$enc_current_filter = urlencode(json_encode($current_filter));

echo "
<div id='content_rechts'>
<div>";
include __DIR__.'/termine_r.php';
echo "</div>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='termine.php?filter={$enc_current_filter}#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";
include __DIR__.'/termine_l.php';
echo "</form>
</div>
";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
