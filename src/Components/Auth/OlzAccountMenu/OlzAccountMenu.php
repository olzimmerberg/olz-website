<?php

namespace Olz\Components\Auth\OlzAccountMenu;

use Olz\Utils\AuthUtils;
use Olz\Utils\EnvUtils;

class OlzAccountMenu {
    public static function render($args = []) {
        $out = '';

        $auth_utils = AuthUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $user = $auth_utils->getAuthenticatedUser();
        $image_path = "{$env_utils->getCodeHref()}icns/user.php?initials=".urlencode('?');
        if ($user) {
            $user_image_path = "img/users/{$user->getId()}.jpg";
            if (is_file("{$env_utils->getDataPath()}{$user_image_path}")) {
                $image_path = "{$env_utils->getDataHref()}{$user_image_path}";
            } else {
                $initials = strtoupper($user->getFirstName()[0].$user->getLastName()[0]);
                $image_path = "{$env_utils->getCodeHref()}icns/user.php?initials={$initials}";
            }
        }

        $out .= "<a href='#' role='button' id='account-menu-link' data-bs-toggle='dropdown' aria-label='Benutzermenu' aria-haspopup='true' aria-expanded='false'>";
        $out .= "<img src='{$image_path}' alt='' class='account-thumbnail' />";
        $out .= "</a>";
        $out .= "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='account-menu-link'>";
        if ($user) {
            $out .= "<a class='dropdown-item' href='/profil.php'>Profil</a>";
            $out .= "<a class='dropdown-item' href='/apps/'>Apps</a>";
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
                href='{$env_utils->getCodeHref()}konto_passwort.php'
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
