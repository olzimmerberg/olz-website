<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class VerifyUserEmailEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'VerifyUserEmailEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'DENIED',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'recaptchaToken' => new FieldTypes\StringField([]),
        ]]);
    }

    protected function handle($input) {
        $auth_utils = $this->authUtils();
        $user = $auth_utils->getAuthenticatedUser();
        if (!$user) {
            throw new HttpError(401, "Nicht eingeloggt!");
        }

        $token = $input['recaptchaToken'];
        if (!$this->recaptchaUtils()->validateRecaptchaToken($token)) {
            return ['status' => 'DENIED'];
        }

        $this->emailUtils()->setLogger($this->log());
        $this->emailUtils()->sendEmailVerificationEmail($user);
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
        ];
    }
}
