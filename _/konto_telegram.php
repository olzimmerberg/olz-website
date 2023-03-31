<?php

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AuthUtils;
use Olz\Utils\TelegramUtils;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "OLZ Konto mit Telegram",
    'description' => "OLZ-Login mit Telegram.",
    'norobots' => true,
]);

$telegram_utils = TelegramUtils::fromEnv();
$pin = $_GET['pin'];

$auth_utils = AuthUtils::fromEnv();
$user = $auth_utils->getCurrentUser();

echo "<div class='content-full'>
<div>";

if ($user) {
    try {
        $telegram_link = $telegram_utils->linkUserUsingPin($pin, $user);
        $chat_id = $telegram_link->getTelegramChatId();
        $telegram_utils->callTelegramApi('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "Hallo, {$user->getFirstName()}!",
        ]);
        echo "<div>Telegram-Chat erfolgreich verlinkt!</div>";
    } catch (\Exception $exc) {
        $message = $exc->getMessage();
        echo "<div>Telegram-Chat konnte nicht verlinkt werden: {$message}</div>";
    }
} else {
    echo "<div>Bitte einloggen, um Telegram-Chat zu verlinken...</div>";
    echo "<div><a href='#login-dialog' onclick='olz.olzLoginModalShow()' role='button'><b>Login</b></a></div>";
}

echo "</div>
</div>";

echo OlzFooter::render();
