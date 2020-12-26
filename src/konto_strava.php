<?php

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_init.php';
require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/auth/olz_profile_form/olz_profile_form.php';
require_once __DIR__.'/utils/auth/StravaUtils.php';
$html_titel = " - Strava Konto";
include __DIR__.'/components/page/olz_header/olz_header.php';

echo "<div id='content_double'>
<div>";

$code = $_GET['code'];
$granted_scope = $_GET['scope'];

$js_code = json_encode($code);

echo <<<ZZZZZZZZZZ
<script>olzKontoLoginWithStrava({$js_code})</script>
<div id='sign-up-with-strava-login-status' class='alert alert-secondary'>Login mit Strava wird gestartet...</div>
<form id='sign-up-with-strava-form' onsubmit='return olzKontoSignUpWithStrava(this)' class='hidden'>
    <div id='sign-up-with-strava-success-message' class='alert alert-success' role='alert'></div>
    <input
        type='hidden'
        name='strava-user'
    />
    <input
        type='hidden'
        name='access-token'
    />
    <input
        type='hidden'
        name='refresh-token'
    />
    <input
        type='hidden'
        name='expires-at'
    />
ZZZZZZZZZZ;
echo olz_profile_form([
    'country_code' => 'CH',
]);
echo <<<'ZZZZZZZZZZ'
    <button type='submit' class='btn btn-primary'>Konto erstellen</button>
    <div id='sign-up-with-strava-error-message' class='alert alert-danger' role='alert'></div>
</form>
ZZZZZZZZZZ;

echo "</div>
</div>";

include __DIR__.'/components/page/olz_footer/olz_footer.php';
