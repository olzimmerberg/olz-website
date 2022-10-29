<?php

namespace Olz\Components\Verein\OlzOrganigramm;

use Olz\Components\Users\OlzUserInfoWithPopup\OlzUserInfoWithPopup;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Utils\AuthUtils;
use Olz\Utils\DbUtils;

class OlzOrganigramm {
    public static function render($args = []) {
        require_once __DIR__.'/../../../../_/config/paths.php';

        $auth_utils = AuthUtils::fromEnv();
        $has_all_permissions = $auth_utils->hasPermission('all');

        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $role_repo = $entityManager->getRepository(Role::class);
        $user_repo = $entityManager->getRepository(User::class);

        $colwid = 111;
        $root_roles = $role_repo->getRolesWithParent(null);
        $org = "<div style='width:100%; overflow-x:scroll;'><table style='table-layout:fixed; width:".($colwid * count($root_roles))."px;'>";
        foreach ($root_roles as $root_role) {
            $root_role_name = nl2br($root_role->getName());
            $root_role_username = $root_role->getUsername();
            $root_role_id = ($has_all_permissions ? "<br/>(ID: {$root_role->getId()})" : '');
            $org .= "<td style='width:".$colwid."px; vertical-align:top;'>";
            $org .= "<div id='link-role-{$root_role->getId()}' style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;'>";
            $org .= "<h6 style='font-weight:bold; min-height:36px;'><a href='verein.php?ressort={$root_role_username}'>{$root_role_name}</a>{$root_role_id}</h6>";
            $root_role_assignees = $root_role->getUsers();
            foreach ($root_role_assignees as $root_role_assignee) {
                $org .= OlzUserInfoWithPopup::render([
                    'user' => $root_role_assignee,
                    'mode' => 'name_picture',
                ]);
            }
            $org .= "</div>";
            $charge_roles = $role_repo->getRolesWithParent($root_role->getId());
            foreach ($charge_roles as $charge_role) {
                $charge_role_name = nl2br($charge_role->getName());
                $charge_role_username = nl2br($charge_role->getUsername());
                $charge_role_id = ($has_all_permissions ? "<br/>(ID: {$charge_role->getId()})" : '');
                $org .= "<div style='text-align:center; height:20px; overflow:hidden;'><span style='border-left:1px solid #000000; font-size:20px;'></span></div>";
                $org .= "<div id='link-role-{$charge_role->getId()}' style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;'>";
                $org .= "<h6 style='font-weight:bold;'><a href='verein.php?ressort={$charge_role_username}'>{$charge_role_name}</a>{$charge_role_id}</h6>";
                $charge_role_assignees = $charge_role->getUsers();
                foreach ($charge_role_assignees as $charge_role_assignee) {
                    $org .= OlzUserInfoWithPopup::render([
                        'user' => $charge_role_assignee,
                        'mode' => 'name',
                    ]);
                }
                $subcharge_roles = $role_repo->getRolesWithParent($charge_role->getId());
                foreach ($subcharge_roles as $subcharge_role) {
                    $subcharge_role_name = nl2br($subcharge_role->getName());
                    $subcharge_role_username = nl2br($subcharge_role->getUsername());
                    $subcharge_role_id = ($has_all_permissions ? "<br/>(ID: {$subcharge_role->getId()})" : '');
                    $org .= "<div id='link-role-{$subcharge_role->getId()}' style='margin-top:4px; text-align:center; font-style:italic;'><a href='verein.php?ressort={$subcharge_role_username}'>{$subcharge_role_name}</a>{$subcharge_role_id}</div>";
                    $subcharge_role_assignees = $subcharge_role->getUsers();
                    foreach ($subcharge_role_assignees as $subcharge_role_assignee) {
                        $org .= OlzUserInfoWithPopup::render([
                            'user' => $subcharge_role_assignee,
                            'mode' => 'name',
                        ]);
                    }
                }
                $org .= "</div>";
            }
            $org .= "</td>";
        }
        $org .= "</table></div>";

        return "<div id='organigramm'><h2>Häufig gesucht</h2>
        <div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-5&quot;)' class='linkint'>Präsident</a></b></div>
        <div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-6&quot;)' class='linkint'>Mitgliederverwaltung</a></b></div>
        <div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-18&quot;)' class='linkint'>Kartenverkauf</a></b></div>
        <div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-19&quot;)' class='linkint'>Kleiderverkauf</a></b></div>
        <div>
            <br />
            <div><b>PC-Konto</b></div>
            <div><b>IBAN: </b>CH91 0900 0000 8525 6448 8</div>
            <div><b>Empfänger: </b>OL Zimmerberg, 8800 Thalwil</div>
        </div>
        <h2>Organigramm OL Zimmerberg</h2>".$org."</div>";
    }
}
