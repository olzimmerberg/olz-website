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
        $raw_token = $this->getRequest()->cookies->get(AuthUtils::REFRESH_TOKEN_COOKIE);
        try {
            $new_raw_token = $auth_utils->refreshSessionFromRefreshToken(
                is_string($raw_token) ? $raw_token : null
            );
        } catch (\Exception $exc) {
            $this->getResponse()->headers->setCookie($auth_utils->createRefreshTokenCookie(null));
            throw new HttpError(401, 'Nicht eingeloggt!', $exc);
        }
        if ($new_raw_token !== null) {
            $this->getResponse()->headers->setCookie(
                $auth_utils->createRefreshTokenCookie($new_raw_token)
            );
        }
        return [
            'status' => 'OK',
        ];
    }
}
