<?php

if (isset($_GET['unset'])) {
    unset($_SESSION['edit']);
}

//-----------------------------------------
// KONSTANTEN - DEFAULTS
//-----------------------------------------
$ftp_user = "web276";
$ftp_pw = "123456";
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
/*
do
    {array_push($jahre,end($jahre)-1);
    }
while (end($jahre)>"2006");
*/
// Spezialkategorien Aktuell
$aktuell_special = [
    "OL-Lager Tesserete 2011" => "lager11",
    "OL-Lager Vinelz 2010" => "lager10",
    "OL-Lager Mannenbach 2009" => "lager09",
    "JWOC 2008" => "jwoc2008",
    "OL-Lager Schwarzenegg 2008" => "lager08",
    "JWOC 2006" => "jwoc",
    "1. Zimmerberg OL 2006" => "zimmerbergol2006", ];

require_once __DIR__.'/../config/paths.php';

//-------------------------------------------
// POST/GET-Variable
//-------------------------------------------
//echo $_SESSION["version"]."*";
require_once dirname(__DIR__)."/library/webtool/class_security.php";
$s = new security();
$s->set_std_sonderbehandlung(["terminelink" => "sql_safe"]);
$s->check_REQUEST();

if (isset($_GET)) {
    reset($_GET);
    foreach ($_GET as $key => $element) {
        ${$key} = $element;
    }
}
if (isset($_POST)) {
    reset($_POST);
    foreach ($_POST as $key => $element) {
        ${$key} = $element;
    }
}

$tmp = ["5" => "forum", "8" => "newsletter", "13" => "anmeldung"];
$var = "button".$tmp[$page];
if (isset($status)) {
    ${$var} = $status;
} // für alte Links bei Forumseinträgen und Newsletter-Anmeldung
if (isset($button)) {
    ${$var} = $button;
} // für alte Links bei Forumseinträgen und Newsletter-Anmeldung
//echo $var."=".$$var;

require_once __DIR__.'/../config/database.php';

//-------------------------------------------
// Sendmail
//-------------------------------------------
$mail_from = "noreply@olzimmerberg.ch"; // Absenderadresse wird als additional header in mail() benötigt

//-------------------------------------------
// Sprache für Datum-/Zeitangaben setzen
//-------------------------------------------
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de_DE.UTF8');

//-----------------------------------------
//Ampersand Output
//-----------------------------------------
ini_set('arg_separator.output', '&amp;');

//-----------------------------------------
//Speicher für Bildbearbeitung (gdlib)
//-----------------------------------------
//ini_set('memory_limit', '32M');
//ini_set('post_max_size', '100M'); // nur lokal möglich
//ini_set('upload_max_filesize', '100M'); // nur lokal möglich
//ini_set('max_input_time', '420'); // Upload: ADSL 500kb/s > 7s/MB
//ini_set('max_execution_time', '420');
$mem_limit = ini_get('memory_limit');
$img_limit = $mem_limit * 1024 * 1024 / 4 / 1.4; // max. Bildgrösse in Megapixel (Sicherheitsfaktor 1.4)
$ul_limit = ini_get('upload_max_filesize');

//-----------------------------------------
//Encoding für HTML-Behandlung (substr)
//-----------------------------------------
mb_internal_encoding("UTF-8");
