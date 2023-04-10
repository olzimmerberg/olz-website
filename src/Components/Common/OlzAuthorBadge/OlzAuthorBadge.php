<?php

namespace Olz\Components\Common\OlzAuthorBadge;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Users\OlzPopup\OlzPopup;
use Olz\Components\Users\OlzRoleInfoCard\OlzRoleInfoCard;
use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;
use Olz\Entity\User;

class OlzAuthorBadge extends OlzComponent {
    public function getHtml($args = []): string {
        $user = $args['user'] ?? null;
        $role = $args['role'] ?? null;
        $name = $args['name'] ?? null;
        $email = $args['email'] ?? null;

        $level = null;
        $label = '?';
        $popup = null;
        if ($user && $role) {
            $level = 'role';
            $label = "{$user->getFirstName()} {$user->getLastName()}, {$role->getName()}";
            $popup = OlzRoleInfoCard::render(['role' => $role, 'user' => $user]);
        } elseif ($role) {
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
                $popup = OlzUserInfoCard::render(['user' => $user]);
            }
        }

        if (!$level) {
            return "";
        }

        $popup_class = $popup ? 'has-popup' : 'no-popup';

        $trigger = <<<ZZZZZZZZZZ
        <span class='olz-author-badge level-{$level} {$popup_class}'>
            {$label}
        </span>
        ZZZZZZZZZZ;
        if ($popup) {
            return OlzPopup::render(['trigger' => $trigger, 'popup' => $popup]);
        }
        return $trigger;
    }
}
