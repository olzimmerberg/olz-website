<?php

namespace Olz\Components\Auth\OlzAccountMenu;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\User;

class OlzAccountMenu extends OlzComponent {
    public function getHtml($args = []): string {
        $out = '';

        $auth_user = $this->authUtils()->getCurrentAuthUser();
        $user = $this->authUtils()->getCurrentUser();
        $image_path = $this->authUtils()->getUserAvatar($user);
        $code_href = $this->envUtils()->getCodeHref();

        $out .= "<a href='#' role='button' id='account-menu-link' data-bs-toggle='dropdown' aria-label='Benutzermenu' aria-haspopup='true' aria-expanded='false'>";
        $out .= "<img src='{$image_path}' alt='' class='account-thumbnail' />";
        $out .= "</a>";
        $out .= "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='account-menu-link'>";
        if ($user) {
            $out .= "<a class='dropdown-item' href='{$code_href}profil.php'>Profil</a>";

            $entityManager = $this->dbUtils()->getEntityManager();
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

            $out .= "<a class='dropdown-item' href='{$this->envUtils()->getCodeHref()}apps/'>Apps</a>";
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
            $out .= <<<'ZZZZZZZZZZ'
            <a
                id='login-menu-item'
                class='dropdown-item'
                href='#login-dialog'
                onclick='olz.olzLoginModalShow()'
                role='button'
            >
                Login
            </a>
            ZZZZZZZZZZ;
        }
        $out .= "</div>";

        return $out;
    }
}
