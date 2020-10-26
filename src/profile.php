<?php

if (!defined('CALLED_THROUGH_INDEX')) {
    require_once __DIR__.'/admin/olz_init.php';
    require_once __DIR__.'/admin/olz_functions.php';
    include __DIR__.'/components/page/olz_header/olz_header.php';
}

require_once __DIR__.'/config/doctrine.php';
require_once __DIR__.'/model/index.php';

$user_repo = $entityManager->getRepository(User::class);
$username = $_SESSION['user'];
$user = $user_repo->findOneBy(['username' => $username]);

include __DIR__."/components/auth/olz_change_password_modal/olz_change_password_modal.php";

echo "<div id='content_double'>";
if ($user) {
    $esc_id = htmlentities(json_encode($user->getId()));
    $esc_first_name = htmlentities($user->getFirstName());
    $esc_last_name = htmlentities($user->getLastName());
    $esc_username = htmlentities($user->getUsername());
    $esc_email = htmlentities($user->getEmail());
    echo <<<ZZZZZZZZZZ
<form id='profile-form' onsubmit='return olzProfileUpdateUser({$esc_id}, this)'>
    <div id='profile-update-success-message' class='alert alert-success' role='alert'></div>
    <div class='row'>
        <div class='col form-group'>
            <label for='profile-first-name-input'>Vorname</label>
            <input
                type='text'
                name='first-name'
                value='{$esc_first_name}'
                class='form-control'
                id='profile-first-name-input'
            />
        </div>
        <div class='col form-group'>
            <label for='profile-last-name-input'>Nachname</label>
            <input
                type='text'
                name='last-name'
                value='{$esc_last_name}'
                class='form-control'
                id='profile-last-name-input'
            />
        </div>
    </div>
    <div class='row'>
        <div class='col form-group'>
            <label for='profile-username-input'>Benutzername</label>
            <input
                type='text'
                name='username'
                value='{$esc_username}'
                class='form-control'
                id='profile-username-input'
            />
        </div>
        <div class='col change-password-cell'>
            <button
                type='button'
                class='btn btn-secondary'
                data-toggle='modal'
                data-target='#change-password-modal'
                id='change-password-button'
            >
                Passwort ändern
            </button>
        </div>
    </div>
    <div class='form-group'>
        <label for='profile-email-input'>E-Mail</label>
        <input
            type='email'
            name='email'
            value='{$esc_email}'
            class='form-control'
            id='profile-email-input'
        />
    </div>
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
