<?php

use Olz\Components\Auth\OlzProfileForm\OlzProfileForm;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "Strava Konto",
    'description' => "OLZ-Login mit Strava.",
    'norobots' => true,
]);

echo "<div class='content-full'>
<div>";

$code = $_GET['code'];
$granted_scope = $_GET['scope'];

$js_code = json_encode($code);

echo <<<ZZZZZZZZZZ
<script>olz.olzKontoLoginWithStrava({$js_code})</script>
<div id='sign-up-with-strava-login-status' class='alert alert-secondary'>Login mit Strava wird gestartet...</div>
<form
    id='sign-up-with-strava-form'
    class='default-form hidden'
    autocomplete='off'
    onsubmit='return olz.olzKontoSignUpWithStrava(this)'
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
echo OlzProfileForm::render([
    'country_code' => 'CH',
]);
echo <<<'ZZZZZZZZZZ'
    <button type='submit' class='btn btn-primary'>Konto erstellen</button>
    <div class='error-message alert alert-danger' role='alert'></div>
</form>
ZZZZZZZZZZ;

echo "</div>
</div>";

echo OlzFooter::render();
