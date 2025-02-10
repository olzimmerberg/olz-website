<?php

namespace Olz\Apps\Newsletter\Components\OlzTelegramCard;

use Olz\Apps\Newsletter\Components\OlzNotificationSubscriptionsForm\OlzNotificationSubscriptionsForm;
use Olz\Components\Common\OlzComponent;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;

/** @extends OlzComponent<array<string, mixed>> */
class OlzTelegramCard extends OlzComponent {
    public function getHtml(mixed $args): string {
        $entityManager = $this->dbUtils()->getEntityManager();
        $user = $this->authUtils()->getCurrentUser();
        $code_href = $this->envUtils()->getCodeHref();

        if ($user) {
            $telegram_link_repo = $entityManager->getRepository(TelegramLink::class);
            $telegram_link = $telegram_link_repo->findOneBy(['user' => $user]);
            $has_telegram_link = $telegram_link && $telegram_link->getTelegramChatId() !== null;

            $notification_subscription_repo = $entityManager->getRepository(NotificationSubscription::class);
            $subscriptions = $notification_subscription_repo->findBy(['user' => $user, 'delivery_type' => 'telegram']);
            $form = OlzNotificationSubscriptionsForm::render(['subscriptions' => $subscriptions]);

            if (!$has_telegram_link) {
                $content = <<<ZZZZZZZZZZ
                        <p class="card-text">Mit der Chat-App Telegram halten wir dich immer auf dem Laufenden!</p>
                        <p class="card-text text-end">
                            <a 
                                href="#"
                                role="button"
                                onclick="olz.initOlzLinkTelegramModal()"
                                class="btn btn-light btn-sm"
                            >
                                Aktivieren
                            </a>
                        </p>
                        <p class="card-text"><a href='{$code_href}fragen_und_antworten#weshalb-telegram-push' class='linkwhite'>Weshalb Telegram?</a></p>
                    ZZZZZZZZZZ;
            } else {
                $content = <<<ZZZZZZZZZZ
                        <form id='telegram-notifications-form' onsubmit='return olzNewsletter.olzTelegramNotificationsUpdate(this)'>
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
                            <p class="card-text"><a href='{$code_href}fragen_und_antworten#weshalb-telegram-push' class='linkwhite'>Weshalb Telegram?</a></p>
                        </form>
                    ZZZZZZZZZZ;
            }

            return <<<ZZZZZZZZZZ
                <div class="telegram-card card text-white bg-telegram mb-2">
                    <h3 class="card-header">
                        <img src='{$code_href}assets/icns/login_telegram.svg' alt=''>
                        Nachrichten-Push
                    </h3>
                    <div class="card-body">
                        {$content}
                    </div>
                </div>
                ZZZZZZZZZZ;
        }
        return '';
    }
}
