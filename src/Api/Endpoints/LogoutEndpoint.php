<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Utils\SessionTrait;

/**
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     status: 'NO_SESSION'|'SESSION_CLOSED',
 *   }
 * >
 */
class LogoutEndpoint extends OlzTypedEndpoint {
    use SessionTrait;

    protected function handle(mixed $input): mixed {
        $this->session()->delete('auth');
        $this->session()->delete('root');
        $this->session()->delete('user');
        $this->session()->delete('user_id');
        $this->session()->delete('auth_user');
        $this->session()->delete('auth_user_id');
        $this->session()->clear();
        return [
            'status' => 'SESSION_CLOSED',
        ];
    }
}
