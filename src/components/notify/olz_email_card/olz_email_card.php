<?php

require_once __DIR__.'/../../../config/doctrine_db.php';
require_once __DIR__.'/../../../config/paths.php';
require_once __DIR__.'/../../../model/index.php';
require_once __DIR__.'/../olz_notification_subscriptions_form/olz_notification_subscriptions_form.php';

$user_repo = $entityManager->getRepository(User::class);
$username = ($_SESSION['user'] ?? null);
$user = $user_repo->findOneBy(['username' => $username]);

if ($user) {
    $user_email = $user->getEmail();
    $has_email = $user_email !== null && strlen($user_email) > 0;

    $notification_subscription_repo = $entityManager->getRepository(NotificationSubscription::class);
    $subscriptions = $notification_subscription_repo->findBy(['user' => $user, 'delivery_type' => 'email']);
    $form = olz_notification_subscriptions_form($subscriptions);

    if (!$has_email) {
        $content = <<<'ZZZZZZZZZZ'
            <p class="card-text">Nichts verpassen mit dem E-Mail Newsletter in deiner Mailbox!</p>
            <p class="card-text text-right">
                <a 
                    href="profil.php"
                    class="btn btn-light btn-sm"
                >
                    E-Mail Adresse eintragen
                </a>
            </p>
        ZZZZZZZZZZ;
    } else {
        $content = <<<ZZZZZZZZZZ
            <form id='email-notifications-form' onsubmit='return olzEmailNotificationsUpdate(this)'>
                <p class='card-title'><b>Du hast folgende Benachrichtigungen aktiviert:</b></p>
                {$form}
                <p class="card-text text-right">
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

    echo <<<ZZZZZZZZZZ
    <div class="email-card text-white bg-email mb-2" style="max-width: 50%;">
        <h5 class="card-header">
            <img src='{$code_href}icns/login_mail.svg' alt=''>
            E-Mail Newsletter
        </h5>
        <div class="card-body">
            {$content}
        </div>
    </div>
    ZZZZZZZZZZ;
}
