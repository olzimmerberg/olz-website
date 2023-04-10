<?php

namespace Olz\Components\Auth\OlzResetPasswordModal;

use Olz\Components\Common\OlzComponent;

class OlzResetPasswordModal extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        return <<<ZZZZZZZZZZ
        <div class='modal fade' id='reset-password-modal' tabindex='-1' aria-labelledby='reset-password-modal-label' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <form
                        id='reset-password-form'
                        class='default-form'
                        onsubmit='return olz.olzResetPasswordModalReset(this);'
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
                                    name='username-or-email'
                                    autofocus
                                />
                            </div>
                            <p><input type='checkbox' name='recaptcha-consent-given' onchange='olz.olzResetPasswordRecaptchaConsent(this.checked)'> Ich akzeptiere, dass beim Zurücksetzen des Passworts einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden. <a href='{$code_href}datenschutz.php' target='_blank'>Weitere Informationen zum Datenschutz</a></p>
                            <div class='error-message alert alert-danger' role='alert'></div>
                            <div class='success-message alert alert-success' role='alert'></div>
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
