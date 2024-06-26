<?php

namespace Olz\Components\Auth\OlzKontoTelegram;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzKontoTelegram extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $params = $this->httpUtils()->validateGetParams([
            'pin' => new FieldTypes\StringField(['allow_null' => true]),
        ]);
        $pin = $params['pin'];
        $user = $this->authUtils()->getCurrentUser();

        $out = OlzHeader::render([
            'title' => "OLZ Konto mit Telegram",
            'description' => "OLZ-Login mit Telegram.",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>
        <div>";

        if ($user) {
            try {
                $telegram_link = $this->telegramUtils()->linkUserUsingPin($pin, $user);
                $chat_id = $telegram_link->getTelegramChatId();
                $this->telegramUtils()->callTelegramApi('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "Hallo, {$user->getFirstName()}!",
                ]);
                $out .= "<div>Telegram-Chat erfolgreich verlinkt!</div>";
            } catch (\Exception $exc) {
                $message = $exc->getMessage();
                $out .= "<div>Telegram-Chat konnte nicht verlinkt werden: {$message}</div>";
            }
        } else {
            $out .= "<div>Bitte einloggen, um Telegram-Chat zu verlinken...</div>";
            $out .= "<div><a href='#login-dialog' role='button'><b>Login</b></a></div>";
        }

        $out .= "</div>
        </div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
