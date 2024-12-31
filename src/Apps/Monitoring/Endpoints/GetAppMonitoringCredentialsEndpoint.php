<?php

namespace Olz\Apps\Monitoring\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @extends TypedEndpoint<
 *   ?array{},
 *   array{username: non-empty-string, password: non-empty-string}
 * >
 */
class GetAppMonitoringCredentialsEndpoint extends TypedEndpoint {
    use OlzTypedEndpoint;

    public static function getApiObjectClasses(): array {
        return [];
    }

    public static function getIdent(): string {
        return 'GetAppMonitoringCredentialsEndpoint';
    }

    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("Monitoring credentials access by {$user->getUsername()}.");

        return [
            'username' => $this->envUtils()->getAppMonitoringUsername(),
            'password' => $this->envUtils()->getAppMonitoringPassword(),
        ];
    }
}
