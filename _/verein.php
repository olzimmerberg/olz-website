<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Verein\OlzOrganigramm\OlzOrganigramm;
use Olz\Components\Verein\OlzRolePage\OlzRolePage;
use Olz\Entity\Role;
use Olz\Utils\DbUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$entityManager = DbUtils::fromEnv()->getEntityManager();
$logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
$http_utils = HttpUtils::fromEnv();
$http_utils->setLog($logger);
$http_utils->validateGetParams([
    'ressort' => new FieldTypes\StringField(['allow_null' => true]),
], $_GET);

if (isset($_GET['ressort'])) {
    $role_username = $_GET['ressort'];
    $role_repo = $entityManager->getRepository(Role::class);
    $role = $role_repo->findOneBy(['username' => $role_username]);

    if (!$role) {
        HttpUtils::fromEnv()->dieWithHttpError(404);
    }

    // TODO: Remove again, after all ressort descriptions have been updated.
    // This is just temporary logic!
    $no_robots = ($role->getGuide() === '');

    echo OlzHeader::render([
        'back_link' => "{$code_href}verein.php",
        'title' => "{$role->getName()} - Verein",
        'description' => "Ressort {$role->getName()} der OL Zimmerberg.",
        'norobots' => $no_robots,
    ]);

    echo OlzRolePage::render(['role' => $role]);

    echo OlzFooter::render();
} else {
    echo OlzHeader::render([
        'title' => "Verein",
        'description' => "Die wichtigsten Kontaktadressen und eine Liste aller Vereinsorgane der OL Zimmerberg.",
    ]);

    echo "<div class='content-full'>";
    echo OlzOrganigramm::render();
    echo "</div>";

    echo OlzFooter::render();
}
