<?php

use Olz\Utils\FacebookUtils;
use Olz\Utils\GoogleUtils;
use Olz\Utils\StravaUtils;

function olz_sign_up_modal($args = []): string {
    global $_CONFIG;

    require_once __DIR__.'/../../../config/server.php';

    $strava_utils = StravaUtils::fromEnv();
    $google_utils = GoogleUtils::fromEnv();
    $facebook_utils = FacebookUtils::fromEnv();
    $strava_url = $strava_utils->getAuthUrl();
    $google_url = $google_utils->getAuthUrl();
    $facebook_url = $facebook_utils->getAuthUrl();

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
                            <img src='{$_CONFIG->getCodeHref()}icns/login_strava.svg' alt=''>
                            Strava
                        </a>
                        <a href='{$google_url}' class='login-button google-button'>
                            <img src='{$_CONFIG->getCodeHref()}icns/login_google.svg' alt=''>
                            Google
                        </a>
                        <a href='{$facebook_url}' class='login-button facebook-button'>
                            <img src='{$_CONFIG->getCodeHref()}icns/login_facebook.svg' alt=''>
                            Facebook
                        </a>
                        <a href='{$_CONFIG->getCodeHref()}konto_passwort.php' class='login-button password-button'>
                            <img src='{$_CONFIG->getCodeHref()}icns/login_password.svg' alt=''>
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
