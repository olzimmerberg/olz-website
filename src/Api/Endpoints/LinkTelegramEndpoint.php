<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\TelegramUtilsTrait;
use PhpTypeScriptApi\HttpError;

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
    use AuthUtilsTrait;
    use TelegramUtilsTrait;

    protected function handle(mixed $input): mixed {
        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $bot_name = $this->telegramUtils()->getBotName();
        $pin = $this->telegramUtils()->getFreshPinForUser($user);

        return [
            'botName' => $bot_name ?: '-',
            'pin' => $pin ?: '-',
        ];
    }
}
