<?php

namespace Olz\Components\Auth\OlzAccountMenu;

use Olz\Entity\User;
use Olz\Utils\AuthUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

class OlzAccountMenu {
    public static function render($args = []) {
        $out = '';

        $auth_utils = AuthUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $auth_user = $auth_utils->getCurrentAuthUser();
        $user = $auth_utils->getCurrentUser();
        $image_path = $auth_utils->getUserAvatar($user);

        $out .= "<a href='#' role='button' id='account-menu-link' data-bs-toggle='dropdown' aria-label='Benutzermenu' aria-haspopup='true' aria-expanded='false'>";
        $out .= "<img src='{$image_path}' alt='' class='account-thumbnail' />";
        $out .= "</a>";
        $out .= "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='account-menu-link'>";
        if ($user) {
            $out .= "<a class='dropdown-item' href='{$env_utils->getCodeHref()}profil.php'>Profil</a>";

            $entityManager = DbUtils::fromEnv()->getEntityManager();
            $user_repo = $entityManager->getRepository(User::class);
            $child_users = $auth_user->getId() ?
                $user_repo->findBy(['parent_user' => $auth_user->getId()]) : [];
            $has_family = count($child_users) > 0;

            if ($has_family) {
                $out .= "<div class='dropdown-divider'></div>";
                $is_current = $auth_user->getId() === $user->getId();
                $class = $is_current ? ' disabled' : '';
                $out .= <<<ZZZZZZZZZZ
                <a
                    id='switch-user-{$auth_user->getId()}'
                    class='dropdown-item{$class}'
                    href='#'
                    onclick='olz.olzAccountMenuSwitchUser({$auth_user->getId()})'
                >
                    {$auth_user->getFullName()}
                </a>
                ZZZZZZZZZZ;

                foreach ($child_users as $child_user) {
                    $is_current = $child_user->getId() === $user->getId();
                    $class = $is_current ? ' disabled' : '';
                    $out .= <<<ZZZZZZZZZZ
                    <a
                        id='switch-user-{$child_user->getId()}'
                        class='dropdown-item{$class}'
                        href='#'
                        onclick='olz.olzAccountMenuSwitchUser({$child_user->getId()})'
                    >
                        {$child_user->getFullName()}
                    </a>
                    ZZZZZZZZZZ;
                }
                $out .= "<div class='dropdown-divider'></div>";
            }

            $out .= "<a class='dropdown-item' href='{$env_utils->getCodeHref()}apps/'>Apps</a>";
            $out .= <<<'ZZZZZZZZZZ'
            <a
                id='logout-menu-item'
                class='dropdown-item'
                href='#'
                onclick='olz.olzAccountMenuLogout()'
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
                onclick='olz.olzLoginModalShow()'
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
