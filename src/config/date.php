<?php

// =============================================================================
// Konfiguration der Datums- und Zeitfunktionen
// =============================================================================

global $_DATE;

if (!isset($_DATE)) {
    require_once __DIR__.'/server.php';

    $class_name = $_CONFIG->getDateUtilsClassName();
    $class_args = $_CONFIG->getDateUtilsClassArgs();

    if ($class_name == 'FixedDateUtils') {
        require_once __DIR__.'/../utils/date/FixedDateUtils.php';
        $_DATE = new FixedDateUtils($class_args[0]);
    } elseif ($class_name == 'LiveDateUtils') {
        require_once __DIR__.'/../utils/date/LiveDateUtils.php';
        $_DATE = new LiveDateUtils();
    } else {
        exit("Invalid date utils class name: {$class_name}");
    }
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
