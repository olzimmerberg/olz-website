<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/config/init.php';

    session_start();

    require_once __DIR__.'/admin/olz_functions.php';
    include __DIR__.'/components/page/olz_header/olz_header.php';
}

require_once __DIR__.'/components/auth/olz_profile_form/olz_profile_form.php';
require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';

$user_repo = $entityManager->getRepository(User::class);
$username = $_SESSION['user'];
$user = $user_repo->findOneBy(['username' => $username]);

echo "<div id='content_double'>";
if ($user) {
    $user_id = $user->getId();
    $esc_id = htmlentities(json_encode($user_id));
    echo <<<ZZZZZZZZZZ
    <a 
        href='#'
        role='button'
        data-toggle='modal'
        data-target='#link-telegram-modal'
    >
        Telegram-Infos aktivieren
    </a>

    <form id='profile-form' onsubmit='return olzProfileUpdateUser({$esc_id}, this)'>
        <div id='profile-update-success-message' class='alert alert-success' role='alert'></div>
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
        'gender' => $user->getGender(),
        'birthdate' => $user->getBirthdate(),
        'street' => $user->getStreet(),
        'postal_code' => $user->getPostalCode(),
        'city' => $user->getCity(),
        'region' => $user->getRegion(),
        'country_code' => $user->getCountryCode(),
    ]);
    echo <<<'ZZZZZZZZZZ'
        <button type='submit' class='btn btn-primary'>Speichern</button>
        <div id='profile-update-error-message' class='alert alert-danger' role='alert'></div>
    </form>
    ZZZZZZZZZZ;
} else {
    echo "<div id='profile-message' class='alert alert-danger' role='alert'>Da musst du schon eingeloggt sein!</div>";
}
echo "</div>";

if (!defined('CALLED_THROUGH_INDEX')) {
    include __DIR__.'/components/page/olz_footer/olz_footer.php';
}
