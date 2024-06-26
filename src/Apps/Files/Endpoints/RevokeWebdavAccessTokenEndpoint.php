<?php

namespace Olz\Apps\Files\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\AccessToken;
use PhpTypeScriptApi\Fields\FieldTypes;

class RevokeWebdavAccessTokenEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'RevokeWebdavAccessTokenEndpoint';
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
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('webdav');

        $current_user = $this->authUtils()->getCurrentUser();

        $access_token_repo = $this->entityManager()->getRepository(AccessToken::class);
        $access_token = $access_token_repo->findOneBy([
            'user' => $current_user,
            'purpose' => 'WebDAV',
        ]);

        if ($access_token) {
            $this->entityManager()->remove($access_token);
            $this->entityManager()->flush();
        }

        return [
            'status' => 'OK',
        ];
    }
}
