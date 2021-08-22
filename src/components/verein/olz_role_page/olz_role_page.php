<?php

function olz_role_page($args = []): string {
    global $entityManager;

    require_once __DIR__.'/../../../config/doctrine_db.php';
    require_once __DIR__.'/../../../model/index.php';
    require_once __DIR__.'/../../../utils/auth/AuthUtils.php';
    require_once __DIR__.'/../../../utils/client/HtmlUtils.php';

    $auth_utils = AuthUtils::fromEnv();
    $is_member = $auth_utils->hasPermission('member');

    $role_repo = $entityManager->getRepository(Role::class);

    $role = $args['role'];
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
    $out .= "<div id='content_double' class='olz-role-page'>";
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
        if ($is_member) {
            $guide_html = $html_utils->renderMarkdown($role->getGuide());
            $out .= "<h2>Aufgaben (nur für OLZ-Mitglieder sichtbar)</h2>";
            $out .= $guide_html;
        }
    }

    $out .= "</div>";
    return $out;
}
