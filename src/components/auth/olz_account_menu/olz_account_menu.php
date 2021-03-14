<?php

function olz_account_menu($args = []): string {
    global $_CONFIG, $entityManager;
    $out = '';

    require_once __DIR__.'/../../../config/doctrine_db.php';
    require_once __DIR__.'/../../../config/server.php';
    require_once __DIR__.'/../../../model/index.php';

    $user_repo = $entityManager->getRepository(User::class);
    $username = $_SESSION['user'];
    $user = $user_repo->findOneBy(['username' => $username]);
    $image_path = "{$_CONFIG->getCodeHref()}icns/user.svg";
    if ($user) {
        $user_image_path = "img/users/{$user->getId()}.jpg";
        if (is_file("{$_CONFIG->getDataPath()}{$user_image_path}")) {
            $image_path = "{$_CONFIG->getDataHref()}{$user_image_path}";
        } else {
            $initials = strtoupper($user->getFirstName()[0].$user->getLastName()[0]);
            $image_path = "{$_CONFIG->getCodeHref()}icns/user.php?initials={$initials}";
        }
    }

    $out .= "<a href='#' role='button' id='account-menu-link' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
    $out .= "<img src='{$image_path}' class='account-thumbnail' />";
    $out .= "</a>";
    $out .= "<div class='dropdown-menu dropdown-menu-right' aria-labelledby='account-menu-link'>";
    if ($user) {
        $out .= "<a class='dropdown-item' href='profil.php'>Profil</a>";
        if (in_array('ftp', preg_split("/ /", $_SESSION['auth'])) || ($_SESSION['auth'] == 'all')) {
            $out .= "<a class='dropdown-item' href='webftp.php'>WebFTP</a>";
        }
        if ($_SESSION['auth'] == 'all') {
            $out .= "<a class='dropdown-item' href='logs.php'>Logs</a>";
        }
        $out .= <<<'ZZZZZZZZZZ'
        <a
            id='logout-menu-item'
            class='dropdown-item'
            href='#'
            onclick='olzAccountMenuLogout()'
        >
            Logout
        </a>
        ZZZZZZZZZZ;
    } else {
        $out .= <<<'ZZZZZZZZZZ'
        <a
            id='login-menu-item'
            class='dropdown-item'
            href='#'
            role='button'
            data-toggle='modal'
            data-target='#login-modal'
        >
            Login
        </a>
        <a
            id='sign-up-menu-item'
            class='dropdown-item feature sign-up'
            href='#'
            role='button'
            data-toggle='modal'
            data-target='#sign-up-modal'
        >
            Konto erstellen
        </a>
        ZZZZZZZZZZ;
    }
    $out .= "</div>";

    return $out;
}
