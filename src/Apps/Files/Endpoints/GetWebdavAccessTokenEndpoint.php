<?php

namespace Olz\Apps\Files\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\AccessToken;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\DateUtilsTrait;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\GeneralUtilsTrait;

/**
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{status: 'OK'|'ERROR', token?: ?non-empty-string}
 * >
 */
class GetWebdavAccessTokenEndpoint extends OlzTypedEndpoint {
    use AuthUtilsTrait;
    use DateUtilsTrait;
    use EntityManagerTrait;
    use GeneralUtilsTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('webdav');

        $current_user = $this->authUtils()->getCurrentUser();

        $access_token_repo = $this->entityManager()->getRepository(AccessToken::class);
        $access_token = $access_token_repo->findOneBy([
            'user' => $current_user,
            'purpose' => 'WebDAV',
        ]);

        if (!$access_token) {
            $now = new \DateTime($this->dateUtils()->getIsoNow());
            $token = $this->generateRandomAccessToken();

            $access_token = new AccessToken();
            $access_token->setUser($current_user);
            $access_token->setPurpose('WebDAV');
            $access_token->setToken($token);
            $access_token->setCreatedAt($now);
            $access_token->setExpiresAt(null);

            $this->entityManager()->persist($access_token);
            $this->entityManager()->flush();
        }

        return [
            'status' => 'OK',
            'token' => $access_token->getToken() ?: null,
        ];
    }

    protected function generateRandomAccessToken(): string {
        return $this->generalUtils()->base64EncodeUrl(openssl_random_pseudo_bytes(18));
    }
}
