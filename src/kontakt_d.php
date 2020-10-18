<?php

// =============================================================================
// Das Organigramm unseres Vereins.
// =============================================================================

require_once __DIR__.'/config/paths.php';
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/model/Role.php';
require_once __DIR__.'/model/RoleRepository.php';
require_once __DIR__.'/model/User.php';
require_once __DIR__.'/model/UserRepository.php';
require_once __DIR__.'/components/users/olz_user_info_with_popup/olz_user_info_with_popup.php';

$role_repo = $entityManager->getRepository(Role::class);
$user_repo = $entityManager->getRepository(User::class);

$colwid = 111;
$root_roles = $role_repo->getRolesWithParent(null);
$org = "<div style='width:100%; overflow-x:scroll;'><table style='table-layout:fixed; width:".($colwid * count($root_roles))."px;'>";
foreach ($root_roles as $root_role) {
    $root_role_name = nl2br($root_role->getName());
    $org .= "<td style='width:".$colwid."px; vertical-align:top;'>";
    $org .= "<div id='link-role-{$root_role->getId()}' style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;'>";
    $org .= "<h6 style='font-weight:bold; min-height:36px;'>{$root_role_name}</h6>";
    $root_role_assignees = $root_role->getUsers();
    foreach ($root_role_assignees as $root_role_assignee) {
        $org .= olz_user_info_with_popup($root_role_assignee, 'name_picture');
    }
    $org .= "</div>";
    $charge_roles = $role_repo->getRolesWithParent($root_role->getId());
    foreach ($charge_roles as $charge_role) {
        $charge_role_name = nl2br($charge_role->getName());
        $org .= "<div style='text-align:center; height:20px; overflow:hidden;'><span style='border-left:1px solid #000000; font-size:20px;'></span></div>";
        $org .= "<div id='link-role-{$charge_role->getId()}' style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;'>";
        $org .= "<h6>{$charge_role_name}</h6>";
        $charge_role_assignees = $charge_role->getUsers();
        foreach ($charge_role_assignees as $charge_role_assignee) {
            $org .= olz_user_info_with_popup($charge_role_assignee, 'name');
        }
        $subcharge_roles = $role_repo->getRolesWithParent($charge_role->getId());
        foreach ($subcharge_roles as $subcharge_role) {
            $subcharge_role_name = nl2br($subcharge_role->getName());
            $org .= "<div id='link-role-{$subcharge_role->getId()}' style='text-align:center; font-style:italic;'>{$subcharge_role_name}</div>";
            $subcharge_role_assignees = $subcharge_role->getUsers();
            foreach ($subcharge_role_assignees as $subcharge_role_assignee) {
                $org .= olz_user_info_with_popup($subcharge_role_assignee, 'name');
            }
        }
        $org .= "</div>";
    }
    $org .= "</td>";
}
$org .= "</table></div>";

echo "<div id='organigramm'><h2>Häufig gesucht</h2>
<div><b><a href='javascript:highlight_organigramm(&quot;link-role-5&quot;)' class='linkint'>Präsident</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-role-6&quot;)' class='linkint'>Mitgliederverwaltung</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-role-18&quot;)' class='linkint'>Kartenverkauf</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-role-19&quot;)' class='linkint'>Kleiderverkauf</a></b></div>
<div><b>PC-Konto: 85-256448-8</b></div>
<h2>Organigramm OL Zimmerberg</h2>".$org."</div>";
