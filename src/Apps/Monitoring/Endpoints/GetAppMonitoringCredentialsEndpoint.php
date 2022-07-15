<?php

namespace Olz\Apps\Monitoring\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetAppMonitoringCredentialsEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'GetAppMonitoringCredentialsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'username' => new FieldTypes\StringField(['allow_null' => false]),
            'password' => new FieldTypes\StringField(['allow_null' => false]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => []]);
    }

    protected function handle($input) {
        if ($this->session->get('auth') != 'all') {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $username = $this->session->get('user');
        $this->logger->info("Monitoring credentials access by {$username}.");

        return [
            'username' => $this->envUtils->getAppMonitoringUsername(),
            'password' => $this->envUtils->getAppMonitoringPassword(),
        ];
    }
}
