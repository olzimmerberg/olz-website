<?php

namespace Olz\Roles\Components\OlzRolePage;

use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Roles\Role;
use Olz\Users\Components\OlzUserInfoModal\OlzUserInfoModal;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{von?: ?string}> */
class OlzRolePageParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzRolePage extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $code_href = $this->envUtils()->getCodeHref();
        $where = implode(' AND ', array_map(function ($term) {
            return <<<ZZZZZZZZZZ
                (
                    r.username LIKE '%{$term}%'
                    OR r.old_username LIKE '%{$term}%'
                    OR r.name LIKE '%{$term}%'
                    OR r.description LIKE '%{$term}%'
                )
                ZZZZZZZZZZ;
        }, $terms));
        return <<<ZZZZZZZZZZ
            SELECT
                CONCAT('{$code_href}verein/', r.username) AS link,
                '{$code_href}assets/icns/link_role_16.svg' AS icon,
                NULL AS date,
                CONCAT('Ressort: ', IF(rpp.name IS NULL, '', CONCAT(rpp.name, ' &gt; ')), IF(rp.name IS NULL, '', CONCAT(rp.name, ' &gt; ')), r.name) AS title,
                CONCAT(IFNULL(r.username, ''), ' ', IFNULL(r.old_username, ''), ' ', IFNULL(r.description, '')) AS text,
                1.0 AS time_relevance
            FROM roles r
                LEFT JOIN roles rp ON (rp.id = r.parent_role)
                LEFT JOIN roles rpp ON (rpp.id = rp.parent_role)
            WHERE
                r.on_off = '1'
                AND {$where}
            ZZZZZZZZZZ;
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzRolePageParams::class);
        $is_member = $this->authUtils()->hasPermission('member');
        $entityManager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();
        $role_repo = $entityManager->getRepository(Role::class);
        $role_username = $args['ressort'];
        $role_repo = $entityManager->getRepository(Role::class);
        $role = $role_repo->findOneBy(['username' => $role_username, 'on_off' => 1]);

        if (!$role) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
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
        $role_description = $role->getDescription();
        $parent_role_id = $role->getParentRoleId();
        $parent_role = $role_repo->findOneBy(['id' => $parent_role_id]);
        $can_have_child_roles = $role->getCanHaveChildRoles();

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
            'title' => $role_name,
            'description' => "{$role_short_description} - Ressort {$role_name} der OL Zimmerberg.",
            'norobots' => $no_robots,
            'canonical_url' => "{$code_href}verein/{$role_username}",
        ]);

        $out .= "<div class='content-full olz-role-page'>";
        $out .= "<nav aria-label='breadcrumb'>";
        $out .= "<ol class='breadcrumb'>";
        $out .= "<li class='breadcrumb-item'><a href='{$code_href}verein'>OL Zimmerberg</a></li>";
        foreach ($parent_chain as $breadcrumb) {
            $username = $breadcrumb?->getUsername();
            $name = $breadcrumb?->getName();
            $out .= "<li class='breadcrumb-item'><a href='{$code_href}verein/{$username}'>{$name}</a></li>";
        }
        $out .= "<li class='breadcrumb-item active' aria-current='page'>{$role_name}</li>";
        $out .= "</ol>";
        $out .= "</nav>";

        $edit_admin = '';
        $add_membership_admin = '';
        $add_child_role_admin = '';
        $is_parent_superior = $this->authUtils()->hasRoleEditPermission($parent_role_id);
        $is_parent_owner = $parent_role && $this->entityUtils()->canUpdateOlzEntity($parent_role, null, 'roles');
        $can_parent_edit = $is_parent_superior || $is_parent_owner;
        $is_superior = $this->authUtils()->hasRoleEditPermission($role_id);
        $is_owner = $this->entityUtils()->canUpdateOlzEntity($role, null, 'roles');
        $can_edit = $is_superior || $is_owner;
        if ($can_edit) {
            $json_id = json_encode($role_id);
            $json_can_parent_edit = json_encode(boolval($can_parent_edit));
            $edit_admin = <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-role-button'
                        class='btn btn-primary'
                        onclick='return olz.editRole({$json_id}, {$json_can_parent_edit})'
                    >
                        <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                </div>
                ZZZZZZZZZZ;
            $add_membership_admin = <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='add-role-user-button'
                        class='btn btn-primary'
                        onclick='return olz.addRoleUser({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                        Neuer Verantwortlicher
                    </button>
                </div>
                ZZZZZZZZZZ;
            if ($can_have_child_roles) {
                $add_child_role_admin = <<<ZZZZZZZZZZ
                    <div>
                        <button
                            id='add-sub-role-button'
                            class='btn btn-primary'
                            onclick='return olz.addChildRole({$json_id})'
                        >
                            <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                            Neues Unter-Ressort
                        </button>
                    </div>
                    ZZZZZZZZZZ;
            }
        }

        $out .= "<div>{$edit_admin}</div>";
        $description_html = $this->htmlUtils()->renderMarkdown($role->getDescription());
        $description_html = $role->replaceImagePaths($description_html);
        $description_html = $role->replaceFilePaths($description_html);
        $out .= $description_html;

        $assignees = $role->getUsers();
        $num_assignees = count($assignees);
        $out .= "<br/><h2>Verantwortlich</h2>";
        if ($num_assignees === 0) {
            $out .= "<p><i>Keine Ressort-Verantwortlichen</i></p>";
            $out .= $add_membership_admin;
        } else {
            $out .= "<div class='role-assignees'>";
            foreach ($assignees as $assignee) {
                $out .= "<div class='assignee'>";
                if ($is_superior || $is_owner) {
                    $json_role_id = json_encode(intval($role_id));
                    $json_user_id = json_encode(intval($assignee->getId()));
                    $out .= <<<ZZZZZZZZZZ
                            <button
                                id='delete-role-user-button'
                                class='btn btn-sm btn-danger'
                                onclick='return olz.deleteRoleUser({$json_role_id}, {$json_user_id})'
                            >
                                <img src='{$code_href}assets/icns/delete_white_16.svg' class='noborder' />
                            </button>
                        ZZZZZZZZZZ;
                }
                $out .= OlzUserInfoModal::render([
                    'user' => $assignee,
                    'mode' => 'name_picture',
                ]);
                $out .= "</div>";
            }
            $out .= $add_membership_admin;
            $out .= "</div>";
        }

        $child_roles = $role_repo->findBy([
            'parent_role' => $role_id,
            'on_off' => 1,
        ], ['position_within_parent' => 'ASC']);
        $num_child_roles = count($child_roles);
        $out .= "<br/><h2>Unter-Ressorts</h2>";
        if ($num_child_roles === 0) {
            $out .= "<p id='sub-roles'><i>Keine Unter-Ressorts</i></p>";
        } else {
            $out .= "<ul id='sub-roles' class='no-style'>";
            foreach ($child_roles as $child_role) {
                $child_role_name = $child_role->getName();
                $child_role_username = $child_role->getUsername();
                $out .= "<li><a href='{$code_href}verein/{$child_role_username}' class='linkint'><b>{$child_role_name}</b></a></li>";
            }
            $out .= "</ul>";
            $out .= $add_child_role_admin;
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
