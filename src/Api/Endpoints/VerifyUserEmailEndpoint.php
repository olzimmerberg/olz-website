<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Exceptions\RecaptchaDeniedException;
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
                'ERROR',
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
        $this->emailUtils()->setLogger($this->log());
        try {
            $this->emailUtils()->sendEmailVerificationEmail($user, $token);
        } catch (RecaptchaDeniedException $exc) {
            return ['status' => 'DENIED'];
        } catch (\Throwable $th) {
            return ['status' => 'ERROR'];
        }
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
        ];
    }
}
