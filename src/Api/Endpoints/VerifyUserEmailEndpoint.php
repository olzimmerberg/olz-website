<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class VerifyUserEmailEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'VerifyUserEmailEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => []]);
    }

    protected function handle(mixed $input): mixed {
        $auth_utils = $this->authUtils();
        $user = $auth_utils->getCurrentUser();
        if (!$user) {
            throw new HttpError(401, "Nicht eingeloggt!");
        }

        $this->emailUtils()->setLogger($this->log());
        try {
            $this->emailUtils()->sendEmailVerificationEmail($user);
        } catch (\Throwable $th) {
            $this->log()->error("Error verifying email for user (ID:{$user->getId()})", [$th]);
            return ['status' => 'ERROR'];
        }
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
        ];
    }
}
