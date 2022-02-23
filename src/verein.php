<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';
require_once __DIR__.'/utils/client/HttpUtils.php';
require_once __DIR__.'/utils/env/EnvUtils.php';
$env_utils = EnvUtils::fromEnv();
$logger = $env_utils->getLogsUtils()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLogger($logger);
$http_utils->validateGetParams([
    'ressort' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

if (isset($_GET['ressort'])) {
    $role_username = $_GET['ressort'];
    $role_repo = $entityManager->getRepository(Role::class);
    $role = $role_repo->findOneBy(['username' => $role_username]);

    if (!$role) {
        require_once __DIR__.'/utils/client/HttpUtils.php';
        HttpUtils::fromEnv()->dieWithHttpError(404);
    }

    // TODO: Remove again, after all ressort descriptions have been updated.
    // This is just temporary logic!
    $no_robots = ($role->getGuide() === '');

    echo olz_header([
        'title' => $role->getName(),
        'description' => "Ressort {$role->getName()} der OL Zimmerberg.",
        'norobots' => $no_robots,
    ]);

    require_once __DIR__.'/components/verein/olz_role_page/olz_role_page.php';
    echo olz_role_page(['role' => $role]);

    echo olz_footer();
} else {
    echo olz_header([
        'title' => "Kontakt",
        'description' => "Die wichtigsten Kontaktadressen und eine Liste aller Vereinsorgane der OL Zimmerberg.",
    ]);

    echo "<div id='content_double'>";
    require_once __DIR__.'/components/verein/olz_organigramm/olz_organigramm.php';
    echo olz_organigramm();
    echo "</div>";

    echo olz_footer();
}
