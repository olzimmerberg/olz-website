<?php

use Olz\Components\Auth\OlzProfileForm\OlzProfileForm;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\StravaLink;
use Olz\Entity\TelegramLink;
use Olz\Entity\User;
use Olz\Utils\DbUtils;

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "Profil",
    'description' => "Alles rund um dein persönliches OLZ-Konto.",
    'norobots' => true,
]);

$entityManager = DbUtils::fromEnv()->getEntityManager();
$user_repo = $entityManager->getRepository(User::class);
$username = ($_SESSION['user'] ?? null);
$user = $user_repo->findOneBy(['username' => $username]);

echo "<div class='content-full profile'>";
if ($user) {
    $telegram_link_repo = $entityManager->getRepository(TelegramLink::class);
    $telegram_link = $telegram_link_repo->findOneBy(['user' => $user]);
    $has_telegram_link = $telegram_link && $telegram_link->getTelegramChatId() !== null;
    $telegram_button_class = $has_telegram_link ? ' active' : '';

    $strava_link_repo = $entityManager->getRepository(StravaLink::class);
    $strava_link = $strava_link_repo->findOneBy(['user' => $user]);
    $has_strava_link = $strava_link !== null;
    $strava_button_class = $has_strava_link ? ' active' : '';

    $user_id = $user->getId();
    $esc_id = htmlentities(json_encode($user_id));
    echo <<<ZZZZZZZZZZ
    <div class='feature external-login mb-3'>
        <a
            href='#'
            role='button'
            data-bs-toggle='modal'
            data-bs-target='#link-telegram-modal'
            class='login-button telegram-button{$telegram_button_class}'
        >
            <img src='{$code_href}icns/login_telegram.svg' alt=''>
            Nachrichten-Push via Telegram
        </a>
        <a
            href='#'
            role='button'
            data-bs-toggle='modal'
            data-bs-target='#link-strava-modal'
            class='login-button strava-button{$strava_button_class}'
        >
            <img src='{$code_href}icns/login_strava.svg' alt=''>
            Login mit Strava
        </a>
    </div>

    <div class='data-protection-section'>
        <button
            id='delete-user-button'
            class='btn btn-danger'
            onclick='return olz.olzProfileDeleteUser({$esc_id})'
        >
            <img src='icns/delete_white_16.svg' class='noborder' />
            Konto löschen
        </button>
        <p><b>Wir behandeln deine Daten vertraulich und verwenden sie sparsam</b>: <a href='datenschutz.php' class='linkint' target='_blank'>Datenschutz</a></p>
        <p><span class='required-field-asterisk'>*</span> Zwingend notwendige Felder sind mit einem roten Sternchen gekennzeichnet.</p>
    </div>
    <div class='after-data-protection-section'></div>

    <form
        id='profile-form'
        class='default-form'
        onsubmit='return olz.olzProfileUpdateUser({$esc_id}, this)'
    >
        <div class='success-message alert alert-success' role='alert'></div>
        <input
            type='hidden'
            name='id'
            value='{$esc_id}'
        />
    ZZZZZZZZZZ;
    echo OlzProfileForm::render([
        'show_avatar' => true,
        'show_change_password' => true,
        'first_name' => $user->getFirstName(),
        'last_name' => $user->getLastName(),
        'username' => $user->getUsername(),
        'email' => $user->getEmail(),
        'phone' => $user->getPhone(),
        'gender' => $user->getGender(),
        'birthdate' => $user->getBirthdate(),
        'street' => $user->getStreet(),
        'postal_code' => $user->getPostalCode(),
        'city' => $user->getCity(),
        'region' => $user->getRegion(),
        'country_code' => $user->getCountryCode(),
        'si_card_number' => $user->getSiCardNumber(),
        'solv_number' => $user->getSolvNumber(),
    ]);
    echo <<<'ZZZZZZZZZZ'
        <button type='submit' class='btn btn-primary'>Speichern</button>
        <div class='error-message alert alert-danger' role='alert'></div>
    </form>
    ZZZZZZZZZZ;
} else {
    echo "<div id='profile-message' class='alert alert-danger' role='alert'>Da musst du schon eingeloggt sein!</div>";
}
echo "</div>";

echo OlzFooter::render();
