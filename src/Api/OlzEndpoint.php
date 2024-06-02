<?php

namespace Olz\Api;

use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Endpoint;
use PhpTypeScriptApi\HttpError;

abstract class OlzEndpoint extends Endpoint {
    use WithUtilsTrait;

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
}
