<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\Fields\FieldTypes;

class LinkTelegramEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'LinkTelegramEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'botName' => new FieldTypes\StringField([]),
            'pin' => new FieldTypes\StringField([]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

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
