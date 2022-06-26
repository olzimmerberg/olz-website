<?php

namespace Olz\Components\Auth\OlzResetPasswordModal;

class OlzResetPasswordModal {
    public static function render($args = []) {
        return <<<'ZZZZZZZZZZ'
        <div class='modal fade' id='reset-password-modal' tabindex='-1' aria-labelledby='reset-password-modal-label' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <form
                        id='reset-password-form'
                        class='default-form'
                        onsubmit='return olzResetPasswordModalReset(this);'
                    >
                        <div class='modal-header'>
                            <h5 class='modal-title' id='reset-password-modal-label'>Passwort zurücksetzen</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div class='modal-body'>
                            <div class='mb-3'>
                                <label for='reset-password-username-input'>
                                    Benutzername oder E-Mail
                                    <a
                                        href='fragen_und_antworten.php#benutzername-email-herausfinden'
                                        class='help-link'
                                    >
                                        Vergessen?
                                    </a>
                                </label>
                                <input
                                    type='text'
                                    class='form-control'
                                    id='reset-password-username-input'
                                    name='username_or_email'
                                    autofocus
                                />
                            </div>
                            <input type='submit' class='hidden' />
                            <div class='error-message alert alert-danger' role='alert'></div>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                            <button id='reset-password-submit-button' type='submit' class='btn btn-primary'>E-Mail senden</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        ZZZZZZZZZZ;
    }
}