<?php

use Olz\Components\Auth\OlzProfileForm\OlzProfileForm;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\FacebookUtils;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';
echo OlzHeader::render([
    'title' => "Facebook Konto",
    'description' => "OLZ-Login mit Facebook.",
    'norobots' => true,
]);

echo "<div class='content-full'>
<div>";

$code = $_GET['code'];

$facebook_utils = FacebookUtils::fromEnv();
$token_data = $facebook_utils->getTokenDataForCode($code);
$user_data = $facebook_utils->getUserData($token_data);

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
