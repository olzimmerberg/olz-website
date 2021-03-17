<?php

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';

$role_username = $_GET['abteilung'];
$role_repo = $entityManager->getRepository(Role::class);
$role = $role_repo->findOneBy(['username' => $role_username]);

if (!$role) {
    http_response_code(404);
    return;
}

$role_name = $role->getName();
$role_description = $role->getDescription();

echo olz_header([
    'title' => $role_name,
    'description' => $role_description,
]);

echo "<div id='content_double'>";
echo "<h1>{$role_name}</h1>";
echo "<p><b>{$role_description}</b></p>";
echo $role->getPage();
echo "</div>";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
