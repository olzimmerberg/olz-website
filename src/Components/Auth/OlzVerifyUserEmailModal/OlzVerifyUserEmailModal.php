<?php

namespace Olz\Components\Auth\OlzVerifyUserEmailModal;

use Olz\Components\Common\OlzComponent;
use Olz\Utils\EnvUtils;

class OlzVerifyUserEmailModal extends OlzComponent {
    public function getHtml($args = []): string {
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();
        return <<<ZZZZZZZZZZ
        <div class='modal fade' id='verify-user-email-modal' tabindex='-1' aria-labelledby='verify-user-email-modal-label' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <form
                        id='verify-user-email-form'
                        class='default-form'
                        onsubmit='return olz.olzVerifyUserEmailModalVerify(this);'
                    >
                        <div class='modal-header'>
                            <h5 class='modal-title' id='verify-user-email-modal-label'>E-Mail-Adresse bestätigen</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div class='modal-body'>
                            <p><b>Wir schicken dir ein E-Mail mit dem Betreff "[OLZ] E-Mail bestätigen". Es enthält einen Link, mit dem du dann deine E-Mail-Adresse bestätigen kannst.</b></p>
                            <br/>
                            <p><input type='checkbox' name='recaptcha-consent-given' onchange='olz.olzVerifyUserEmailRecaptchaConsent(this.checked)'> Ich akzeptiere, dass beim Bestätigen der E-Mail-Adresse einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden. <a href='{$code_href}datenschutz.php' target='_blank'>Weitere Informationen zum Datenschutz</a></p>
                            <div class='error-message alert alert-danger' role='alert'></div>
                            <div class='success-message alert alert-success' role='alert'></div>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                            <button id='verify-user-email-submit-button' type='submit' class='btn btn-primary'>E-Mail senden</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        ZZZZZZZZZZ;
    }
}
