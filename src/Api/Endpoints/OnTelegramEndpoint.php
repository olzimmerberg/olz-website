<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\TelegramLink;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\HttpFoundation\Request;

class OnTelegramEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'OnTelegramEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'authenticityCode' => new FieldTypes\StringField([]),
            'telegramEvent' => new FieldTypes\StringField([]),
        ]]);
    }

    public function parseInput(Request $request) {
        return [
            'authenticityCode' => $request->query->get('authenticityCode'),
            'telegramEvent' => json_encode(json_decode($request->getContent(), true)),
        ];
    }

    protected function handle($input) {
        $expected_code = $this->envUtils()->getTelegramAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $telegram_event = json_decode($input['telegramEvent'], true);
        $message_text = $telegram_event['message']['text'] ?? null;
        $message_chat_id = $telegram_event['message']['chat']['id'] ?? null;
        $message_user_id = $telegram_event['message']['from']['id'] ?? null;

        if ($message_chat_id === null) {
            $this->log()->notice('Telegram message without chat_id', [$telegram_event]);
            return [];
        }

        $this->telegramUtils()->callTelegramApi('sendChatAction', [
            'chat_id' => $message_chat_id,
            'action' => 'typing',
        ]);

        $telegram_pin_chars = $this->telegramUtils()->getTelegramPinChars();
        $telegram_pin_length = $this->telegramUtils()->getTelegramPinLength();

        if (preg_match("/^\\/start ([{$telegram_pin_chars}]{{$telegram_pin_length}})$/", $message_text, $matches)) {
            try {
                $pin = $matches[1];
                $telegram_link = $this->telegramUtils()->linkChatUsingPin($pin, $message_chat_id, $message_user_id);
                $user = $telegram_link->getUser();
                $user_first_name = $user->getFirstName();
                $this->telegramUtils()->callTelegramApi('sendMessage', [
                    'chat_id' => $message_chat_id,
                    'text' => "Hallo, {$user_first_name}!",
                ]);
            } catch (\Exception $exc) {
                $this->telegramUtils()->callTelegramApi('sendMessage', [
                    'chat_id' => $message_chat_id,
                    'text' => $exc->getMessage(),
                ]);
            }
            return [];
        }

        if (preg_match("/^\\/start\\s*$/", $message_text, $matches)) {
            $this->telegramUtils()->startAnonymousChat($message_chat_id, $message_user_id);
        }

        $chat_state = $this->telegramUtils()->getChatState($message_chat_id);
        if ($chat_state == null) {
            $this->telegramUtils()->startAnonymousChat($message_chat_id, $message_user_id);
            $chat_state = $this->telegramUtils()->getChatState($message_chat_id);
        }

        $is_anonymous_chat = $this->telegramUtils()->isAnonymousChat($message_chat_id);
        if ($is_anonymous_chat) {
            $pin = $this->telegramUtils()->getFreshPinForChat($message_chat_id);
            $this->telegramUtils()->callTelegramApi('sendMessage', [
                'chat_id' => $message_chat_id,
                'parse_mode' => 'HTML',
                'text' => "<b>Willkommen bei der OL Zimmerberg!</b>\n\nDamit dieser Chat zu irgendwas zu gebrauchen ist, musst du <a href=\"https://olzimmerberg.ch/konto_telegram?pin={$pin}\">hier dein OLZ-Konto verlinken</a>.\n\nDieser Link wird nach 10 Minuten ungültig; wähle /start, um einen neuen Link zu erhalten.",
                'disable_web_page_preview' => true,
            ]);
            return [];
        }

        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
        $telegram_link = $telegram_link_repo->findOneBy([
            'telegram_chat_id' => $message_chat_id,
        ]);
        $user = $telegram_link->getUser();

        if (preg_match("/^\\/ich\\s*$/", $message_text, $matches)) {
            $response_message = <<<ZZZZZZZZZZ
                <b>Du bist angemeldet als:</b>
                <b>Name:</b> {$user->getFullName()}
                <b>Benutzername:</b> {$user->getUsername()}
                <b>E-Mail:</b> {$user->getEmail()}
                ZZZZZZZZZZ;
            $this->telegramUtils()->callTelegramApi('sendMessage', [
                'chat_id' => $message_chat_id,
                'parse_mode' => 'HTML',
                'text' => $response_message,
            ]);
            return [];
        }

        $this->telegramUtils()->callTelegramApi('sendMessage', [
            'chat_id' => $message_chat_id,
            'text' => 'Hä?',
        ]);

        return [];
    }
}
