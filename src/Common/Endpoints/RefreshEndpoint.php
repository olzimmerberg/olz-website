<?php

namespace Olz\Common\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Common\Utils\AuthUtils;

/**
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     status: 'OK'|'KEEP'|'NO',
 *   }
 * >
 */
class RefreshEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $auth_utils = $this->authUtils();
        $raw_token = $this->getRequest()->cookies->get(AuthUtils::REFRESH_TOKEN_COOKIE);
        $raw_refresh_token = is_string($raw_token) ? $raw_token : null;
        try {
            $new_raw_token = $auth_utils->refreshSessionFromRefreshToken($raw_refresh_token);
        } catch (\Exception $exc) {
            if ($raw_refresh_token) {
                // The client presented a refresh token, but it could not be used
                // (e.g. expired, unknown, or a re-used token). Unlike a request
                // without any refresh token (i.e. a guest), this is worth logging.
                $this->log()->info("Could not refresh session: {$exc->getMessage()}");
            }
            $this->getResponse()->headers->setCookie($auth_utils->createRefreshTokenCookie(null));
            return ['status' => 'NO'];
        }
        if ($new_raw_token === null) {
            return ['status' => 'KEEP'];
        }
        $this->getResponse()->headers->setCookie(
            $auth_utils->createRefreshTokenCookie($new_raw_token)
        );
        return ['status' => 'OK'];
    }
}
