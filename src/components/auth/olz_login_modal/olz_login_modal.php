<?php

require_once __DIR__.'/../../../utils/auth/GoogleUtils.php';
require_once __DIR__.'/../../../utils/auth/FacebookUtils.php';
require_once __DIR__.'/../../../utils/auth/StravaUtils.php';

$strava_utils = getStravaUtilsFromEnv();
$google_utils = getGoogleUtilsFromEnv();
$facebook_utils = getFacebookUtilsFromEnv();
$strava_url = $strava_utils->getAuthUrl();
$google_url = $google_utils->getAuthUrl();
$facebook_url = $facebook_utils->getAuthUrl();

echo <<<ZZZZZZZZZZ
<div class='modal fade' id='login-modal' tabindex='-1' aria-labelledby='login-modal-label' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <form onsubmit='olzLoginModalLogin();return false;'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='login-modal-label'>Login</h5>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Schliessen'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <div class='feature external-login'>
                        <div><a href='{$strava_url}'>Login mit Strava</a></div>
                        <div><a href='{$google_url}'>Login mit Google</a></div>
                        <div><a href='{$facebook_url}'>Login mit Facebook</a></div>
                        <br />
                    </div>
                    <div class='form-group'>
                        <label for='login-username-input'>Benutzername</label>
                        <input type='text' class='form-control test-flaky' id='login-username-input' autofocus />
                    </div>
                    <div class='form-group'>
                        <label for='login-password-input'>Passwort</label>
                        <input type='password' class='form-control' id='login-password-input' />
                    </div>
                    <input type='submit' class='hidden' />
                    <div id='login-message' class='alert alert-danger' role='alert'></div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Abbrechen</button>
                    <button type='submit' class='btn btn-primary' id='login-button'>Login</button>
                </div>
            </form>
        </div>
    </div>
</div>
ZZZZZZZZZZ;
