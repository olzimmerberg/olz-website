<?php

namespace Olz\Apps\GoogleSearch\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetAppGoogleSearchCredentialsEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'GetAppGoogleSearchCredentialsEndpoint';
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
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("GoogleSearch credentials access by {$user->getUsername()}.");

        return [
            'username' => $this->envUtils()->getAppGoogleSearchUsername(),
            'password' => $this->envUtils()->getAppGoogleSearchPassword(),
        ];
    }
}
