<?php

namespace Olz\Roles\Components\OlzRoleInfoModal;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Roles\Role;

/** @extends OlzComponent<array{
 *   role: Role,
 *   text?: ?non-empty-string,
 * }> */
class OlzRoleInfoModal extends OlzComponent {
    public function getHtml(mixed $args): string {
        $role = $args['role'];
        $text = $args['text'] ?? $role->getName();
        $role_id = intval($role->getId());
        return <<<ZZZZZZZZZZ
            <div>
                <a
                    href='#'
                    onclick='return olz.initOlzRoleInfoModal({$role_id})'
                    class='linkrole'
                >
                    {$text}
                </a>
            </div>
            ZZZZZZZZZZ;
    }
}
