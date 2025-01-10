<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     status: 'OK'|'ERROR',
 *   }
 * >
 */
class VerifyUserEmailEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $auth_utils = $this->authUtils();
        $user = $auth_utils->getCurrentUser();
        if (!$user) {
            throw new HttpError(401, "Nicht eingeloggt!");
        }

        $this->emailUtils()->setLogger($this->log());
        try {
            $this->emailUtils()->sendEmailVerificationEmail($user);
        } catch (\Throwable $th) {
            $this->log()->error("Error verifying email for user (ID:{$user->getId()})", [$th]);
            return ['status' => 'ERROR'];
        }
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
        ];
    }
}
