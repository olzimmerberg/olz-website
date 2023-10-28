<?php

namespace Olz\Components\Auth\OlzKontoTelegram;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AuthUtils;
use Olz\Utils\TelegramUtils;

class OlzKontoTelegram extends OlzComponent {
    public function getHtml($args = []): string {
        $out = '';

        $out .= OlzHeader::render([
            'title' => "OLZ Konto mit Telegram",
            'description' => "OLZ-Login mit Telegram.",
            'norobots' => true,
        ]);

        $telegram_utils = TelegramUtils::fromEnv();
        $pin = $_GET['pin'];

        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getCurrentUser();

        $out .= "<div class='content-full'>
        <div>";

        if ($user) {
            try {
                $telegram_link = $telegram_utils->linkUserUsingPin($pin, $user);
                $chat_id = $telegram_link->getTelegramChatId();
                $telegram_utils->callTelegramApi('sendMessage', [
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
            $out .= "<div><a href='#login-dialog' onclick='olz.olzLoginModalShow()' role='button'><b>Login</b></a></div>";
        }

        $out .= "</div>
        </div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
