<?php

namespace Olz\Common\Endpoints;

use Olz\Api\OlzTypedEndpoint;

/**
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     status: 'NO_SESSION'|'SESSION_CLOSED',
 *   }
 * >
 */
class LogoutEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $refresh_token = $this->authUtils()->getRefreshToken();
        if ($refresh_token !== null) {
            $this->entityManager()->remove($refresh_token);
            $this->entityManager()->flush();
        }
        $this->getResponse()->headers->setCookie(
            $this->authUtils()->createRefreshTokenCookie(null)
        );
        $this->authUtils()->setSessionUser(null);
        $this->authUtils()->setSessionAuthUser(null);
        $this->session()->clear();
        return [
            'status' => 'SESSION_CLOSED',
        ];
    }
}
