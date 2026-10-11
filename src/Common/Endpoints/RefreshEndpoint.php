<?php

namespace Olz\Common\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Utils\AuthUtils;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     status: 'OK',
 *   }
 * >
 */
class RefreshEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $auth_utils = $this->authUtils();
        if ($this->session()->has('user')) {
            return [
                'status' => 'OK',
            ];
        }
        $refresh_token = $auth_utils->getRefreshToken();
        $user = $refresh_token?->getUser();
        if ($refresh_token === null || $user === null) {
            $this->getResponse()->headers->setCookie($auth_utils->createRefreshTokenCookie(null));
            throw new HttpError(401, 'Nicht eingeloggt!');
        }
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $expires_at = $refresh_token->getExpiresAt();
        if ($expires_at !== null && $expires_at < $now) {
            $this->entityManager()->remove($refresh_token);
            $this->entityManager()->flush();
            $this->getResponse()->headers->setCookie($auth_utils->createRefreshTokenCookie(null));
            throw new HttpError(401, 'Nicht eingeloggt!');
        }
        $refresh_token->setExpiresAt((clone $now)->modify('+'.AuthUtils::REFRESH_TOKEN_TTL.' seconds'));
        $this->entityManager()->flush();
        $this->session()->resetConfigure(['timeout' => AuthUtils::SESSION_TIMEOUT]);
        $auth_utils->setSessionUser($user);
        $auth_utils->setSessionAuthUser($user);
        $raw_token = $this->getRequest()->cookies->get(AuthUtils::REFRESH_TOKEN_COOKIE);
        $this->getResponse()->headers->setCookie(
            $auth_utils->createRefreshTokenCookie(is_string($raw_token) ? $raw_token : null)
        );
        return [
            'status' => 'OK',
        ];
    }
}
