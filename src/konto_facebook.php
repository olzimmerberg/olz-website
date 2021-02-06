<?php

require_once __DIR__.'/config/init.php';

session_start();

require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/auth/olz_profile_form/olz_profile_form.php';
require_once __DIR__.'/utils/auth/FacebookUtils.php';
include __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header(['title' => "Facebook Konto"]);

echo "<div id='content_double'>
<div>";

$code = $_GET['code'];

$facebook_utils = getFacebookUtilsFromEnv();
$token_data = $facebook_utils->getTokenDataForCode($code);
$user_data = $facebook_utils->getUserData($token_data);

echo "<pre>";
print_r($user_data);
echo "</pre><br/>";

echo olz_profile_form([
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

include __DIR__.'/components/page/olz_footer/olz_footer.php';
