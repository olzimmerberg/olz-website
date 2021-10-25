<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';
require_once __DIR__.'/../../model/AccessToken.php';

class GetWebdavAccessTokenEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG, $_DATE, $entityManager;
        require_once __DIR__.'/../../config/date.php';
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        require_once __DIR__.'/../../utils/GeneralUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $general_utils = GeneralUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setDateUtils($_DATE);
        $this->setEntityManager($entityManager);
        $this->setGeneralUtils($general_utils);
    }

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

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'token' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('webdav');
        if (!$has_access) {
            return ['status' => 'ERROR', 'token' => null];
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
