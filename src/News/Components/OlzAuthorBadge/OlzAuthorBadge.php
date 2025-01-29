<?php

namespace Olz\News\Components\OlzAuthorBadge;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;

/**
 * @extends OlzComponent<array{
 *   news_id: int,
 *   user?: ?User,
 *   role?: ?Role,
 *   name?: ?string,
 *   email?: ?string,
 *   mode?: ?('full'|'badge'|'text'),
 * }>
 */
class OlzAuthorBadge extends OlzComponent {
    public function getHtml(mixed $args): string {
        $news_id = $args['news_id'];
        $user = $args['user'] ?? null;
        $role = $args['role'] ?? null;
        $name = $args['name'] ?? null;
        $email = $args['email'] ?? null;
        $mode = $args['mode'] ?? 'full';

        $code_href = $this->envUtils()->getCodeHref();

        $icon = null;
        $level = null;
        $label = '?';
        $has_popup = false;
        if ($user && $role) {
            $icon = 'author_role_20.svg';
            $level = 'role';
            $label = "{$user->getFirstName()} {$user->getLastName()}, {$role->getName()}";
            $has_popup = true;
        } elseif ($role) {
            $icon = 'author_role_20.svg';
            $level = 'role';
            $label = "{$role->getName()}";
            $has_popup = true;
        } elseif ($user) {
            $level = 'user';
            $label = "{$user->getFirstName()} {$user->getLastName()}";
            $has_popup = true;
        }
        if ($name) {
            $level = 'name';
            $label = $name;
            if ($email) {
                $has_popup = true;
            }
        }

        if (!$level) {
            return "";
        }

        if ($mode === 'text') {
            return $label;
        }

        $popup_class = $has_popup ? 'has-popup' : 'no-popup';

        $icon_html = $icon ? "<img src='{$code_href}assets/icns/{$icon}' alt='' class='author-icon'>" : '';
        $trigger = <<<ZZZZZZZZZZ
            <span class='olz-author-badge level-{$level} {$popup_class}'>
                {$label}{$icon_html}
            </span>
            ZZZZZZZZZZ;
        if ($mode === 'badge') {
            return $trigger;
        }
        if ($has_popup) {
            return <<<ZZZZZZZZZZ
                <a href='#' onclick='return olz.initOlzAuthorBadge({$news_id})'>
                    {$trigger}
                </a>
                ZZZZZZZZZZ;
        }
        return $trigger;
    }
}
