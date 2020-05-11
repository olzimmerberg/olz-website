<?php

// =============================================================================
// Die Informationsseite für Anfänger, Einsteiger, Neulinge.
// =============================================================================

require_once __DIR__.'/model/Role.php';
require_once __DIR__.'/components/users/olz_user_info_card/olz_user_info_card.php';

$role_repo = $entityManager->getRepository(Role::class);
$nachwuchs_role = $role_repo->findOneBy(['username' => 'nachwuchs-ausbildung']);

echo "<h3>Ansprechperson</h3>
<div style='padding:0px 10px 0px 10px;'>";
$nachwuchs_assignees = $nachwuchs_role->getUsers();
foreach ($nachwuchs_assignees as $nachwuchs_assignee) {
    echo olz_user_info_card($nachwuchs_assignee);
}
echo "</div>";
