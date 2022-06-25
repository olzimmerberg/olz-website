<?php

// =============================================================================
// Die Informationsseite für Anfänger, Einsteiger, Neulinge.
// =============================================================================

use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;
use Olz\Entity\Role;

$role_repo = $entityManager->getRepository(Role::class);
$nachwuchs_role = $role_repo->findOneBy(['username' => 'nachwuchs-kontakt']);

echo "<h3>Ansprechperson</h3>
<div style='padding:0px 10px 0px 10px;'>";
$nachwuchs_assignees = $nachwuchs_role->getUsers();
foreach ($nachwuchs_assignees as $nachwuchs_assignee) {
    echo OlzUserInfoCard::render(['user' => $nachwuchs_assignee]);
}
echo "</div>";
