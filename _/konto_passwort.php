<?php

use Olz\Components\Auth\OlzProfileForm\OlzProfileForm;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

require_once __DIR__.'/config/init.php';
require_once __DIR__.'/config/paths.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "OLZ-Konto mit Passwort",
    'description' => "OLZ-Login mit Passwort.",
    'norobots' => true,
]);

echo "<div class='content-full'>
<div>";

echo <<<'ZZZZZZZZZZ'
<h1>OLZ-Konto erstellen</h1>
<p><b>Wir behandeln deine Daten vertraulich und verwenden sie sparsam</b>: <a href='datenschutz.php' class='linkint' target='_blank'>Datenschutz</a></p>
<form
    id='sign-up-with-password-form'
    class='default-form'
    onsubmit='return olz.olzKontoSignUpWithPassword(this)'
>
    <div class='success-message alert alert-success' role='alert'></div>
ZZZZZZZZZZ;
echo OlzProfileForm::render([
    'show_avatar' => false,
    'show_required_password' => true,
]);
echo <<<ZZZZZZZZZZ
    <p><input type='checkbox' name='consent-given' onchange='olz.olzSignUpConsent(this.checked)'> <span class='required-field-asterisk'>*</span> Ich akzeptiere, dass beim Erstellen des Kontos einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden. Ich nehme zur Kenntnis, dass bei jedem Login notgedrungen ein Cookie in meinem Browser gesetzt wird. <a href='{$code_href}datenschutz.php' target='_blank'>Weitere Informationen zum Datenschutz</a></p>
    <p><span class='required-field-asterisk'>*</span> Zwingend notwendige Felder sind mit einem roten Sternchen gekennzeichnet.</p>
    <button id='sign-up-with-password-submit-button' type='submit' class='btn btn-primary'>Konto erstellen</button>
    <div class='error-message alert alert-danger' role='alert'></div>
</form>
ZZZZZZZZZZ;

echo "</div>
</div>";

echo OlzFooter::render();
