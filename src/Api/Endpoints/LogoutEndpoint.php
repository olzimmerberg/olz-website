<?php

namespace Olz\Api\Endpoints;

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
        $this->authUtils()->setSessionUser(null);
        $this->authUtils()->setSessionAuthUser(null);
        $this->session()->clear();
        return [
            'status' => 'SESSION_CLOSED',
        ];
    }
}
