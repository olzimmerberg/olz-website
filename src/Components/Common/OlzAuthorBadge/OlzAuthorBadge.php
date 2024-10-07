<?php

namespace Olz\Components\Common\OlzAuthorBadge;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Users\OlzPopup\OlzPopup;
use Olz\Components\Users\OlzRoleInfoCard\OlzRoleInfoCard;
use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;
use Olz\Entity\Users\User;

class OlzAuthorBadge extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $user = $args['user'] ?? null;
        $role = $args['role'] ?? null;
        $name = $args['name'] ?? null;
        $email = $args['email'] ?? null;
        $mode = $args['mode'] ?? 'full'; // 'full' (with popup), 'badge', 'text'

        $code_href = $this->envUtils()->getCodeHref();

        $icon = null;
        $level = null;
        $label = '?';
        $popup = null;
        if ($user && $role) {
            $icon = 'author_role_20.svg';
            $level = 'role';
            $label = "{$user->getFirstName()} {$user->getLastName()}, {$role->getName()}";
            $popup = OlzRoleInfoCard::render(['role' => $role, 'user' => $user]);
        } elseif ($role) {
            $icon = 'author_role_20.svg';
            $level = 'role';
            $label = "{$role->getName()}";
            $popup = OlzRoleInfoCard::render(['role' => $role]);
        } elseif ($user) {
            $level = 'user';
            $label = "{$user->getFirstName()} {$user->getLastName()}";
            $popup = OlzUserInfoCard::render(['user' => $user]);
        }
        if ($name) {
            $level = 'name';
            $label = $name;
            if ($email) {
                $user = new User();
                $user->setFirstName($name);
                $user->setLastName(' ');
                $user->setEmail($email);
                $user->setPermissions('');
                $user->setAvatarImageId(null);
                $popup = OlzUserInfoCard::render(['user' => $user]);
            }
        }

        if (!$level) {
            return "";
        }

        if ($mode === 'text') {
            return $label;
        }

        $popup_class = $popup ? 'has-popup' : 'no-popup';

        $icon_html = $icon ? "<img src='{$code_href}assets/icns/{$icon}' alt='' class='author-icon'>" : '';
        $trigger = <<<ZZZZZZZZZZ
            <span class='olz-author-badge level-{$level} {$popup_class}'>
                {$label}{$icon_html}
            </span>
            ZZZZZZZZZZ;
        if ($mode === 'badge') {
            return $trigger;
        }
        if ($popup) {
            return OlzPopup::render(['trigger' => $trigger, 'popup' => $popup]);
        }
        return $trigger;
    }
}
