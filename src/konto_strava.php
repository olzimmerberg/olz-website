<?php

session_start();

require_once __DIR__.'/admin/olz_init.php';
require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/utils/auth/StravaUtils.php';
$html_titel = " - Strava Konto";
include __DIR__.'/components/page/olz_header/olz_header.php';

echo "<div id='content_double'>
<div>";

$code = $_GET['code'];
$granted_scope = $_GET['scope'];

$strava_utils = getStravaUtilsFromEnv();
print_r($strava_utils->getTokenDataForCode($code));
echo "<br/>";
print_r($granted_scope);

echo "</div>
</div>";

include __DIR__.'/components/page/olz_footer/olz_footer.php';
