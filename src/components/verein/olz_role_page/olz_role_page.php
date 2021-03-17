<?php

function olz_role_page($args = []): string {
    global $entityManager;

    require_once __DIR__.'/../../../config/doctrine_db.php';
    require_once __DIR__.'/../../../model/index.php';

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
    $out .= "<div id='content_double'>";
    $out .= "<nav aria-label='breadcrumb'>";
    $out .= "<ol class='breadcrumb'>";
    $out .= "<li class='breadcrumb-item'><a href='verein.php'>OL Zimmerberg</a></li>";
    foreach ($parent_chain as $breadcrumb) {
        $username = $breadcrumb->getUsername();
        $name = $breadcrumb->getName();
        $out .= "<li class='breadcrumb-item'><a href='verein.php?abteilung={$username}'>{$name}</a></li>";
    }
    $out .= "<li class='breadcrumb-item active' aria-current='page'>{$role_name}</li>";
    $out .= "</ol>";
    $out .= "</nav>";

    $page = $role->getPage();
    if (strlen(trim($page)) > 0) {
        $out .= $page;
    } else {
        $out .= "<h1>{$role->getName()}</h1>";
        $out .= "<p><b>{$role->getDescription()}</b></p>";
    }

    $out .= "</div>";
    return $out;
}
