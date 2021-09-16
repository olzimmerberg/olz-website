<?php

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "Profil",
    'description' => "Alles rund um dein persönliches OLZ-Konto.",
    'norobots' => true,
]);

require_once __DIR__.'/components/auth/olz_profile_form/olz_profile_form.php';
require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';

$user_repo = $entityManager->getRepository(User::class);
$username = ($_SESSION['user'] ?? null);
$user = $user_repo->findOneBy(['username' => $username]);

echo "<div id='content_double' class='profile'>";
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
            data-toggle='modal'
            data-target='#link-telegram-modal'
            class='login-button telegram-button{$telegram_button_class}'
        >
            <img src='{$code_href}icns/login_telegram.svg' alt=''>
            Nachrichten-Push via Telegram
        </a>
        <a
            href='#'
            role='button'
            data-toggle='modal'
            data-target='#link-telegram-modal'
            class='login-button strava-button{$strava_button_class}'
        >
            <img src='{$code_href}icns/login_strava.svg' alt=''>
            Login mit Strava
        </a>
    </div>

    <p><b>Wir behandeln deine Daten vertraulich und verwenden sie sparsam</b>: <a href='datenschutz.php' class='linkint' target='_blank'>Datenschutz</a></p>

    <form
        id='profile-form'
        class='default-form'
        onsubmit='return olzProfileUpdateUser({$esc_id}, this)'
    >
        <div class='success-message alert alert-success' role='alert'></div>
        <input
            type='hidden'
            name='id'
            value='{$esc_id}'
        />
    ZZZZZZZZZZ;
    echo olz_profile_form([
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
    ]);
    echo <<<'ZZZZZZZZZZ'
        <p><span class='required-field-asterisk'>*</span> Zwingend notwendige Felder sind mit einem roten Sternchen gekennzeichnet.</p>
        <button type='submit' class='btn btn-primary'>Speichern</button>
        <div class='error-message alert alert-danger' role='alert'></div>
    </form>
    ZZZZZZZZZZ;
} else {
    echo "<div id='profile-message' class='alert alert-danger' role='alert'>Da musst du schon eingeloggt sein!</div>";
}
echo "</div>";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
