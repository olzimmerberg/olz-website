<?php

namespace Olz\Apps\Newsletter\Components\OlzEmailCard;

use Olz\Apps\Newsletter\Components\OlzNotificationSubscriptionsForm\OlzNotificationSubscriptionsForm;
use Olz\Components\Common\OlzComponent;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\User;

class OlzEmailCard extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $entityManager = $this->dbUtils()->getEntityManager();
        $user_repo = $entityManager->getRepository(User::class);
        $user = $this->authUtils()->getCurrentUser();

        if ($user) {
            $user_email = $user->getEmail();
            $has_email = $user_email !== null && strlen($user_email) > 0;
            $is_email_verified = (
                $user->isEmailVerified()
                || $this->authUtils()->hasPermission('verified_email', $user)
            );

            $notification_subscription_repo = $entityManager->getRepository(NotificationSubscription::class);
            $subscriptions = $notification_subscription_repo->findBy(['user' => $user, 'delivery_type' => 'email']);
            $form = OlzNotificationSubscriptionsForm::render(['subscriptions' => $subscriptions]);

            if (!$has_email) {
                $content = <<<ZZZZZZZZZZ
                        <p class="card-text">Nichts verpassen mit dem E-Mail Newsletter in deiner Mailbox!</p>
                        <p class="card-text text-end">
                            <a 
                                href="{$code_href}profil"
                                class="btn btn-light btn-sm"
                            >
                                E-Mail Adresse eintragen
                            </a>
                        </p>
                    ZZZZZZZZZZ;
            } elseif (!$is_email_verified) {
                $content = <<<ZZZZZZZZZZ
                        <p class="card-text">Nichts verpassen mit dem E-Mail Newsletter in deiner Mailbox!</p>
                        <p class="card-text text-end">
                            <a 
                                href="{$code_href}profil"
                                class="btn btn-light btn-sm"
                            >
                                E-Mail Adresse bestätigen
                            </a>
                        </p>
                    ZZZZZZZZZZ;
            } else {
                $content = <<<ZZZZZZZZZZ
                        <form id='email-notifications-form' onsubmit='return olzNewsletter.olzEmailNotificationsUpdate(this)'>
                            <p class='card-title'><b>Du hast folgende Benachrichtigungen aktiviert:</b></p>
                            {$form}
                            <p class="card-text text-end">
                                <button
                                    id='email-notifications-submit'
                                    type='submit'
                                    class='btn btn-light btn-sm'
                                >
                                    Speichern
                                </button>
                            </p>
                            <div id='email-notifications-success-message' class='alert alert-success' role='alert'></div>
                            <div id='email-notifications-error-message' class='alert alert-danger' role='alert'></div>
                        </form>
                    ZZZZZZZZZZ;
            }

            return <<<ZZZZZZZZZZ
                <div class="email-card card text-white bg-email mb-2">
                    <h5 class="card-header">
                        <img src='{$code_href}assets/icns/login_mail.svg' alt=''>
                        E-Mail Newsletter
                    </h5>
                    <div class="card-body">
                        {$content}
                    </div>
                </div>
                ZZZZZZZZZZ;
        }
        return '';
    }
}
