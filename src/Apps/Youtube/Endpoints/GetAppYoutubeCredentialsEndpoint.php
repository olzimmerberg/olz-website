<?php

namespace Olz\Apps\Youtube\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetAppYoutubeCredentialsEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'GetAppYoutubeCredentialsEndpoint';
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
        $this->logger->info("Youtube credentials access by {$username}.");

        return [
            'username' => $this->envUtils->getAppGoogleSearchUsername(),
            'password' => $this->envUtils->getAppGoogleSearchPassword(),
        ];
    }
}