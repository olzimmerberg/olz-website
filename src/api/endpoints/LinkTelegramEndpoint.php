<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/StringField.php';

class LinkTelegramEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setTelegramUtils($telegram_utils) {
        $this->telegramUtils = $telegram_utils;
    }

    public static function getIdent() {
        return 'LinkTelegramEndpoint';
    }

    public function getResponseFields() {
        return [
            'botName' => new StringField([]),
            'pin' => new StringField([]),
        ];
    }

    public function getRequestFields() {
        return [];
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
