<?php

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/auth/olz_profile_form/olz_profile_form.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "OLZ Konto mit Telegram",
    'description' => "OLZ-Login mit Telegram.",
    'norobots' => true,
]);

require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';
require_once __DIR__.'/utils/notify/TelegramUtils.php';

$telegram_utils = getTelegramUtilsFromEnv();
$pin = $_GET['pin'];

$user_repo = $entityManager->getRepository(User::class);
$username = ($_SESSION['user'] ?? null);
$user = $user_repo->findOneBy(['username' => $username]);

echo "<div id='content_double'>
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
    echo "<div><a href='#' role='button' data-bs-toggle='modal' data-bs-target='#login-modal'><b>Login</b></a></div>";
}

echo "</div>
</div>";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
