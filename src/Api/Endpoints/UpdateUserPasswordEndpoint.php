<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\Fields\ValidationError;

class UpdateUserPasswordEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'UpdateUserPasswordEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'OTHER_USER',
                'INVALID_OLD',
            ]]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\IntegerField([]),
            'oldPassword' => new FieldTypes\StringField(['allow_empty' => false]),
            'newPassword' => new FieldTypes\StringField(['allow_empty' => false]),
        ]]);
    }

    protected function handle(mixed $input): mixed {
        $auth_username = $this->session()->get('user');

        $old_password = $input['oldPassword'];
        $new_password = $input['newPassword'];

        if (!$this->authUtils()->isPasswordAllowed($new_password)) {
            throw new ValidationError(['newPassword' => ["Das neue Passwort muss mindestens 8 Zeichen lang sein."]]);
        }

        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input['id']]);

        if ($user->getUsername() !== $auth_username) {
            return ['status' => 'OTHER_USER'];
        }

        if (!$this->authUtils()->verifyPassword($old_password, $user->getPasswordHash())) {
            return ['status' => 'INVALID_OLD'];
        }

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $user->setPasswordHash($this->authUtils()->hashPassword($new_password));
        $user->setLastModifiedAt($now_datetime);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
