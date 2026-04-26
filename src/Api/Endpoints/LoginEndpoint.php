<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     usernameOrEmail: non-empty-string,
 *     password: non-empty-string,
 *     rememberMe: bool,
 *   },
 *   array{
 *     status: 'AUTHENTICATED'|'INVALID_CREDENTIALS'|'BLOCKED',
 *     numRemainingAttempts: ?int<0, max>,
 *   }
 * >
 */
class LoginEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $username_or_email = trim($input['usernameOrEmail']);
        $password = $input['password'];
        $remember_me = $input['rememberMe'];

        try {
            $user = $this->authUtils()->authenticate($username_or_email, $password);
        } catch (AuthBlockedException $exc) {
            return [
                'status' => 'BLOCKED',
                'numRemainingAttempts' => 0,
            ];
        } catch (InvalidCredentialsException $exc) {
            return [
                'status' => 'INVALID_CREDENTIALS',
                'numRemainingAttempts' => $exc->getNumRemainingAttempts(),
            ];
        }

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $user->setLastLoginAt($now_datetime);
        $this->entityManager()->flush();

        $this->session()->resetConfigure([
            'timeout' => $remember_me ? 2419200 : 3600, // a month / an hour
        ]);

        $this->authUtils()->setSessionUser($user);
        $this->authUtils()->setSessionAuthUser($user);
        return [
            'status' => 'AUTHENTICATED',
            'numRemainingAttempts' => null,
        ];
    }
}
