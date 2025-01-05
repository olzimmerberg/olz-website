<?php

namespace Olz\Apps\Monitoring\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
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

    // public function getResponseField(): FieldTypes\Field {
    //     return new FieldTypes\ObjectField(['field_structure' => [
    //         'username' => new FieldTypes\StringField(['allow_null' => false]),
    //         'password' => new FieldTypes\StringField(['allow_null' => false]),
    //     ]]);
    // }

    // public function getRequestField(): FieldTypes\Field {
    //     return new FieldTypes\ObjectField(['field_structure' => []]);
    // }

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
