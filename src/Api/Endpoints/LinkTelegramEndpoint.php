<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Users\User;

/**
 * @extends OlzTypedEndpoint<
 *   ?array{},
 *   array{
 *     botName: non-empty-string,
 *     pin: non-empty-string,
 *   }
 * >
 */
class LinkTelegramEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $auth_username = $this->session()->get('user');

        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $auth_username]);

        $bot_name = $this->telegramUtils()->getBotName();
        $pin = $this->telegramUtils()->getFreshPinForUser($user);

        return [
            'botName' => $bot_name,
            'pin' => $pin,
        ];
    }
}
