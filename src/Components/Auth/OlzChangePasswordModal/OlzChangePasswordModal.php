<?php

namespace Olz\Components\Auth\OlzChangePasswordModal;

use Olz\Entity\User;
use Olz\Utils\DbUtils;

class OlzChangePasswordModal {
    public static function render($args = []) {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $user_repo = $entityManager->getRepository(User::class);
        $username = ($_SESSION['user'] ?? null);
        $user = $user_repo->findOneBy(['username' => $username]);

        if ($user) {
            $esc_id = htmlentities(json_encode($user->getId()));

            return <<<ZZZZZZZZZZ
            <div class='modal fade' id='change-password-modal' tabindex='-1' aria-labelledby='change-password-modal-label' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <form
                            id='change-password-form'
                            class='default-form'
                            onsubmit='return olzChangePasswordModalUpdate({$esc_id}, this);'
                        >
                            <div class='modal-header'>
                                <h5 class='modal-title' id='change-password-modal-label'>Passwort ändern</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                            </div>
                            <div class='modal-body'>
                                <div class='mb-3'>
                                    <label for='change-password-old-input'>Bisheriges Passwort</label>
                                    <input type='password' name='old' class='form-control test-flaky' id='change-password-old-input' />
                                </div>
                                <div class='mb-3'>
                                    <label for='change-password-new-input'>Neues Passwort</label>
                                    <input type='password' name='new' class='form-control' id='change-password-new-input' />
                                </div>
                                <div class='mb-3'>
                                    <label for='change-password-repeat-input'>Neues Passwort wiederholen</label>
                                    <input type='password' name='repeat' class='form-control' id='change-password-repeat-input' />
                                </div>
                                <input type='submit' class='hidden' />
                                <div class='error-message alert alert-danger' role='alert'></div>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                                <button id='change-password-submit-button' type='submit' class='btn btn-primary'>Passwort ändern</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            ZZZZZZZZZZ;
        }
        return '';
    }
}
