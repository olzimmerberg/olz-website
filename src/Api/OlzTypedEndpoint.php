<?php

namespace Olz\Api;

use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\LogTrait;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @template Request
 * @template Response
 *
 * @extends TypedEndpoint<Request, Response>
 */
abstract class OlzTypedEndpoint extends TypedEndpoint {
    use AuthUtilsTrait;
    use LogTrait;

    public function runtimeSetup(): void {
        $this->setLogger($this->log());
    }

    // --- Custom ---

    protected function checkPermission(string $permission): void {
        $has_access = $this->authUtils()->hasPermission($permission);
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }
    }

    protected function checkIsStaff(): void {
        $has_access = !empty($this->authUtils()->getAuthenticatedRoles());
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }
    }
}
