<?php

namespace Olz\Components\Auth\OlzAccountMenu;

use Olz\Entity\User;

class OlzAccountMenu {
    public static function render($args = []) {
        global $_CONFIG, $entityManager;
        $out = '';

        require_once __DIR__.'/../../../../_/config/doctrine_db.php';
        require_once __DIR__.'/../../../../_/config/server.php';

        $user_repo = $entityManager->getRepository(User::class);
        $username = ($_SESSION['user'] ?? null);
        $user = $user_repo->findOneBy(['username' => $username]);
        $image_path = "{$_CONFIG->getCodeHref()}icns/user.php?initials=".urlencode('?');
        if ($user) {
            $user_image_path = "img/users/{$user->getId()}.jpg";
            if (is_file("{$_CONFIG->getDataPath()}{$user_image_path}")) {
                $image_path = "{$_CONFIG->getDataHref()}{$user_image_path}";
            } else {
                $initials = strtoupper($user->getFirstName()[0].$user->getLastName()[0]);
                $image_path = "{$_CONFIG->getCodeHref()}icns/user.php?initials={$initials}";
            }
        }

        $out .= "<a href='#' role='button' id='account-menu-link' data-bs-toggle='dropdown' aria-label='Benutzermenu' aria-haspopup='true' aria-expanded='false'>";
        $out .= "<img src='{$image_path}' alt='' class='account-thumbnail' />";
        $out .= "</a>";
        $out .= "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='account-menu-link'>";
        if ($user) {
            $out .= "<a class='dropdown-item' href='profil.php'>Profil</a>";
            if (in_array('ftp', preg_split('/ /', $_SESSION['auth'] ?? '')) || (($_SESSION['auth'] ?? null) == 'all')) {
                $out .= "<a class='dropdown-item' href='/webftp.php'>WebFTP</a>";
            }
            if (($_SESSION['auth'] ?? null) == 'all') {
                $out .= "<a class='dropdown-item' href='/apps/'>Apps</a>";
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
            $out .= <<<ZZZZZZZZZZ
            <a
                id='login-menu-item'
                class='dropdown-item'
                href='#login-dialog'
                onclick='olzLoginModalShow()'
                role='button'
            >
                Login
            </a>
            <a
                id='sign-up-menu-item'
                class='dropdown-item'
                href='{$_CONFIG->getCodeHref()}konto_passwort.php'
                role='button'
            >
                Konto erstellen
            </a>
            <a
                id='external-sign-up-menu-item'
                class='dropdown-item feature external-login'
                href='#'
                role='button'
                data-bs-toggle='modal'
                data-bs-target='#sign-up-modal'
            >
                Konto erstellen (extern)
            </a>
            ZZZZZZZZZZ;
        }
        $out .= "</div>";

        return $out;
    }
}
