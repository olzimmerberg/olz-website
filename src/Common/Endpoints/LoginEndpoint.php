<?php

namespace Olz\Common\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Exceptions\AuthBlockedException;
use Olz\Exceptions\InvalidCredentialsException;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     usernameOrEmail: non-empty-string,
 *     password: non-empty-string,
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
            'timeout' => 2419200, // always a month
        ]);

        $this->authUtils()->setSessionUser($user);
        $this->authUtils()->setSessionAuthUser($user);
        return [
            'status' => 'AUTHENTICATED',
            'numRemainingAttempts' => null,
        ];
    }
}
