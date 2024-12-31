<?php

namespace Olz\Api;

use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

trait OlzTypedEndpoint {
    use WithUtilsTrait;

    public function call(mixed $input): mixed {
        $this->setLogger($this->log());
        return parent::call($input);
    }

    // --- Custom ---

    protected function checkPermission(string $permission): void {
        $has_access = $this->authUtils()->hasPermission($permission);
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }
    }
}
