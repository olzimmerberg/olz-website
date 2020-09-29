<?php

require_once __DIR__.'/../../../config/doctrine.php';
require_once __DIR__.'/../../../model/index.php';

$user_repo = $entityManager->getRepository(User::class);
$username = $_SESSION['user'];
$user = $user_repo->findOneBy(['username' => $username]);

if ($user) {
    $esc_id = htmlentities(json_encode($user->getId()));

    echo <<<ZZZZZZZZZZ
    <div class='modal fade' id='change-password-modal' tabindex='-1' aria-labelledby='change-password-modal-label' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            <form id='change-password-form' onsubmit='return olzChangePasswordModalUpdate({$esc_id}, this)'>
                <div class='modal-header'>
                <h5 class='modal-title' id='change-password-modal-label'>Passwort ändern</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Schliessen'>
                    <span aria-hidden='true'>&times;</span>
                </button>
                </div>
                <div class='modal-body'>
                <div class='form-group'>
                    <label for='change-password-old-input'>Bisheriges Passwort</label>
                    <input type='password' name='old' class='form-control test-flaky' id='change-password-old-input' />
                </div>
                <div class='form-group'>
                    <label for='change-password-new-input'>Neues Passwort</label>
                    <input type='password' name='new' class='form-control' id='change-password-new-input' />
                </div>
                <div class='form-group'>
                    <label for='change-password-repeat-input'>Neues Passwort wiederholen</label>
                    <input type='password' name='repeat' class='form-control' id='change-password-repeat-input' />
                </div>
                <input type='submit' class='hidden' />
                <div id='login-message' class='alert alert-danger' role='alert'></div>
                </div>
                <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Abbrechen</button>
                <button type='submit' class='btn btn-primary'>Passwort ändern</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    ZZZZZZZZZZ;
}
