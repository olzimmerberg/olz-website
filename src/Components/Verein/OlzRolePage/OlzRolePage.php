<?php

namespace Olz\Components\Verein\OlzRolePage;

use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;
use Olz\Entity\Role;
use Olz\Utils\AuthUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\HtmlUtils;

class OlzRolePage {
    public static function render($args = []) {
        $auth_utils = AuthUtils::fromEnv();
        $is_member = $auth_utils->hasPermission('member');

        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $role_repo = $entityManager->getRepository(Role::class);

        $role = $args['role'];
        $role_id = $role->getId();
        $role_name = $role->getName();
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

        $out = "";
        $out .= "<div class='content-full olz-role-page'>";
        $out .= "<nav aria-label='breadcrumb'>";
        $out .= "<ol class='breadcrumb'>";
        $out .= "<li class='breadcrumb-item'><a href='verein.php'>OL Zimmerberg</a></li>";
        foreach ($parent_chain as $breadcrumb) {
            $username = $breadcrumb->getUsername();
            $name = $breadcrumb->getName();
            $out .= "<li class='breadcrumb-item'><a href='verein.php?ressort={$username}'>{$name}</a></li>";
        }
        $out .= "<li class='breadcrumb-item active' aria-current='page'>{$role_name}</li>";
        $out .= "</ol>";
        $out .= "</nav>";

        $page = $role->getPage();
        if (strlen(trim($page)) > 0) {
            $out .= $page;
        } else {
            $html_utils = HtmlUtils::fromEnv();
            $out .= "<h1>{$role->getName()}</h1>";
            $description_html = $html_utils->renderMarkdown($role->getDescription());
            $out .= $description_html;
        }

        $assignees = $role->getUsers();
        $num_assignees = count($assignees);
        $out .= "<br/><h2>Verantwortlich</h2>";
        if ($num_assignees === 0) {
            $out .= "<p><i>Keine Ressort-Verantwortlichen</i></p>";
        } else {
            foreach ($assignees as $assignee) {
                $out .= OlzUserInfoCard::render(['user' => $assignee]);
            }
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
                $out .= "<p><a href='verein.php?ressort={$child_role_username}' class='linkint'><b>{$child_role_name}</b></a></p>";
            }
        }

        if ($is_member) {
            $guide_html = $html_utils->renderMarkdown($role->getGuide());
            $out .= "<br/><br/><h2>Aufgaben (nur für OLZ-Mitglieder sichtbar)</h2>";
            $out .= $guide_html;
        }

        $out .= "</div>";
        return $out;
    }
}
