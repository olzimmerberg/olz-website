<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/StringField.php';

class OnTelegramEndpoint extends Endpoint {
    public function setTelegramUtils($telegram_utils) {
        $this->telegramUtils = $telegram_utils;
    }

    public function setServerConfig($serverConfig) {
        $this->serverConfig = $serverConfig;
    }

    public static function getIdent() {
        return 'OnTelegramEndpoint';
    }

    public function getResponseFields() {
        return [];
    }

    public function getRequestFields() {
        return [
            new StringField('authenticityCode', []),
            new StringField('telegramEvent', []),
        ];
    }

    public function parseInput() {
        global $_GET;
        $input = [
            'authenticityCode' => $_GET['authenticityCode'],
            'telegramEvent' => json_encode(json_decode(file_get_contents('php://input'), true)),
        ];
        return $input;
    }

    protected function handle($input) {
        $expected_code = $this->serverConfig->getTelegramAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $telegram_event = json_decode($input['telegramEvent'], true);
        $message_text = $telegram_event['message']['text'] ?? null;
        $message_chat_id = $telegram_event['message']['chat']['id'] ?? null;
        $message_user_id = $telegram_event['message']['from']['id'] ?? null;

        $this->telegramUtils->callTelegramApi('sendChatAction', [
            'chat_id' => $message_chat_id,
            'action' => 'typing',
        ]);

        $telegram_pin_chars = $this->telegramUtils->getTelegramPinChars();
        $telegram_pin_length = $this->telegramUtils->getTelegramPinLength();

        if (preg_match("/^\\/start ([{$telegram_pin_chars}]{{$telegram_pin_length}})$/", $message_text, $matches)) {
            try {
                $pin = $matches[1];
                $telegram_link = $this->telegramUtils->linkChatUsingPin($pin, $message_chat_id, $message_user_id);
                $user = $telegram_link->getUser();
                $user_first_name = $user->getFirstName();
                $this->telegramUtils->callTelegramApi('sendMessage', [
                    'chat_id' => $message_chat_id,
                    'text' => "Hallo, {$user_first_name}!",
                ]);
            } catch (\Exception $exc) {
                $this->telegramUtils->callTelegramApi('sendMessage', [
                    'chat_id' => $message_chat_id,
                    'text' => $exc->getMessage(),
                ]);
            }
            return [];
        }

        if (preg_match("/^\\/start\\s*$/", $message_text, $matches)) {
            $this->telegramUtils->startAnonymousChat($message_chat_id, $message_user_id);
        }

        $chat_state = $this->telegramUtils->getChatState($message_chat_id);
        if ($chat_state == null) {
            $this->telegramUtils->startAnonymousChat($message_chat_id, $message_user_id);
            $chat_state = $this->telegramUtils->getChatState($message_chat_id);
        }

        $is_anonymous_chat = $this->telegramUtils->isAnonymousChat($message_chat_id);
        if ($is_anonymous_chat) {
            $pin = $this->telegramUtils->getFreshPinForChat($message_chat_id);
            $this->telegramUtils->callTelegramApi('sendMessage', [
                'chat_id' => $message_chat_id,
                'parse_mode' => 'HTML',
                'text' => "Willkommen bei der OL Zimmerberg!\nDamit dieser Chat zu irgendwas zu gebrauchen ist, musst du <a href=\"https://olzimmerberg.ch/konto_telegram.php?pin={$pin}\">hier dein OLZ-Konto verlinken</a>.",
                'disable_web_page_preview' => false,
            ]);
            return [];
        }

        $this->telegramUtils->callTelegramApi('sendMessage', [
            'chat_id' => $message_chat_id,
            'text' => 'Hä?',
        ]);

        return [];
    }
}
