<?php

use Olz\Components\Auth\OlzProfileForm\OlzProfileForm;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\GoogleUtils;

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "Google Konto",
    'description' => "OLZ-Login mit Google.",
    'norobots' => true,
]);

echo "<div id='content_double'>
<div>";

$code = $_GET['code'];

$google_utils = GoogleUtils::fromEnv();
$token_data = $google_utils->getTokenDataForCode($code);
$user_data = $google_utils->getUserData($token_data);

echo "<pre>";
print_r($user_data);
echo "</pre><br/>";

echo OlzProfileForm::render([
    'first_name' => $user_data['first_name'],
    'last_name' => $user_data['last_name'],
    'email' => $user_data['email'],
    'gender' => $user_data['gender'],
    'birthdate' => $user_data['birthday'],
    'city' => $user_data['city'],
    'region' => $user_data['region'],
    'country_code' => 'CH',
]);

echo "</div>
</div>";

echo OlzFooter::render();
