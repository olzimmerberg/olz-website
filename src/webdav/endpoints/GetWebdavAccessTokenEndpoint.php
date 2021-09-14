<?php

require_once __DIR__.'/../../api/common/Endpoint.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../model/AccessToken.php';

class GetWebdavAccessTokenEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setGeneralUtils($generalUtils) {
        $this->generalUtils = $generalUtils;
    }

    public static function getIdent() {
        return 'GetWebdavAccessTokenEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'token' => new StringField(['allow_null' => true]),
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

        if (!$access_token) {
            $now = new DateTime($this->dateUtils->getIsoNow());
            $token = $this->generateRandomAccessToken();

            $access_token = new AccessToken();
            $access_token->setUser($current_user);
            $access_token->setPurpose('WebDAV');
            $access_token->setToken($token);
            $access_token->setCreatedAt($now);
            $access_token->setExpiresAt(null);

            $this->entityManager->persist($access_token);
            $this->entityManager->flush();
        }

        return [
            'status' => 'OK',
            'token' => $access_token->getToken(),
        ];
    }

    protected function generateRandomAccessToken() {
        return $this->generalUtils->base64EncodeUrl(openssl_random_pseudo_bytes(18));
    }
}
