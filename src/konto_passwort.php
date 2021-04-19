<?php

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/auth/olz_profile_form/olz_profile_form.php';
require_once __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "OLZ-Konto mit Passwort",
    'description' => "OLZ-Login mit Passwort.",
    'norobots' => true,
]);

echo "<div id='content_double'>
<div>";

echo <<<'ZZZZZZZZZZ'
<form
    id='sign-up-with-password-form'
    class='default-form'
    onsubmit='return olzKontoSignUpWithPassword(this)'
>
    <div class='success-message alert alert-success' role='alert'></div>
ZZZZZZZZZZ;
echo olz_profile_form([
    'show_required_password' => true,
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
