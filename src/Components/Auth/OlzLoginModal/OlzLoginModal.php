<?php

namespace Olz\Components\Auth\OlzLoginModal;

use Olz\Components\Common\OlzComponent;
use Olz\Utils\FacebookUtils;
use Olz\Utils\GoogleUtils;
use Olz\Utils\StravaUtils;

class OlzLoginModal extends OlzComponent {
    public function getHtml($args = []): string {
        $strava_utils = StravaUtils::fromEnv();
        $google_utils = GoogleUtils::fromEnv();
        $facebook_utils = FacebookUtils::fromEnv();
        $strava_url = $strava_utils->getAuthUrl();
        $google_url = $google_utils->getAuthUrl();
        $facebook_url = $facebook_utils->getAuthUrl();

        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();

        return <<<ZZZZZZZZZZ
        <div class='modal fade' id='login-modal' tabindex='-1' aria-labelledby='login-modal-label' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <form onsubmit='olz.olzLoginModalLogin();return false;'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='login-modal-label'>Login</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Schliessen'></button>
                        </div>
                        <div class='modal-body'>
                            <div class='feature external-login mb-3'>
                                <a href='{$strava_url}' class='login-button strava-button'>
                                    <img src='{$data_href}assets/icns/login_strava.svg' alt=''>
                                    Strava
                                </a>
                                <a href='{$google_url}' class='login-button google-button'>
                                    <img src='{$data_href}assets/icns/login_google.svg' alt=''>
                                    Google
                                </a>
                                <a href='{$facebook_url}' class='login-button facebook-button'>
                                    <img src='{$data_href}assets/icns/login_facebook.svg' alt=''>
                                    Facebook
                                </a>
                                <br />
                            </div>
                            <div class='mb-3'>
                                <label for='login-username-input'>Benutzername oder E-Mail</label>
                                <input type='text' class='form-control test-flaky' id='login-username-input' autofocus autofill='username' />
                            </div>
                            <div class='mb-3'>
                                <label for='login-password-input'>Passwort</label>
                                <input type='password' class='form-control' id='login-password-input' autofill='current-password' />
                            </div>
                            <div class='mb-3 remember-me-row'>
                                <input type='checkbox' id='login-remember-me-input' />
                                <label for='login-remember-me-input'>Eingeloggt bleiben</label>
                            </div>
                            <div class='mb-3'>
                                <a
                                    id='reset-password-link'
                                    href='#'
                                    data-bs-dismiss='modal'
                                    data-bs-toggle='modal'
                                    data-bs-target='#reset-password-modal'
                                >
                                    Passwort vergessen?
                                </a>
                            </div>
                            <div class='mb-3'>
                                <a
                                    id='sign-up-link'
                                    href='{$code_href}konto_passwort.php'
                                >
                                    Noch kein OLZ-Konto?
                                </a>
                            </div>
                            <div class='feature external-login mb-3'>
                                <a
                                    id='external-sign-up-link'
                                    href='#'
                                    data-bs-dismiss='modal'
                                    data-bs-toggle='modal'
                                    data-bs-target='#sign-up-modal'
                                >
                                    Noch kein OLZ-Konto? (extern)
                                </a>
                            </div>
                            <input type='submit' class='hidden' />
                            <div id='login-message' class='alert alert-danger' role='alert'></div>
                        </div>
                        <div class='modal-footer'>
                            <button
                                type='button'
                                class='btn btn-secondary'
                                data-bs-dismiss='modal'
                                onclick='olz.olzLoginModalCancel()'
                            >
                                Abbrechen
                            </button>
                            <button
                                type='submit'
                                class='btn btn-primary'
                                id='login-button'
                            >
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        ZZZZZZZZZZ;
    }
}
