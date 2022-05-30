<?php

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/auth/olz_profile_form/olz_profile_form.php';
require_once __DIR__.'/utils/auth/StravaUtils.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "Strava Konto",
    'description' => "OLZ-Login mit Strava.",
    'norobots' => true,
]);

echo "<div id='content_double'>
<div>";

$code = $_GET['code'];
$granted_scope = $_GET['scope'];

$js_code = json_encode($code);

echo <<<ZZZZZZZZZZ
<script>olzKontoLoginWithStrava({$js_code})</script>
<div id='sign-up-with-strava-login-status' class='alert alert-secondary'>Login mit Strava wird gestartet...</div>
<form
    id='sign-up-with-strava-form'
    onsubmit='return olzKontoSignUpWithStrava(this)'
    class='default-form hidden'
>
    <div class='success-message alert alert-success' role='alert'></div>
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
    <div class='error-message alert alert-danger' role='alert'></div>
</form>
ZZZZZZZZZZ;

echo "</div>
</div>";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
