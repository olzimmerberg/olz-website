<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';

class LinkTelegramEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'LinkTelegramEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'botName' => new FieldTypes\StringField([]),
            'pin' => new FieldTypes\StringField([]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

    protected function handle($input) {
        $auth_username = $this->session->get('user');

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $auth_username]);

        $bot_name = $this->telegramUtils->getBotName();
        $pin = $this->telegramUtils->getFreshPinForUser($user);

        return [
            'botName' => $bot_name,
            'pin' => $pin,
        ];
    }
}
