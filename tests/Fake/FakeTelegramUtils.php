<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\TelegramLink;

class FakeTelegramUtils {
    public $isAnonymousChat = false;
    public $configurationSent = false;
    public $telegramApiCalls = [];

    public function renderMarkdown($markdown) {
        return $markdown;
    }

    public function startAnonymousChat($chat_id, $user_id) {
    }

    public function linkChatUsingPin($pin, $chat_id, $user_id) {
        if ($pin != 'validpin') {
            throw new \Exception('Error linking chat using PIN.');
        }
        $user = FakeUsers::defaultUser(true);
        $user->setFirstName('Fakefirst');
        $telegram_link = new TelegramLink();
        $telegram_link->setUser($user);
        return $telegram_link;
    }

    public function getFreshPinForChat($chat_id) {
        return 'freshpin';
    }

    public function getTelegramPinChars() {
        return 'abcdefghijklmnopqrstuvwxyz';
    }

    public function getTelegramPinLength() {
        return 8;
    }

    public function isAnonymousChat($chat_id) {
        return $this->isAnonymousChat;
    }

    public function getChatState($chat_id) {
        return [];
    }

    public function sendConfiguration() {
        $this->configurationSent = true;
    }

    public function callTelegramApi($command, $args) {
        if ($args['chat_id'] == 'provoke_error') {
            throw new \Exception('provoked telegram error');
        }
        $this->telegramApiCalls[] = [$command, $args];
        return [];
    }

    public function getBotName() {
        return 'bot-name';
    }

    public function getFreshPinForUser($user) {
        if ($user->getUsername() == 'admin') {
            return 'correct-pin';
        }
        return 'wrong-pin';
    }
}
