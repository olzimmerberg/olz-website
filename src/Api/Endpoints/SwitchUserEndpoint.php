<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     userId: int<1, max>,
 *   },
 *   array{
 *     status: 'OK',
 *   }
 * >
 */
class SwitchUserEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input['userId']]);
        if (!$user) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $auth_user_id = $this->session()->get('auth_user_id');
        $is_parent = $auth_user_id && intval($user->getParentUserId()) === intval($auth_user_id);
        $is_self = $auth_user_id && intval($user->getId()) === intval($auth_user_id);
        if (!$is_self && !$is_parent) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->authUtils()->setSessionUser($user);

        return [
            'status' => 'OK',
        ];
    }
}
