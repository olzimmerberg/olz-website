<?php

namespace Olz\Apps\Statistics\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetAppStatisticsCredentialsEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'GetAppStatisticsCredentialsEndpoint';
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
        $this->logger->info("Statistics credentials access by {$username}.");

        return [
            'username' => $this->envUtils->getAppStatisticsUsername(),
            'password' => $this->envUtils->getAppStatisticsPassword(),
        ];
    }
}
