<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class UpdateUserPasswordEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'UpdateUserPasswordEndpoint';
    }

    public function getResponseFields() {
        return [
            new EnumField('status', ['allowed_values' => [
                'OK',
                'OTHER_USER',
                'INVALID_OLD',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            new IntegerField('id', []),
            new StringField('oldPassword', ['allow_empty' => false]),
            new StringField('newPassword', ['allow_empty' => false]),
        ];
    }

    protected function handle($input) {
        $auth_username = $this->session->get('user');

        $old_password = $input['oldPassword'];
        $new_password = $input['newPassword'];

        if (!$this->authUtils->isPasswordAllowed($new_password)) {
            throw new ValidationError(['newPassword' => ["Das neue Passwort muss mindestens 8 Zeichen lang sein."]]);
        }

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input['id']]);

        if ($user->getUsername() !== $auth_username) {
            return ['status' => 'OTHER_USER'];
        }

        if (!password_verify($old_password, $user->getPasswordHash())) {
            return ['status' => 'INVALID_OLD'];
        }

        $user->setPasswordHash(password_hash($new_password, PASSWORD_DEFAULT));
        $this->entityManager->flush();

        return ['status' => 'OK'];
    }
}
