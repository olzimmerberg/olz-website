<?php

require_once __DIR__.'/../../api/common/Endpoint.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../model/AccessToken.php';

class RevokeWebdavAccessTokenEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'RevokeWebdavAccessTokenEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [];
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('webdav');
        if (!$has_access) {
            return ['status' => 'ERROR'];
        }

        $current_user = $this->authUtils->getSessionUser();

        $access_token_repo = $this->entityManager->getRepository(AccessToken::class);
        $access_token = $access_token_repo->findOneBy([
            'user' => $current_user,
            'purpose' => 'WebDAV',
        ]);

        if ($access_token) {
            $this->entityManager->remove($access_token);
            $this->entityManager->flush();
        }

        return [
            'status' => 'OK',
        ];
    }
}
