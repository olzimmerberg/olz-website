<?php

namespace Olz\Components\Auth\OlzAccountMenu;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Users\User;

/** @extends OlzComponent<array<string, mixed>> */
class OlzAccountMenu extends OlzComponent {
    public function getHtml(mixed $args): string {
        $out = '';

        $auth_user = $this->authUtils()->getCurrentAuthUser();
        $user = $this->authUtils()->getCurrentUser();
        $image_paths = $this->authUtils()->getUserAvatar($user);
        $code_href = $this->envUtils()->getCodeHref();
        $should_verify_email = (
            $user
            && !$user->getParentUserId()
            && !$user->isEmailVerified()
            && !$this->authUtils()->hasPermission('verified_email', $user)
        );
        $show_profile_notification_dot = $should_verify_email;
        $show_notification_dot = $user && $show_profile_notification_dot;

        $out .= "<a href='#' role='button' id='account-menu-link' data-bs-toggle='dropdown' aria-label='Benutzermenu' aria-haspopup='true' aria-expanded='false'>";
        if ($show_notification_dot) {
            $out .= "<div class='notification-dot'></div>";
        }
        $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);
        $out .= "<img {$image_src_html} alt='' class='account-thumbnail' />";
        $out .= "</a>";
        $out .= "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='account-menu-link'>";
        if ($user && $auth_user) {
            $out .= "<a class='dropdown-item' href='{$code_href}benutzer/ich'>";
            if ($show_profile_notification_dot) {
                $out .= "<div class='notification-dot'></div>";
            }
            $out .= "Profil</a>";

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
            }

            $out .= <<<'ZZZZZZZZZZ'
                <div class='dropdown-divider'></div>
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
