<?php

use Olz\Components\Auth\OlzProfileForm\OlzProfileForm;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AuthUtils;

require_once __DIR__.'/config/init.php';
require_once __DIR__.'/config/paths.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "OLZ-Konto mit Passwort",
    'description' => "OLZ-Login mit Passwort.",
    'norobots' => true,
]);

$auth_utils = AuthUtils::fromEnv();
$user = $auth_utils->getCurrentUser();

$title = $user ? "Neues Familienmitglied" : "OLZ-Konto erstellen";
$defaults = $user ? [
    'phone' => $user->getPhone() ?? '',
    'street' => $user->getStreet() ?? '',
    'postal_code' => $user->getPostalCode() ?? '',
    'city' => $user->getCity() ?? '',
    'region' => $user->getRegion() ?? '',
    'country_code' => $user->getCountryCode() ?? '',
] : [];

echo "<div class='content-full'>
<div>";

echo <<<ZZZZZZZZZZ
<h1>{$title}</h1>
<p><b>Wir behandeln deine Daten vertraulich und verwenden sie sparsam</b>: <a href='datenschutz.php' class='linkint' target='_blank'>Datenschutz</a></p>
<p><span class='required-field-asterisk'>*</span> Zwingend notwendige Felder sind mit einem roten Sternchen gekennzeichnet.</p>
<form
    id='sign-up-with-password-form'
    class='default-form'
    onsubmit='return olz.olzKontoSignUpWithPassword(this)'
>
    <div class='success-message alert alert-success' role='alert'></div>
ZZZZZZZZZZ;
echo OlzProfileForm::render([
    'show_avatar' => false,
    'show_required_email' => $user ? false : true,
    'show_required_password' => $user ? false : true,
    ...$defaults,
]);
echo <<<ZZZZZZZZZZ
    <p><input type='checkbox' name='recaptcha-consent-given' onchange='olz.olzSignUpRecaptchaConsent(this.checked)'> <span class='required-field-asterisk'>*</span> Ich akzeptiere, dass beim Erstellen des Kontos einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.</p>
    <p><input type='checkbox' name='cookie-consent-given'> <span class='required-field-asterisk'>*</span> Ich nehme zur Kenntnis, dass bei jedem Login notgedrungen ein Cookie in meinem Browser gesetzt wird. <a href='{$code_href}datenschutz.php' target='_blank'>Weitere Informationen zum Datenschutz</a></p>
    <button id='sign-up-with-password-submit-button' type='submit' class='btn btn-primary'>Konto erstellen</button>
    <div class='error-message alert alert-danger' role='alert'></div>
</form>
ZZZZZZZZZZ;

echo "</div>
</div>";

echo OlzFooter::render();
