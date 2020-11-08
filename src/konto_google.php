<?php

session_start();

require_once __DIR__.'/admin/olz_init.php';
require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/utils/auth/GoogleUtils.php';
$html_titel = " - Google Konto";
include __DIR__.'/components/page/olz_header/olz_header.php';

echo "<div id='content_double'>
<div>";

$code = $_GET['code'];

$google_utils = getGoogleUtilsFromEnv();
$token_data = $google_utils->getTokenDataForCode($code);
$user_data = $google_utils->getUserData($token_data);
echo "<pre>";
print_r($user_data);
echo "</pre><br/>";

echo "</div>
</div>";

include __DIR__.'/components/page/olz_footer/olz_footer.php';
