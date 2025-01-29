<?php

namespace Olz\Roles\Components\OlzOrganigramm;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Roles\Role;
use Olz\Users\Components\OlzUserInfoModal\OlzUserInfoModal;

/** @extends OlzComponent<array<string, mixed>> */
class OlzOrganigramm extends OlzComponent {
    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();
        $entityManager = $this->dbUtils()->getEntityManager();
        $role_repo = $entityManager->getRepository(Role::class);

        $out = '';

        $out .= '<h2>Organigramm OL Zimmerberg</h2>';

        $root_roles = $role_repo->getRolesWithParent(null);
        $out .= "<div id='organigramm-scroll'><div class='organigramm-wrapper'>";
        foreach ($root_roles as $root_role) {
            $root_role_name = nl2br($root_role->getName());
            $root_role_username = $root_role->getUsername();
            $out .= "<div class='organigramm-column'>";
            $out .= "<div id='role-{$root_role->getId()}' class='role-box'>";
            $out .= "<a href='{$code_href}verein/{$root_role_username}' class='root-role-name'><h6>{$root_role_name}</h6></a>";
            $root_role_assignees = $root_role->getUsers();
            foreach ($root_role_assignees as $root_role_assignee) {
                $out .= OlzUserInfoModal::render([
                    'user' => $root_role_assignee,
                    'mode' => 'name_picture',
                ]);
            }
            $out .= "</div>";
            $roles = $role_repo->getRolesWithParent($root_role->getId());
            foreach ($roles as $role) {
                $role_name = nl2br($role->getName());
                $role_username = nl2br($role->getUsername());
                $out .= "<div class='organigramm-connect'><span></span></div>";
                $out .= "<div id='role-{$role->getId()}' class='role-box'>";
                $out .= "<a href='{$code_href}verein/{$role_username}' class='role-name'><h6>{$role_name}</h6></a>";
                $role_assignees = $role->getUsers();
                foreach ($role_assignees as $role_assignee) {
                    $out .= OlzUserInfoModal::render([
                        'user' => $role_assignee,
                        'mode' => 'name',
                    ]);
                }
                $sub_roles = $role_repo->getRolesWithParent($role->getId());
                foreach ($sub_roles as $sub_role) {
                    $sub_role_name = nl2br($sub_role->getName());
                    $sub_role_username = nl2br($sub_role->getUsername());
                    $out .= "<div id='role-{$sub_role->getId()}' class='sub-role-name'><a href='{$code_href}verein/{$sub_role_username}'>{$sub_role_name}</a></div>";
                    $sub_role_assignees = $sub_role->getUsers();
                    foreach ($sub_role_assignees as $sub_role_assignee) {
                        $out .= OlzUserInfoModal::render([
                            'user' => $sub_role_assignee,
                            'mode' => 'name',
                        ]);
                    }
                }
                $out .= "</div>";
            }
            $out .= "</div>";
        }
        $out .= "</div></div>";

        return $out;
    }
}
