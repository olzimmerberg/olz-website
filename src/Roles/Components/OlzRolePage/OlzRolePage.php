<?php

namespace Olz\Roles\Components\OlzRolePage;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;
use Olz\Entity\Roles\Role;

class OlzRolePage extends OlzComponent {
    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([]);
        $is_member = $this->authUtils()->hasPermission('member');
        $user = $this->authUtils()->getCurrentUser();
        $entityManager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();
        $role_repo = $entityManager->getRepository(Role::class);
        $role_username = $args['ressort'];
        $role_repo = $entityManager->getRepository(Role::class);
        $role = $role_repo->findOneBy(['username' => $role_username, 'on_off' => 1]);

        if (!$role) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        // TODO: Remove again, after all ressort descriptions have been updated.
        // This is just temporary logic!
        $no_robots = ($role->getGuide() === '');

        $role_description = $role->getDescription();
        $end_of_first_line = strpos($role_description, "\n");
        $first_line = $end_of_first_line
            ? substr($role_description, 0, $end_of_first_line)
            : $role_description;
        $description_html = $this->htmlUtils()->renderMarkdown($first_line);
        $role_short_description = strip_tags($description_html);

        $role_id = $role->getId();
        $role_name = $role->getName();
        $role_title = $role->getTitle() ?? $role->getName();
        $role_description = $role->getDescription();

        $parent_chain = [];
        $parent = $role;
        while ($parent) {
            $parent_id = $parent->getParentRoleId();
            if ($parent_id) {
                $parent = $role_repo->findOneBy(['id' => $parent_id]);
                array_unshift($parent_chain, $parent);
            } else {
                $parent = null;
            }
        }

        $out = OlzHeader::render([
            'back_link' => "{$code_href}verein",
            'title' => $role_title,
            'description' => "{$role_short_description} - Ressort {$role_name} der OL Zimmerberg.",
            'norobots' => $no_robots,
        ]);

        $out .= "<div class='content-full olz-role-page'>";
        $out .= "<nav aria-label='breadcrumb'>";
        $out .= "<ol class='breadcrumb'>";
        $out .= "<li class='breadcrumb-item'><a href='{$code_href}verein'>OL Zimmerberg</a></li>";
        foreach ($parent_chain as $breadcrumb) {
            $username = $breadcrumb->getUsername();
            $name = $breadcrumb->getName();
            $out .= "<li class='breadcrumb-item'><a href='{$code_href}verein/{$username}'>{$name}</a></li>";
        }
        $out .= "<li class='breadcrumb-item active' aria-current='page'>{$role_name}</li>";
        $out .= "</ol>";
        $out .= "</nav>";

        $owner_user = $role->getOwnerUser();
        $is_owner = $user && $owner_user && intval($owner_user->getId() ?? 0) === intval($user->getId());
        $has_roles_permission = $this->authUtils()->hasPermission('roles');
        $can_edit = $is_owner || $has_roles_permission;
        $edit_admin = '';
        if ($can_edit) {
            $json_id = json_encode(intval($role_id));
            $edit_admin = <<<ZZZZZZZZZZ
            <div>
                <button
                    id='edit-role-button'
                    class='btn btn-primary'
                    onclick='return olz.editRole({$json_id})'
                >
                    <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                    Bearbeiten
                </button>
                <button
                    id='delete-role-button'
                    class='btn btn-danger'
                    onclick='return olz.deleteRole({$json_id})'
                >
                    <img src='{$code_href}assets/icns/delete_white_16.svg' class='noborder' />
                    Löschen
                </button>
            </div>
            ZZZZZZZZZZ;
        }

        $page = $role->getPage();
        if (strlen(trim($page)) > 0) {
            $out .= $page;
        } else {
            $out .= "<h1>{$edit_admin}{$role_title}</h1>";
            $description_html = $this->htmlUtils()->renderMarkdown($role->getDescription());
            $description_html = $role->replaceImagePaths($description_html);
            $description_html = $role->replaceFilePaths($description_html);
            $out .= $description_html;
        }

        $assignees = $role->getUsers();
        $num_assignees = count($assignees);
        $out .= "<br/><h2>Verantwortlich</h2>";
        if ($num_assignees === 0) {
            $out .= "<p><i>Keine Ressort-Verantwortlichen</i></p>";
        } else {
            $out .= "<div class='olz-user-info-card-list'>";
            foreach ($assignees as $assignee) {
                $out .= OlzUserInfoCard::render(['user' => $assignee]);
            }
            $out .= "</div>";
        }

        $child_roles = $role_repo->findBy(['parent_role' => $role_id], ['index_within_parent' => 'ASC']);
        $num_child_roles = count($child_roles);
        $out .= "<br/><h2>Unter-Ressorts</h2>";
        if ($num_child_roles === 0) {
            $out .= "<p><i>Keine Unter-Ressorts</i></p>";
        } else {
            foreach ($child_roles as $child_role) {
                $child_role_name = $child_role->getName();
                $child_role_username = $child_role->getUsername();
                $out .= "<p><a href='{$code_href}verein/{$child_role_username}' class='linkint'><b>{$child_role_name}</b></a></p>";
            }
        }

        if ($is_member) {
            $guide_html = $this->htmlUtils()->renderMarkdown($role->getGuide());
            $guide_html = $role->replaceImagePaths($guide_html);
            $guide_html = $role->replaceFilePaths($guide_html);
            $out .= "<br/><br/><h2>Aufgaben (nur für OLZ-Mitglieder sichtbar)</h2>";
            $out .= $guide_html;
        }

        $out .= "</div>";
        $out .= OlzFooter::render();

        return $out;
    }
}
