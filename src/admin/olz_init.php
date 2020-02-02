<?php
if (strpos($_SERVER["SCRIPT_NAME"],"test")) {$root_path="http://olzimmerberg.ch/";} else {$root_path="";}

if (isset($_GET['unset'])) unset($_SESSION['edit']);

//-----------------------------------------
// KONSTANTEN - DEFAULTS
//-----------------------------------------
$ftp_user = "web276";
$ftp_pw = "123456";
date_default_timezone_set('Europe/Zurich');
$heute = date("Y-m-d");
if($heute>=(date("Y")."-01-01") AND isset($_SESSION["auth"])) $start_jahr = date("Y")+1;
else $start_jahr = date("Y");
$end_jahr = (isset($_GET["archiv"])?2005:date("Y")-5);
$jahre=array();
for ($jahr=$start_jahr; $end_jahr<=$jahr; $jahr--) {
    array_push($jahre, $jahr);
}
$wochentage = array("So","Mo","Di","Mi","Do","Fr","Sa");
$wochentage_lang = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
$monate = array ("Jan.","Feb.","März","April","Mai","Juni","Juli","Aug.","Sept.","Okt.","Nov.","Dez.","alle");
/*
do
	{array_push($jahre,end($jahre)-1);
	}
while (end($jahre)>"2006");
*/
// Spezialkategorien Aktuell
$aktuell_special = array(
				"OL-Lager Tesserete 2011" => "lager11",
				"OL-Lager Vinelz 2010" => "lager10",
				"OL-Lager Mannenbach 2009" => "lager09",
				"JWOC 2008" => "jwoc2008",
				"OL-Lager Schwarzenegg 2008" => "lager08",
				"JWOC 2006" => "jwoc",
				"1. Zimmerberg OL 2006" => "zimmerbergol2006");

//-------------------------------------------
// UMGEBUNG
//-------------------------------------------
/*$local = 0;
if (preg_match("/127\.0\.0/",$_SERVER['REMOTE_ADDR'])) $local = 1;
if ($_SERVER['REMOTE_ADDR']=="::1") $local = 1;*/
$local = 0; //uu, 11.8.2016 Umgebungsabfrage funktioniert so nicht mehr

// if($local) $root = $_SERVER["DOCUMENT_ROOT"].'/olzimmerberg.ch';
// else $root = $_SERVER["DOCUMENT_ROOT"];

if($local) $data_path = 'TODO: not implemented';
else $data_path = $_SERVER['DOCUMENT_ROOT'].'/';

if($local) $data_href = 'TODO: not implemented';
else $data_href = '/';
//-------------------------------------------
// POST/GET-Variable
//-------------------------------------------
//echo $_SESSION["version"]."*";
require_once(dirname(__DIR__)."/library/webtool/class_security.php");
$s = new security;
$s->set_std_sonderbehandlung(array("terminelink" => "sql_safe"));
$s->check_REQUEST();

if (isset($_GET))
	{reset($_GET);
	foreach($_GET as $key => $element)
		{$$key = $element;
		}
	}
if (isset($_POST))
	{reset($_POST);
	foreach($_POST as $key => $element)
		{$$key = $element;
		}
	}

$tmp = array ("5"=>"forum","8"=>"newsletter","13"=>"anmeldung");
$var = "button".$tmp[$page];
if (isset($status)) $$var = $status; // für alte Links bei Forumseinträgen und Newsletter-Anmeldung
if (isset($button)) $$var = $button; // für alte Links bei Forumseinträgen und Newsletter-Anmeldung
//echo $var."=".$$var;

//-------------------------------------------
// Datenbankverbindung
//-------------------------------------------
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if ($db->connect_error) die("Connect Error (".$db->connect_errno.") ".$db->connect_error);
//mysql_query('SET NAMES utf8');
$db->query("SET NAMES utf8");
$db_name = "db12229638-1";
//mysql_select_db($db_name);
function DBEsc($str) {
    global $db;
    return $db->escape_string($str);
}

//-------------------------------------------
// Sendmail
//-------------------------------------------
$mail_from = "noreply@olzimmerberg.ch"; // Absenderadresse wird als additional header in mail() benötigt

//-------------------------------------------
// Sprache für Datum-/Zeitangaben setzen
//-------------------------------------------
setlocale(LC_ALL, 'de_DE@euro','de_DE');

//-----------------------------------------
//Ampersand Output
//-----------------------------------------
ini_set('arg_separator.output','&amp;');

//-----------------------------------------
//Speicher für Bildbearbeitung (gdlib)
//-----------------------------------------
//ini_set('memory_limit', '32M');
//ini_set('post_max_size', '100M'); // nur lokal möglich
//ini_set('upload_max_filesize', '100M'); // nur lokal möglich
//ini_set('max_input_time', '420'); // Upload: ADSL 500kb/s > 7s/MB
//ini_set('max_execution_time', '420');
$mem_limit = ini_get('memory_limit');
$img_limit = $mem_limit*1024*1024/4/1.4; // max. Bildgrösse in Megapixel (Sicherheitsfaktor 1.4)
$ul_limit = ini_get('upload_max_filesize');

//-----------------------------------------
//Encoding für HTML-Behandlung (substr)
//-----------------------------------------
mb_internal_encoding("UTF-8");

?>
