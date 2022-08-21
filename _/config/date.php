<?php

// =============================================================================
// Konfiguration der Datums- und Zeitfunktionen
// =============================================================================

use Olz\Utils\AbstractDateUtils;

global $_DATE, $heute, $start_jahr, $end_jahr, $jahre, $wochentage, $wochentage_lang, $monate;

if (!isset($_DATE)) {
    $_DATE = AbstractDateUtils::fromEnv();
}

date_default_timezone_set('Europe/Zurich');

function olz_current_date($format) {
    global $_DATE;
    return $_DATE->getCurrentDateInFormat($format);
}

$heute = $_DATE->getIsoToday();
if ($heute >= ($_DATE->getCurrentDateInFormat('Y')."-01-01") and isset($_SESSION["auth"])) {
    $start_jahr = $_DATE->getCurrentDateInFormat('Y') + 1;
} else {
    $start_jahr = $_DATE->getCurrentDateInFormat('Y');
}
$end_jahr = (isset($_GET["archiv"]) ? 2005 : $_DATE->getCurrentDateInFormat('Y') - 5);
$jahre = [];
for ($jahr = $start_jahr; $end_jahr <= $jahr; $jahr--) {
    array_push($jahre, $jahr);
}
$wochentage = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"];
$wochentage_lang = ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"];
$monate = ["Jan.", "Feb.", "März", "April", "Mai", "Juni", "Juli", "Aug.", "Sept.", "Okt.", "Nov.", "Dez.", "alle"];
