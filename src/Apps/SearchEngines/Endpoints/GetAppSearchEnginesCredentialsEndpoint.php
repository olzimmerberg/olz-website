<?php

namespace Olz\Apps\SearchEngines\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetAppSearchEnginesCredentialsEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'GetAppSearchEnginesCredentialsEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'username' => new FieldTypes\StringField(['allow_null' => false]),
            'password' => new FieldTypes\StringField(['allow_null' => false]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => []]);
    }

    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("SearchEngines credentials access by {$user->getUsername()}.");

        return [
            'username' => $this->envUtils()->getAppSearchEnginesUsername(),
            'password' => $this->envUtils()->getAppSearchEnginesPassword(),
        ];
    }
}
