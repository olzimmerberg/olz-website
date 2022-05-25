<?php

use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Olz\Entity\User;

require_once __DIR__.'/../../../config/doctrine_db.php';
require_once __DIR__.'/../../../config/paths.php';
require_once __DIR__.'/../olz_notification_subscriptions_form/olz_notification_subscriptions_form.php';

$user_repo = $entityManager->getRepository(User::class);
$username = ($_SESSION['user'] ?? null);
$user = $user_repo->findOneBy(['username' => $username]);

if ($user) {
    $telegram_link_repo = $entityManager->getRepository(TelegramLink::class);
    $telegram_link = $telegram_link_repo->findOneBy(['user' => $user]);
    $has_telegram_link = $telegram_link && $telegram_link->getTelegramChatId() !== null;

    $notification_subscription_repo = $entityManager->getRepository(NotificationSubscription::class);
    $subscriptions = $notification_subscription_repo->findBy(['user' => $user, 'delivery_type' => 'telegram']);
    $form = olz_notification_subscriptions_form($subscriptions);

    if (!$has_telegram_link) {
        $content = <<<'ZZZZZZZZZZ'
            <p class="card-text">Mit der Chat-App Telegram halten wir dich immer auf dem Laufenden!</p>
            <p class="card-text text-end">
                <a 
                    href="#"
                    role="button"
                    data-bs-toggle="modal"
                    data-bs-target="#link-telegram-modal"
                    class="btn btn-light btn-sm"
                >
                    Aktivieren
                </a>
            </p>
            <p class="card-text"><a href='fragen_und_antworten.php#weshalb-telegram-push' class='linkwhite'>Weshalb Telegram?</a></p>
        ZZZZZZZZZZ;
    } else {
        $content = <<<ZZZZZZZZZZ
            <form id='telegram-notifications-form' onsubmit='return olzTelegramNotificationsUpdate(this)'>
                <p class='card-title'><b>Du hast folgende Benachrichtigungen aktiviert:</b></p>
                {$form}
                <p class="card-text text-end">
                    <button 
                        id='telegram-notifications-submit'
                        type='submit'
                        class='btn btn-light btn-sm'
                    >
                        Speichern
                    </button>
                </p>
                <div id='telegram-notifications-success-message' class='alert alert-success' role='alert'></div>
                <div id='telegram-notifications-error-message' class='alert alert-danger' role='alert'></div>
                <p class="card-text"><a href='fragen_und_antworten.php#weshalb-telegram-push' class='linkwhite'>Weshalb Telegram?</a></p>
            </form>
        ZZZZZZZZZZ;
    }

    echo <<<ZZZZZZZZZZ
    <div class="telegram-card text-white bg-telegram mb-2">
        <h5 class="card-header">
            <img src='{$code_href}icns/login_telegram.svg' alt=''>
            Nachrichten-Push
        </h5>
        <div class="card-body">
            {$content}
        </div>
    </div>
    ZZZZZZZZZZ;
}
