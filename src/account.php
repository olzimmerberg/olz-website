<?php

require_once __DIR__.'/config/doctrine.php';
require_once __DIR__.'/model/index.php';

$user_repo = $entityManager->getRepository(User::class);
$username = $_SESSION['user'];
$user = $user_repo->findOneBy(['username' => $username]);
$image_path = "{$code_href}icns/user.svg";
if ($user) {
    $user_image_path = "img/users/{$user->getId()}.jpg";
    if (is_file("{$data_path}{$user_image_path}")) {
        $image_path = "{$data_href}{$user_image_path}";
    }
}

// TODO: Remove once everyone can log in
if ($user) {
    echo "<a href='#' role='button' id='account-menu-link' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
    echo "<img src='{$image_path}' class='account-thumbnail' />";
    echo "</a>";
    echo "<div class='dropdown-menu dropdown-menu-right' aria-labelledby='account-menu-link'>";
    if ($user) {
        echo "<a class='dropdown-item' href='#'>Profil</a>";
        echo "<a class='dropdown-item' href='?page=ftp'>WebFTP</a>";
        echo "<a class='dropdown-item' href='?page=16'>Online-Resultate</a>";
        echo "<a class='dropdown-item' href='?page=17'>SVG-Editor</a>";
        echo "<a class='dropdown-item' href='?page=Logout'>Logout</a>";
    } else {
        echo "<a class='dropdown-item' href='#'>Login</a>";
    }
    echo "</div>";

    // TODO: Remove once everyone can log in
}
