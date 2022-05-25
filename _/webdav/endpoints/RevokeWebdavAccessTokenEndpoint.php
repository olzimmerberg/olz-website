<?php

use Olz\Entity\AccessToken;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';

class RevokeWebdavAccessTokenEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'RevokeWebdavAccessTokenEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
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
