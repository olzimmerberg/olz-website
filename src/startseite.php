<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/config/init.php';

    session_start_if_cookie_set();

    require_once __DIR__.'/admin/olz_functions.php';
    require_once __DIR__.'/components/page/olz_header/olz_header.php';
    require_once __DIR__.'/components/page/olz_organization_data/olz_organization_data.php';
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
        new StringField('buttonbild_der_woche', ['allow_null' => true]),
    ], $_GET, ['just_log' => true]);

    echo olz_header([
        'description' => "Eine Übersicht der Neuigkeiten und geplanten Anlässe der OL Zimmerberg.",
        'additional_headers' => [
            olz_organization_data([]),
        ],
    ]);
}

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

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
    echo olz_footer();
}
