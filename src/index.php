<?php

// =============================================================================
// Der Eintrittspunkt (fast) jeder Benutzeranfrage.
// Von hier aus wird der für die angefragte Seite benötigte Code importiert.
// =============================================================================

session_start();

require_once 'admin/olz_init.php';
require_once 'admin/olz_functions.php';
require_once 'tickers.php';

define('CALLED_THROUGH_INDEX', 1);

$pages = [
    "0" => "error.php", // TO DO
    "1" => "startseite.php",
    "2" => "aktuell.php",
    "3" => "termine.php",
    "4" => "galerie.php",
    "5" => "forum.php",
    "6" => "kontakt.php",
    "7" => "blog.php",
    "8" => "service.php",
    "9" => "search.php",
    "10" => "login.php",
    "11" => "zimmerbergol.php",
    "12" => "karten.php",
    "15" => "termine_tools_DEV.php",
    "16" => "zol/index.php",
    "18" => "fuer_einsteiger.php",
    "19" => "zol/karten.php",
    "20" => "trophy.php",
    "21" => "material.php",
    "99" => "results.php",
    "100" => "profil.php",
    "mail" => "divmail.php",
    "ftp" => "webftp.php",
    "tools" => "termine_helper.php",
];

//http://YOUR-SITE.COM/FILERUN/?page=login&action=login&nonajax=1&username=test&password=1234
// Seiten-Titel
$html_titel = "";
if (isset($id) and in_array($page, ["2", "3", "4", "7"])) {
    $table_tmp = ["", "", "aktuell", "termine", "galerie", "", "", "blog"];
    $sql = "SELECT titel FROM ".$table_tmp[$page]." WHERE id='{$id}'";
    $res = $db->query($sql);
    while ($row = $res->fetch_assoc()) {
        $html_titel = " - ".$row['titel'];
    }
}

//-----------------------------------------
// UPLOAD-GRÖSSE PRÜFEN
//-----------------------------------------
$POST_MAX_SIZE = ini_get("post_max_size");
$mul = substr($POST_MAX_SIZE, -1);
$mul = ($mul == "M" ? 1048576 : ($mul == "K" ? 1024 : ($mul == "G" ? 1073741824 : 1)));
if ($_SERVER["CONTENT_LENGTH"] > $mul * (int) $POST_MAX_SIZE && $POST_MAX_SIZE) {
    $button_name = 'button'.$_SESSION["edit"]["db_table"];
    $_POST[$button_name] = $_SESSION["edit"]["button"];
    $alert = "Fehler: Upload-Datei ist zu gross (".round($_SERVER["CONTENT_LENGTH"] / pow(2, 20), 1)."MB). Maximale Dateigrösse ist ".$POST_MAX_SIZE."B.";
}

//-----------------------------------------
// BEARBEITUNGS-STATUS PRÜFEN
//-----------------------------------------
if (isset($_SESSION["edit"])) {
    $button_name = 'button'.$_SESSION["edit"]["db_table"];
    if (!isset($_POST[$button_name]) or isset($id)) {
        $alert = "Bearbeitung muss zuerst abgeschlossen werden.";
        $_POST[$button_name] = $_SESSION["edit"]["button"];
        $db_table = $_SESSION["edit"]["db_table"];
        $id = $_SESSION[$db_table."id"];
        //$id = $_SESSION["id"];
        $id_edit = $_SESSION["id_text"];
    }
    $page = $_SESSION["page"];
}
if ($unset == 'unset') {
    unset($_SESSION["edit"]);
}

if (($page == "14") and (in_array(["all", "anm_felder"], explode(" ", $_SESSION["auth"])))) {
    $page = "1";
}
if ($page == "") {
    $page = $_SESSION["page"];
}
if ($page == "") {
    $page = "1";
}
if ($page == "zol" or $page == 'n2' or $page == 'wre') {
    $page = "11";
}
if (($page == "10") and ($_SESSION["versuch"] > $maxversuche)) {
    $page = $_SESSION["page"];
} // Login zu viele Versuche
if (($page == "10") and isset($_SESSION['auth'])) {
    $page = $_SESSION["page"];
} // bereits eingeloggt
if (!is_numeric($page) and !in_array($page, explode(" ", $_SESSION["auth"])) and ($_SESSION["auth"] != "all")) { // Adminseiten
    if (is_numeric($_SESSION["page"])) {
        $page = $_SESSION["page"];
    } // zurück zur letzten Seite
    else {
        $page = 1;
    } // zurück zu Seite 1
}
if ($page == 16 and $_SESSION['auth'] != "all") {
    $page = $_SESSION["page"];
}
if ($page != "10") {
    $_SESSION["page"] = $page;
}
if ($pages[$page][0] == '') {
    $page = 0;
}
// Win-IE Weiche
if (preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"]) and preg_match("/Win/", $_SERVER["HTTP_USER_AGENT"])) {
    $bildart = "gif";
} else {
    $bildart = "png";
}
if ($page == 19 && false) {  // TODO: reenable after corona
    $refresh = "<meta http-equiv='refresh' content='60'>";
} // Stand Karten/Anmeldungen
else {
    $refresh = "";
}

header('Cache-Control: max-age=600');

include "components/page/olz_header/olz_header.php";

// Dynamisches Layout
include $pages[$page];

include "components/page/olz_footer/olz_footer.php";
