<?php

namespace Olz\Components\Auth\OlzSignUpModal;

use Olz\Components\Common\OlzComponent;
use Olz\Utils\StravaUtils;

class OlzSignUpModal extends OlzComponent {
    public function getHtml($args = []): string {
        $strava_utils = StravaUtils::fromEnv();
        $strava_url = $strava_utils->getAuthUrl();

        $code_href = $this->envUtils()->getCodeHref();

        return <<<ZZZZZZZZZZ
        <div class='modal fade' id='sign-up-modal' tabindex='-1' aria-labelledby='sign-up-modal-label' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='sign-up-modal-label'>Login</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                    </div>
                    <div class='modal-body'>
                        <div class='feature external-login mb-3'>
                            <a href='{$strava_url}' class='login-button strava-button'>
                                <img src='{$code_href}assets/icns/login_strava.svg' alt=''>
                                Strava
                            </a>
                            <a href='{$code_href}konto_passwort' class='login-button password-button'>
                                <img src='{$code_href}assets/icns/login_password.svg' alt=''>
                                Passwort
                            </a>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Abbrechen</button>
                    </div>
                </div>
            </div>
        </div>
        ZZZZZZZZZZ;
    }
}
