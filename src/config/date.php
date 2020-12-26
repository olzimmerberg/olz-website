<?php

date_default_timezone_set('Europe/Zurich');

$heute = date("Y-m-d");
if ($heute >= (date("Y")."-01-01") and isset($_SESSION["auth"])) {
    $start_jahr = date("Y") + 1;
} else {
    $start_jahr = date("Y");
}
$end_jahr = (isset($_GET["archiv"]) ? 2005 : date("Y") - 5);
$jahre = [];
for ($jahr = $start_jahr; $end_jahr <= $jahr; $jahr--) {
    array_push($jahre, $jahr);
}
$wochentage = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"];
$wochentage_lang = ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"];
$monate = ["Jan.", "Feb.", "März", "April", "Mai", "Juni", "Juli", "Aug.", "Sept.", "Okt.", "Nov.", "Dez.", "alle"];
