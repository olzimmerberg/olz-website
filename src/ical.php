<?php

// =============================================================================
// iCal-Datei generieren mit Terminen des aktuellen Jahres.
// Dieses Script wird immer beim Sichern und beim Löschen eines Termins
// aufgerufen.
// =============================================================================

include_once "admin/olz_init.php";
include_once "admin/olz_functions.php";

$file_path = "{$data_path}olz_ical.ics";
$jahr = date('Y');

// Termine abfragen
$sql = "SELECT * FROM termine WHERE (datum >= '{$jahr}-01-01') AND on_off=1";
$result = $db->query($sql);

// ical-Kalender
$ical = "BEGIN:VCALENDAR".
"\r\nPRODID:OL Zimmerberg Termine".
"\r\nVERSION:2.0".
"\r\nMETHOD:PUBLISH".
"\r\nCALSCALE:GREGORIAN".
"\r\nX-WR-CALNAME:OL Zimmerberg Termine".
"\r\nX-WR-TIMEZONE:Europe/Zurich";

// Termine
while ($row = mysqli_fetch_array($result)) {// Links extrahieren
    $links = $row['link'];
    $dom = new domdocument();
    $dom->loadHTML($links || ' ');
    $_links = "OLZ-Termin: https://olzimmerberg.ch/termine.php?uid=".$row['id']."#id".$row['id'];
    $_attach = "\r\nATTACH;VALUE=URI:https://olzimmerberg.ch/termine.php?uid=".$row['id']."#id".$row['id'];
    foreach ($dom->getElementsByTagName("a") as $a) {
        $text = $a->textContent;
        $url = $a->getAttribute("href");
        $_links .= "\\n".$text.": ".$url;
        $_attach .= "\r\nATTACH;VALUE=URI:".$url;
    }
    $_links .= ($row['solv_uid'] > 0) ? "\\nSOLV-Termin: https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row['solv_uid'] : "";
    $_attach .= ($row['solv_uid'] > 0) ? "\r\nATTACH;VALUE=URI:https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row['solv_uid'] : "";

    $datum = $row['datum'];
    $datum_end = ($row['datum_end'] > "0000-00-00") ? $row['datum_end'] : $datum;
    $ical .=
"\r\nBEGIN:VEVENT\nDTSTART;VALUE=DATE:".olz_date('jjjjmmtt', $datum).
"\r\nDTEND;VALUE=DATE:".olz_date('jjjjmmtt', $datum_end).
"\r\nDTSTAMP:".date('Ymd\THis\Z').
"\r\nLAST-MODIFIED:".date('Ymd\THis\Z', strtotime($row['modified'])).
"\r\nCREATED:".date('Ymd\THis\Z', strtotime($row['created'])).
"\r\nSUMMARY:".$row['titel'].
"\r\nDESCRIPTION:".str_replace("\r\n", "\\n", $row['text']).
"\\n".$_links;
    $ical .=
"\r\nCATEGORIES:".$row['typ'].
$_attach.//"\r\nATTACH;VALUE=URI:https://olzimmerberg.ch/termine.php?uid=".$row['id']."#id".$row['id'].
"\r\nCLASS:PUBLIC".
"\r\nUID:olz_termin_".$row['id']."@olzimmerberg.ch".
"\r\nEND:VEVENT";
}

$ical .= "\r\nEND:VCALENDAR";
//echo "<pre>".$ical."</pre>";

// Datei schreiben
$f = fopen($file_path, "w+");
fwrite($f, $ical);
fclose($f);

//echo "<pre>".$ical."</pre>";
