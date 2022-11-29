<?php

namespace Olz\Components\Common\OlzAuthorBadge;

class OlzAuthorBadge {
    public static function render($args = []) {
        $user = $args['user'] ?? null;
        $role = $args['role'] ?? null;
        $name = $args['name'] ?? null;

        $level = null;
        $label = '?';
        if ($user) {
            $level = 'user';
            $label = "{$user->getFirstName()} {$user->getLastName()}";
        }
        if ($role) {
            $level = 'role';
            $label = "{$role->getName()}";
        }
        if ($name) {
            $level = 'name';
            $label = $name;
        }

        if (!$level) {
            return "";
        }

        return <<<ZZZZZZZZZZ
        <span class='olz-author-badge level-{$level}'>
            {$label}
        </span>
        ZZZZZZZZZZ;
    }
}
