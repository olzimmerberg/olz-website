<?php

namespace Olz\Roles\Components\OlzOrganigramm;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Users\OlzUserInfoWithPopup\OlzUserInfoWithPopup;
use Olz\Entity\Roles\Role;

class OlzOrganigramm extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $has_all_permissions = $this->authUtils()->hasPermission('all');

        $code_href = $this->envUtils()->getCodeHref();
        $entityManager = $this->dbUtils()->getEntityManager();
        $role_repo = $entityManager->getRepository(Role::class);

        $out = '';

        $out .= '<h2>Organigramm OL Zimmerberg</h2>';

        $colwid = 111;
        $root_roles = $role_repo->getRolesWithParent(null);
        $out .= "<div id='organigramm-scroll' style='width:100%; overflow-x:scroll;'><table style='table-layout:fixed; width:".($colwid * count($root_roles))."px;'>";
        foreach ($root_roles as $root_role) {
            $root_role_name = nl2br($root_role->getName());
            $root_role_username = $root_role->getUsername();
            $out .= "<td style='width:".$colwid."px; vertical-align:top;'>";
            $out .= "<div id='link-role-{$root_role->getId()}' style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;'>";
            $out .= "<h6 style='font-weight:bold; min-height:36px;'><a href='{$code_href}verein/{$root_role_username}'>{$root_role_name}</a></h6>";
            $root_role_assignees = $root_role->getUsers();
            foreach ($root_role_assignees as $root_role_assignee) {
                $out .= OlzUserInfoWithPopup::render([
                    'user' => $root_role_assignee,
                    'mode' => 'name_picture',
                ]);
            }
            $out .= "</div>";
            $charge_roles = $role_repo->getRolesWithParent($root_role->getId());
            foreach ($charge_roles as $charge_role) {
                $charge_role_name = nl2br($charge_role->getName());
                $charge_role_username = nl2br($charge_role->getUsername());
                $out .= "<div style='text-align:center; height:20px; overflow:hidden;'><span style='border-left:1px solid #000000; font-size:20px;'></span></div>";
                $out .= "<div id='link-role-{$charge_role->getId()}' style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;'>";
                $out .= "<h6 style='font-weight:bold;'><a href='{$code_href}verein/{$charge_role_username}'>{$charge_role_name}</a></h6>";
                $charge_role_assignees = $charge_role->getUsers();
                foreach ($charge_role_assignees as $charge_role_assignee) {
                    $out .= OlzUserInfoWithPopup::render([
                        'user' => $charge_role_assignee,
                        'mode' => 'name',
                    ]);
                }
                $subcharge_roles = $role_repo->getRolesWithParent($charge_role->getId());
                foreach ($subcharge_roles as $subcharge_role) {
                    $subcharge_role_name = nl2br($subcharge_role->getName());
                    $subcharge_role_username = nl2br($subcharge_role->getUsername());
                    $out .= "<div id='link-role-{$subcharge_role->getId()}' style='margin-top:4px; text-align:center; font-style:italic;'><a href='{$code_href}verein/{$subcharge_role_username}'>{$subcharge_role_name}</a></div>";
                    $subcharge_role_assignees = $subcharge_role->getUsers();
                    foreach ($subcharge_role_assignees as $subcharge_role_assignee) {
                        $out .= OlzUserInfoWithPopup::render([
                            'user' => $subcharge_role_assignee,
                            'mode' => 'name',
                        ]);
                    }
                }
                $out .= "</div>";
            }
            $out .= "</td>";
        }
        $out .= "</table></div>";

        return $out;
    }
}
