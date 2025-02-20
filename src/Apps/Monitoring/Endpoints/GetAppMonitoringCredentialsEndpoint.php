<?php

namespace Olz\Apps\Monitoring\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{username: non-empty-string, password: non-empty-string}
 * >
 */
class GetAppMonitoringCredentialsEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("Monitoring credentials access by {$user->getUsername()}.");

        return [
            'username' => $this->envUtils()->getAppMonitoringUsername() ?: '-',
            'password' => $this->envUtils()->getAppMonitoringPassword() ?: '-',
        ];
    }
}
