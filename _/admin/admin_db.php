<?php

// =============================================================================
// Urs' Content Management System. Jegliches Bearbeiten von Daten geschieht über
// diese Definitionen.
// Es ist noch nicht klar, wie sich die Doctrine-Einführung auf diesen Code
// auswirken wird. Möglicherweise können hier Dinge vereinfacht oder einfacher
// getestet werden.
// =============================================================================

require_once __DIR__.'/../config/database.php';

$mail_from = 'noreply@olzimmerberg.ch'; // Absenderadresse wird als additional header in mail() benötigt

// ***************************************************
// Formular zur Verwaltung der Mysql-Datenbanken
// ***************************************************
// Datenbankfelder mit Zusatzinformationen defnieren
// Inhalt: [0]Feldname, [1]Bezeichnung, [2]Formularfeldtyp(falls 'checkbox:[2][1]=option,Option/falls 'text/textarea': [2][1]=readonly/falls 'select:[2][1]=option,Option,[2][2]=multiple), [3]Startwert, [4]Kommentar, [5]HTML_Zusatz, [6]Stil, [7]Formatierung,[8]Test,[9]Warnung
// -------------------------------------------------------------
/*
$_SESSION['edit']['db_table']: Tabelle in Bearbeitung
$_SESSION['edit']['modus']: 'neuedit'=neu angelegter Datensatz
$_SESSION['edit']['confirm']: löschen bestätigen
$_SESSION['edit']['replace']: ersetzen bestätigen
$_SESSION['edit']['vorschau']: '1'= speichern aus Vorschau
$_SESSION['edit']['button']: letzter Klick
*/
require_once __DIR__.'/../image_tools.php';
require_once __DIR__.'/../file_tools.php';

$layout = "2";
$_SESSION['edit']['table'] = $db_table;
$tmp_folder = "temp";

$markup_notice = (
    "Hinweise:<br>"
    ."<div style='font-weight:normal;'>1. Internet-Link in Text einbauen: Internet-Adresse mit 'http://' beginnen, "
    ."Bsp.: 'http://www.olzimmerberg.ch' wird zu  <a href='http://www.olzimmerberg.ch' class='linkext' target='blank'><b>www.olzimmerberg.ch</b></a><br>"
    ."2. Text mit Fettschrift hervorheben: Fetten Text mit '&lt;b>' beginnen und mit '&lt;/b>' beenden, "
    ."Bsp: '&lt;b>dies ist fetter Text&lt;/b>' wird zu '<b>dies ist fetter Text</b>'<br>"
    ."3. Bilder:<br><table><tr class='tablebar'><td><b>Bildnummer</b></td><td><b>Wie einbinden?</b></td></tr>"
    ."<tr><td>1. Bild</td><td>&lt;BILD1></td></tr><tr><td>2. Bild</td><td>&lt;BILD2></td></tr></table><br>"
    ."4. Dateien:<br><table><tr class='tablebar'><td><b>Dateinummer</b></td><td><b>Wie einbinden?</b></td><td><b>Wie wird's aussehen?</b></td></tr>"
    ."<tr><td>1. Datei</td><td>&lt;DATEI1 text=&quot;OL Karte&quot;></td><td><a style='padding-left:17px; background-image:url(img/fileicons/image-16.png); background-repeat:no-repeat;'>OL Karte</a></td></tr>"
    ."<tr><td>2. Datei</td><td>&lt;DATEI2 text=&quot;Ausschreibung als PDF&quot;></td><td><a style='padding-left:17px; background-image:url(img/fileicons/pdf-16.png); background-repeat:no-repeat;'>Ausschreibung als PDF</a></td></tr></table></div>"
);

if ($db_table == "aktuell") {// DB AKTUELL
    $img_folder = "img";
    $img_max_size = 240;
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum", "datum", "olz_current_date('Y-m-d');", "", "", "", ""],
        ["zeit", "text", "hidden", "olz_current_date('H:i:s');", "", "", "", ""],
        ["on_off", "Aktiv", "boolean", "1", "", "", "", ""],
        ["typ", "Typ", "text", "'aktuell'", "", "", "", ""],
        ["titel", "Titel", "text", "''", "", "", "", ""],
        ["text", "Kurztext", "textarea", "''", "", "", "", " rows='4'"],
        ["textlang", "Haupttext", "textarea", "''", $markup_notice, "", "", " rows='8'"],
        ["autor", "Autor", "text", "''", "", "", "", ""],
        ["link", "Link", "text", "''", "", "", "", ""],
        ["termin", "Termin", "hidden", "0", "", "", "", ""],
        ["tags", "Tags", "hidden", "''", "", "", "", ""],
    ];
} elseif ($db_table == "bild_der_woche") {// DB BILD DER WOCHE
    $layout = "1";
    $img_max_size = 240; // maximale Bildbreite,-höhe
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum", "datum", "olz_current_date('Y-m-d');", "", "", "", ""],
        ["on_off", "Aktiv", "hidden", "1", "", "", "", ""],
        ["titel", "Mouseover-Text", "text", "''", "", "", "", ""],
        ["text", "Bildlegende", "textarea", "''", "", "", "", " rows='4'"],
    ];
} elseif ($db_table == "blog") {// DB BLOG
    $nutzer = ($_SESSION['user'] ?? null);
    $img_folder = "img";
    $img_max_size = 240; // maximale Bildbreite,-höhe
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum", "datum", "olz_current_date('Y-m-d');", "", "", "", ""],
        ["zeit", "Zeit", "text", "olz_current_date('H:i:s');", "", "", "", ""],
        ["autor", "Autor", ["text", $nutzer == "gold" ? "" : " readonly"], "ucwords('{$nutzer}')", "", "", "", ""],
        ["titel", "Titel", "text", "''", "", "", "", "", "!empty", "Bitte Titel angeben."],
        ["on_off", "Aktiv", "boolean", "1", "", "", "", ""],
        ["text", "Text", "textarea", "''", $markup_notice, "", "", " rows='8'", "!empty", "Bitte Text angeben."],
    ];
} elseif ($db_table == "downloads") {// DB DOWNLOADS
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum", "hidden", "olz_current_date('Y-m-d');", "", "", "", ""],
        ["name", "Bezeichnung", "text", "''", "", "", "", ""],
        ["position", "Position", "hidden", "'0'", "", "", "", ""],
        ["on_off", "Aktiv", "boolean", "1", "", "", "", ""],
    ];
} elseif ($db_table == "forum") {// DB FORUM
    $send_mail = "on";
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum", "hidden", "olz_current_date('Y-m-d');", "", "", "", ""],
        ["name", "Titel", "text", "''", "", "", "", "", "!empty", "Bitte einen Titel angeben."],
        ["name2", "Name", "text", "''", "", "", "", "", "!empty", "Bitte einen Namen angeben."],
        ["email", "Email", "text", "''", "", "", "", "", "olz_is_email", "Bitte gültige Emailadresse angeben."],
        ["eintrag", "Text", "textarea", "''", "", "", "", " rows='8'", "!empty", "Hast du nichts mitzuteilen ?"],
        ["zeit", "text", "hidden", "olz_current_date('H:i:s');", "", "", "", ""],
        ["on_off", "Aktiv", "boolean", "'1'", "", "", "", ""],
        ["uid", "Code", ["text", " readonly"], "olz_create_uid(\"{$db_table}\")", "", "", "", " class='test-flaky'"],
    ];
} elseif ($db_table == "galerie") {// DB GALERIE
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum", "datum", "olz_current_date('Y-m-d');", "", "", "", ""],
        ["titel", "Titel", "text", "''", "", "", "", ""],
        ["autor", "Autor", "text", "''", "", "", "", "", "", ""],
        ["counter", "Counter", "hidden", "'0'", "", "", "", "", "", ""],
        ["typ", "Typ", (($_SESSION['auth'] ?? null) == "all" ? ["select", [["Fotos", "foto"], ["Film", "movie"]]] : "hidden"), "'foto'", "", "", "", "", "", ""],
        ["content", "Filmangaben", (($_SESSION['auth'] ?? null) == "all" ? "text" : "hidden"), "''", "", "", "", "", "", ""],
        ["termin", "Termin", "hidden", "0", "", "", "", ""],
    ];
    if (($_SESSION['auth'] ?? null) != null) {
        array_push($db_felder,
                    ["on_off", "Aktiv", "boolean", "1", "", "", "", ""]);
    }
} elseif ($db_table == "karten") {// DB KARTEN
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["position", "Position", "text", "'0'", "", "", "", ""],
        ["kartennr", "Kartennummer", "text", "'0'", "", "", "", ""],
        ["name", "Kartenname", "text", "''", "", "", "", ""],
        ["vorschau", "Dateiname Vorschau", "text", "''", "", "", "", ""],
        ["typ", "Kartentyp", ["select", [["Normalkarte", "ol"], ["Dorfkarte", "stadt"], ["Schulhauskarte", "scool"]]], "'ol'", "", "", "", ""],
        ["center_x", "X-Koordinate", "text", "'0'", "", "", "", ""],
        ["center_y", "Y-Koordinate", "text", "'0'", "", "", "", ""],
        ["jahr", "Kartenjahr", "text", "''", "", "", "", ""],
        ["ort", "Ort", "text", "''", "", "", "", ""],
        ["massstab", "Massstab", "text", "''", "", "", "", ""],
        ["zoom", "Zoomfaktor", ["select", [["1 Pixel = 2m", "2"], ["1 Pixel = 8m", "8"], ["1 Pixel = 32m", "32"]]], "'8'", "Zoomfaktor für Anzeige auf map.search.ch", "", "", ""],
    ];
} elseif ($db_table == "links") {// DB LINKS
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum", "hidden", "olz_current_date('Y-m-d');", "", "", "", ""],
        ["position", "Position", "hidden", "'0'", "", "", "", ""],
        ["name", "Bezeichnung", "text", "''", "", "", "", "", "!empty", "Bitte Download-Bezeichnung angeben."],
        ["url", "URL", "text", "'http://'", "", "", "", "", "!empty", "Bitte URL angeben."],
        ["on_off", "Aktiv", "boolean", "1", "", "", "", ""],
    ];
} elseif ($db_table == "termine") {// DB TERMINE
    // include 'parse_solv_ranglisten.php';
    require_once __DIR__.'/../termine/utils/TermineFilterUtils.php';
    $checkbox_options = array_map(function ($option) {
        return [$option['name'], $option['ident']];
    }, array_filter(
        TermineFilterUtils::ALL_TYPE_OPTIONS,
        function ($option) {
            return $option['ident'] != 'alle';
        }
    ));
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum (Beginn)", "datum", "olz_current_date('Y-m-d')", "Format: yyyy-mm-tt (z.B. '2006-01-31')", "", "", ""],
        ["zeit", "Zeit (Beginn)", "zeit", "'00:00:00'", "Format: hh:mm:ss (z.B. '18:30:00')", "", "", ""],
        ["datum_end", "Datum (Ende)", "datum", "", "Bei mehrtägigen Anlässen (sonst leer lassen).", "<input type='button' name='' onclick='End_angleichen()' value='1. Datum übernehmen' class='dropdown' style='width: 44%;margin-left:10px;'>", "", ""],
        ["zeit_end", "Zeit (Ende)", "zeit", "'00:00:00'", "Format: hh:mm:ss (z.B. '18:30:00')", "", "", ""],
        ["datum_off", "Datum (Ausschalten)", "datum", "", "Termin wird ab diesem Datum permanent ausgeblendet.", "<input type='button' name='' onclick='Off_angleichen()' value='2. Datum übernehmen' class='dropdown' style='width: 44%;margin-left:10px;'>", "width:50%", ""],
        ["titel", "Titel", "text", "''", "", "<select name='set_titel' style='width:33%;margin-left:10px;' size='1'
onchange='Titel_angleichen()' class='dropdown'>
<option value=''>&nbsp;</option>
<option value='. Nationaler OL'>Nationaler</option>
<option value='-OL-Meisterschaft'>Meisterschaft</option>
<option value=' OL-Weekend'>Weekend</option>
<option value='Training: '>Training</option>
<option value='Meldeschluss '>Meldeschluss</option>
</select>", "width:60%;font-weight:bold;", ""],
        ["text", "Text", "textarea", "''", "", "", "", " rows='4'"],

        ["typ", "Typ", ["checkbox", $checkbox_options], "''", "", "", "", ""],

        ["link", "Link", "textarea", "''", "",
            "<p>
<input value='+' style='width:18px;' type='button' onclick='Linkhilfe()' class='dropdown'>
<select name='set_link' style='width: 33%;' size='1' class='dropdown'>
<option value=''>&nbsp;</option>
<option value='1'>Ausschreibung</option>
<option value='8'>Anmeldung</option>
<option value='2'>GO2OL</option>
<option value='3'>Fahrplan</option>
<option value='4'>[Mail]</option>
<option value='5'>[Link intern]</option>
<option value='6'>[Link extern]</option>
<option value='7'>[Link PDF]</option>
</select>
<input name='help_set_link' value='' style='width: 56%;' type='text'>", "", " rows='4'", ],
        ["teilnehmer", "TeilnehmerInnen", "number", "", "", "", "", ""],
        ["xkoord", "X-Koordinate", "number", "", "", "<input type='button' name='' onclick='koordinaten()' value='Analysieren' title='Versucht automatisch X- und Y-Koordinate aus der Eingabe zu eruieren\nBsp: Eingabe: \"	263925 / 699025\" > Ausgabe: X=\"699025\", Y=\"699025\"' class='dropdown' style='width: 44%;margin-left:10px;'>", "width:150px;", ""],
        ["ykoord", "Y-Koordinate", "number", "", "", "", "width:150px;", ""],
        ["on_off", "Aktiv", "boolean", "1", "", "", "", ""],
        ["newsletter", "Newsletter für Änderung", "boolean", "1", "", "", "", ""],
        ["go2ol", "GO2OL-Code", "text", "''", "", "", "", ""],
        ["solv_uid", "SOLV-ID", "number", "", "", "", "", ""],
    ];
} elseif ($db_table == "vorstand") {// DB VORSTAND
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["name", "Bezeichnung", "text", "''", "", "", "", ""],
        ["funktion", "Funktion", "text", "", "", "", "", ""],
        ["email", "E-Mail", "text", "", "", "", "", ""],
        ["on_off", "Aktiv", "boolean", "1", "", "", "", ""],
    ];
} elseif ($db_table == "event") {// EVENT - Online-Ranglisten
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["datum", "Datum", "datum", "olz_current_date('Y-m-d');", "", "", "", ""],
        ["name", "Bezeichnung", "text", "''", "", "", "", ""],
        ["name_kurz", "Dateiname", "text", "''", "", "<br>Name der Exportdatei aus der Auswertungssoftware", "", "", "", ""],
        ["kat_gruppen", "Kategorien gruppiert", "text", "''", "", "<br>z.B. 'H10 D10;H45 H50' (gruppiert nach gemeinsamen Bahnen)", "", "", "", ""],
        ["karten", "Karten", "text", "''", "", "<br>z.B. Anzahl vorgedruckte Karten '10;15;16;8' (gleiche Reihenfolge wie Kategoriegruppen)", "", "", "", ""],
    ];
} elseif ($db_table == "olz_text") {// TEXTE
    $db_felder = [
        ["id", "ID", "hidden", "''", "", "", "", ""],
        ["text", "Text", "textarea", "''", "", "", "", " rows='8'"],
    ];
}

// -------------------------------------------------------------
// Button-Rückgabe modulieren
// -------------------------------------------------------------
$_SESSION['edit']['button'] = $_POST[$button_name];

if ($function == "start") {
    $do = "getdata";
} elseif ($function == "duplicate") {
    $do = "duplicate";
} elseif ($function == "start_user") {
    $do = "getdata";
} elseif ($function == "neu") {
    $do = "neu";
} elseif ($function == "edit") {
    $do = "edit";
} elseif ($function == "replace") {
    $do = "vorschau";
} elseif ($function == "vorschau") {
    $do = "vorschau";
} elseif ($function == "code") {
    $do = "code";
} elseif (($function == "abbruch") and (($_SESSION['edit']['replace'] ?? null) == "1")) {
    $do = "deletefile";
} elseif (($function == "abbruch") and (($_SESSION['edit']['modus'] ?? null) == "neuedit")) {
    $do = "delete";
} elseif ($function == "abbruch") {
    $do = "abbruch";
} elseif (($function == "save") and (($_SESSION['edit']['vorschau'] ?? null) == "0")) {
    $do = "save";
} elseif ($function == "save") {
    $do = "submit";
} elseif (($function == "delete") and (($_SESSION['edit']['confirm'] ?? null) == "1")) {
    $do = "delete";
} elseif ($function == "delete") {
    $do = "confirm";
} elseif ($function == "undo") {
    $do = "delete";
} elseif ($function == "Einblenden") {
    $do = "ein";
} elseif ($function == "Ausblenden") {
    $do = "aus";
} elseif ($function == "up") {
    $do = "up";
} elseif ($function == "down") {
    $do = "down";
} elseif ($function == "activate") {
    $do = "activate";
}

if (($do ?? null) == "confirm") {
    $alert = "Möchtest du diesen Eintrag wirklich löschen ?";
    $do = "edit";
    $_SESSION['edit']['confirm'] = "1";
} else {
    $_SESSION['edit']['confirm'] = "0";
}

// -------------------------------------------------------------
// USER Code eingeben
// -------------------------------------------------------------
if (($do ?? null) == "code") {
    echo "<table class='liste'><tr><td style='width:20%;'><span style='font-weight:bold;'>Code:</span></td><td style='width:80%'>
        <input type='text' name='uid'  style='width:100%;'></td></tr></table>";
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Weiter", "1"], ["Abbrechen", "2"]], "")."</div>";
    unset($_SESSION['edit']['button']);
}
// -------------------------------------------------------------
// DS duplizieren
// -------------------------------------------------------------
if (($do ?? null) == "duplicate") {
    $sql = "SELECT * from {$db_table} WHERE (id = '".$id."') ORDER BY id ASC";
    $result = $db->query($sql);
    if ($result->num_rows == 0) {
        $do = "abbruch";
        $alert = "Kein Datensatz gewählt.";
    } else {
        $row = $result->fetch_assoc();
        unset($row["id"]); // Remove ID from array
        $row = array_filter($row, 'strlen'); // Null-/Leerwerte herausfiltern
        $sql = "INSERT INTO {$db_table}";
        $sql .= " ( ".implode(", ", array_keys($row)).") ";
        $sql .= " VALUES ('".implode("', '", array_values($row))."')";
        $result = $db->query($sql);
        $id = $db->insert_id;
        $_SESSION[$db_table."id_"] = $id;
        $_SESSION['edit']['modus'] = "neuedit";
        $do = "getdata";
    }
    if (($_SESSION['edit']['modus'] ?? null) != "neuedit") {
        $_SESSION['edit']['modus'] = "";
    }
}

// -------------------------------------------------------------
// Neuer Datensatz
// -------------------------------------------------------------
if (($do ?? null) == "neu") {
    $sql_tmp = [];
    foreach ($db_felder as $tmp_feld) {
        if ($tmp_feld[3] > '') {
            $start_value = "\$start_value = ".$tmp_feld[3];
            eval("{$start_value};");
            // echo $start_value;
            if ($tmp_feld[0] != 'id') {
                array_push($sql_tmp, $tmp_feld[0]." = '".$start_value."'");
            }
        }
    }
    if (!isset($_SESSION['edit']['modus'])) {
        $sql = "INSERT {$db_table} SET ".implode(",", $sql_tmp);
        if (($_SESSION['auth'] ?? null) == 'all') {
            echo "### <a href='javascript:alert(".htmlentities(json_encode($sql), ENT_QUOTES).")'>HOSTSTAR DEBUG</a> ###<br>";
        }
        $result = $db->query($sql);
        $id = $db->insert_id;
        if (($_SESSION['auth'] ?? null) == 'all') {
            echo "NEUE ID: ".$id."<br>";
        }
        $_SESSION[$db_table."id_"] = $id;

        $do = "getdata";
        $_SESSION['edit']['modus'] = "neuedit";
    } else {
        $do = "getdata";
    }
}

// -------------------------------------------------------------
// Daten aus DB holen
// -------------------------------------------------------------
if (($do ?? null) == "getdata") {
    if ($function == "start_user") {
        $sql = "SELECT * from {$db_table} WHERE (uid = '".$uid."') ORDER BY id ASC";
    } else {
        $sql = "SELECT * from {$db_table} WHERE (id = '".$id."') ORDER BY id ASC";
    }

    $result = $db->query($sql);
    if ($result->num_rows == 0) {
        $do = "abbruch";
        if ($function == "start_user") {
            $alert = "Ungültiger Code!";
        } else {
            $alert = "Kein Datensatz gewählt.";
        }
    } else {
        $row = $result->fetch_assoc();
        foreach ($db_felder as $tmp_feld) {
            $var = $tmp_feld[0];
            if ($var == 'id') {
                $id = $row['id'];
            }
            $_SESSION[$db_table.$var] = stripslashes($row[str_replace(["[", "]"], ["", ""], $tmp_feld[0])]);
        }
        $do = "edit";
    }
    if (($_SESSION['edit']['modus'] ?? null) != "neuedit") {
        $_SESSION['edit']['modus'] = "";
    }
}

// -------------------------------------------------------------
// Eintrag löschen
// -------------------------------------------------------------
if (($do ?? null) == "delete") {
    // Bilder löschen
    if (isset($tables_img_dirs[$db_table])) {
        $db_imgpath = $tables_img_dirs[$db_table];
        if (is_dir($db_imgpath."/".$id."/img")) {
            $imgs = scandir($db_imgpath."/".$id."/img");
            for ($i = 0; $i < count($imgs); $i++) {
                if ($imgs[$i] != ".." && $imgs[$i] != ".") {
                    @unlink($db_imgpath."/".$id."/img/".$imgs[$i]);
                }
            }
            @rmdir($db_imgpath."/".$id."/img");
        }
        if (is_dir($db_imgpath."/".$id."/thumb")) {
            $imgs = scandir($db_imgpath."/".$id."/thumb");
            for ($i = 0; $i < count($imgs); $i++) {
                if ($imgs[$i] != ".." && $imgs[$i] != ".") {
                    @unlink($db_imgpath."/".$id."/thumb/".$imgs[$i]);
                }
            }
            @rmdir($db_imgpath."/".$id."/thumb");
        }
        @rmdir($db_imgpath."/".$id);
    }

    $sql = "DELETE FROM {$db_table} WHERE (id = '".$_SESSION[$db_table."id"]."')";
    $result = $db->query($sql);
    $ds_count = -1;
    $do = "abbruch";
    // ical-DATEI AKTUALISIEREN
    if (in_array($db_table, ["termine"])) {
        include __DIR__.'/../ical.php';
    }
}

// -------------------------------------------------------------
// Position verschieben
// -------------------------------------------------------------
if ((($do ?? null) == "up") or (($do ?? null) == "down")) {
    if (($do ?? null) == "up") {
        $offset = "-1.5";
    } else {
        $offset = "1.5";
    }
    $sql = "UPDATE {$db_table} SET position=(position+{$offset}) WHERE (id= '{$id}')";
    $db->query($sql);
    $sql = "SELECT * FROM {$db_table} ORDER BY position ASC";
    $result = $db->query($sql);
    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        $sql = "UPDATE {$db_table} SET position='{$counter}' WHERE (id='".$row['id']."')";
        $db->query($sql);
        $counter = $counter + 1;
    }
    $do = "abbruch";
}
// -------------------------------------------------------------
// Eingabe abbrechen
// -------------------------------------------------------------
if (($do ?? null) == "abbruch") {
    foreach ($db_felder as $tmp_feld) {
        if (($tmp_feld[2] == "file") and (isset($_SESSION[$db_table]['name'])) and file_exists($tmp_folder."/".$_SESSION[$db_table]['name'])) {
            unlink($tmp_folder."/".$_SESSION[$db_table]['name']);
        } // Temp-Datei löschen
    }
    unset($_SESSION['edit']);
}

// -------------------------------------------------------------
// Galerie aktivieren
// -------------------------------------------------------------
if (($do ?? null) == "activate") {
    $sql = "UPDATE {$db_table} SET on_off='1' WHERE id='".$id."'";
    $result = $db->query($sql);
    unset($_SESSION['edit']);
}

// -------------------------------------------------------------
// Werte in Session-Variablen schreiben
// -------------------------------------------------------------
if (($do ?? null) == "save") {
    foreach ($db_felder as $tmp_feld) {
        $var = $tmp_feld[0];
        $_SESSION[$db_table.$var] = $_POST[$db_table.$var];
    }
    $do = "submit";
}

// -------------------------------------------------------------
// DS Speichern
// -------------------------------------------------------------
if (($do ?? null) == 'submit') {
    $sql_tmp = [];
    function user2db($feld_typ, $wert) {
        global $db;
        require_once __DIR__.'/../config/database.php';
        $default = "'".$db->escape_string(trim($wert))."'";
        if ($feld_typ == 'boolean') {
            return $wert != '' ? '1' : '0';
        }
        if ($feld_typ == 'number') {
            return $db->escape_string(''.intval($wert));
        }
        if ($feld_typ == 'datum') {
            if ($wert == '') {
                return 'NULL';
            }
            if ($wert == '0000-00-00') {
                return 'NULL';
            }
            return $default;
        }
        if ($feld_typ == 'datumzeit') {
            if ($wert == '') {
                return 'NULL';
            }
            if ($wert == '0000-00-00') {
                return 'NULL';
            }
            if ($wert == '0000-00-00 00:00:00') {
                return 'NULL';
            }
            return $default;
        }
        if ($feld_typ == 'zeit') {
            if ($wert == '') {
                return 'NULL';
            }
            if ($wert == '00:00:00') {
                return 'NULL';
            }
            return $default;
        }
        return $default;
    }
    foreach ($db_felder as $tmp_feld) {
        $var = $tmp_feld[0];
        // uu, 29.12.19 > Checkbox-Felder vom Typ 'boolean' werden als Array behandelt > 1. Wert abfragen
        if (is_array($_SESSION[$db_table.$var]) and $tmp_feld[2] == 'boolean') {
            $_SESSION[$db_table.$var] = $_SESSION[$db_table.$var][0];
        } elseif (is_array($_SESSION[$db_table.$var])) {
            $_SESSION[$db_table.$var] = explode(" ", $_SESSION[$db_table.$var]);
        }
        array_push($sql_tmp, $var." = ".user2db($tmp_feld[2], $_SESSION[$db_table.$var]));
    }

    $sql = "UPDATE {$db_table} SET ".implode(",", $sql_tmp)." WHERE (id = '".$_SESSION[$db_table."id"]."')";
    if (($_SESSION['auth'] ?? null) == 'all') {
        echo "### <a href='javascript:alert(&quot;".htmlentities($sql)."&quot;)'>HOSTSTAR DEBUG</a> ###<br>";
    }
    $result = $db->query($sql);

    if ($db_table == "bild_der_woche") {
        $sql = "UPDATE {$db_table} SET on_off='0' WHERE NOT (id = '".$_SESSION[$db_table."id"]."') AND (on_off = '1')";
        $db->query($sql);
    }

    if (in_array($db_table, ["forum"])) { // Nach Abschicken aktivieren
        $sql = "UPDATE {$db_table} SET on_off='1' WHERE (id = '".$_SESSION[$db_table."id"]."')";
        $db->query($sql);
    }
    // ical-DATEI AKTUALISIEREN
    if (in_array($db_table, ["termine"])) {
        include __DIR__.'/../ical.php';
    }
    // BESTAETIGUNGSMAIL
    if (($send_mail ?? null) == "on") {
        $page_links = [
            'forum' => 'forum.php',
        ];
        $page_link = $page_links[$db_table];
        $mail_text = ucfirst($db_table)." OL Zimmerberg\n************************\n";
        $mail_header = "From: OL Zimmerberg <".$db_table."@olzimmerberg.ch>\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        $mail_subject = "OL Zimmerberg - ".ucfirst($db_table);
        $mail_adress = [ // Kontrollmail
            "u.utzinger@sunrise.ch",
            "website@olzimmerberg.ch",
        ];
        if (!($local ?? null)) {
            array_push($mail_adress, $_SESSION[$db_table."email"]);
        } // Usermail

        if ($db_table == "forum") {
            $felder_tmp = [["name", "Name"], ["email", "Email-Adresse"], ["eintrag", "Eintrag"]];
            $feedback = "Dein Eintrag wurde gespeichert. Du erhältst ein Bestätigungsmail mit einem Code. Damit kannst du deinen Eintrag jederzeit ändern oder löschen.";
        }

        // MAILTEXT
        foreach ($felder_tmp as $feld_tmp) {
            $var = $db_table.$feld_tmp[0];
            $label = $feld_tmp[1];
            $mail_text .= $label.": ".$_SESSION[$var]."\n";
        }
        $mail_text = $mail_text."\n\n************************\nDein Eintrag wurde bearbeitet/geändert am: ".olz_current_date("Y-m-d")."/".olz_current_date("H:i:s")."\nCode: ".$_SESSION[$db_table."uid"]." (direkter Link: https://www.olzimmerberg.ch/".$page_link."?button{$db_table}=Weiter&code=".$_SESSION[$db_table."uid"].")";

        // MAIL SENDEN
        foreach ($mail_adress as $mailadress_tmp) {
            mail($mailadress_tmp, $mail_subject, $mail_text, $mail_header, $mail_from);
            // echo $mail_from;
        }
    }
    unset($_SESSION['edit']);
}
// -------------------------------------------------------------
// Vorschau
// -------------------------------------------------------------
if (($do ?? null) == 'vorschau') {// include 'upload.php';
    $vorschau = [];
    foreach ($db_felder as $tmp_feld) {
        $test = "";
        $var = $tmp_feld[0];
        if (isset($_POST[$db_table."id"])) {
            if (is_array($tmp_feld[2][1])) {
                if (($_POST[$db_table.$var] ?? '') == '') {
                    $_SESSION[$db_table.$var] = "";
                } else {
                    $_SESSION[$db_table.$var] = implode(" ", $_POST[$db_table.$var]);
                }
            } else {
                $_SESSION[$db_table.$var] = $_POST[$db_table.$var];
            }
            if (isset($tmp_feld[8]) and ($tmp_feld[8] != '')) { // Feldwert überprüfen
                $tmp = "\$test=".$tmp_feld[8]."(\$_SESSION['".$db_table.$var."']);";
                eval("{$tmp}");
                $var_alert = $db_table.$var."_alert";
                if ($test) {
                    ${$var_alert} = "";
                } else {
                    ${$var_alert} = "<span class='error'>".$tmp_feld[9]."</span>";
                    $do = "edit";
                }
            }
        }

        // uu, 29.12.19 > Checkbox-Felder vom Typ 'boolean' werden als Array behandelt > 1. Wert abfragen
        $wert = ($tmp_feld[2] == 'boolean') ? $_SESSION[$db_table.$var][0] : $_SESSION[$db_table.$var];
        $vorschau[$var] = stripslashes($wert);
    }

    if (isset($_SESSION['edit']['replace'])) {
        $do = "edit";
    }
    if (($do ?? null) == 'vorschau') {
        echo "<h2>Vorschau</h2>";
        $html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Bearbeiten", "1"], ["Speichern", "4"]], "")."</div>";
        $_SESSION['edit']['vorschau'] = "1";
    }
}
// -------------------------------------------------------------
// DS Eingabe
// -------------------------------------------------------------
if (($do ?? null) == 'edit') {// Eingabe-Formular aufbauen
    $html_input = "";
    $html_hidden = "";
    if ($function == "duplicate") {
        $html_input = "<h2 style='margin-bottom:15px;'>Duplikat bearbeiten</h2>";
    } elseif ($function == "neu") {
        $html_input = "<h2 style='margin-bottom:15px;'>Neuer Datensatz bearbeiten</h2>";
    } else {
        $html_input = "<h2 style='margin-bottom:15px;'>Datensatz bearbeiten</h2>";
    }
    // Datenbankfelder

    foreach ($db_felder as $tmp_feld) {
        $var = $tmp_feld[0];
        $var2 = $db_table.$var;
        $feld_name = $db_table.$var;
        $feld_wert = $_SESSION[$db_table.$var];
        $feld_bezeichnung = $tmp_feld[1];
        if (is_array($tmp_feld[2])) {
            $feld_typ = $tmp_feld[2][0];
        } else {
            $feld_typ = $tmp_feld[2];
        }
        if (is_array($tmp_feld[2]) and ($tmp_feld[2][0] != 'checkbox')) {
            $feld_rw = $tmp_feld[2][1];
        } // readonly
        else {
            $feld_rw = "";
        }
        $feld_kommentar = $tmp_feld[4];
        $feld_kommentar = ($feld_kommentar > '') ? "<br><b>".$tmp_feld[4]."</b>" : "";
        $feld_stil = $tmp_feld[6];
        $feld_spezial = $tmp_feld[5];
        $feld_format = $tmp_feld[7];
        $var_alert = $db_table.$var."_alert";
        if ($layout == '2') {
            $bez_style = " style='width:20%;padding-top:4px;'";
        } else {
            $bez_style = "";
        }

        if ($layout == "2") {
            $tmp_code = "</td><td style='width:80%'>";
        } else {
            $tmp_code = "<p>";
        }

        if ($feld_typ == "text" || $feld_typ == "number" || $feld_typ == "datumzeit" || $feld_typ == "zeit") { // Input-Typ 'text'
            $feld_stil = ($feld_stil == "") ? "style='width:95%;'" : "style='".$feld_stil."'";
            $html_input .= "<tr><td{$bez_style}><b>".$feld_bezeichnung.":</b>".$tmp_code."<input type='text' id='".$feld_name."' name='".$feld_name."' value='".htmlspecialchars(stripslashes($feld_wert), ENT_QUOTES)."' ".$feld_stil.$feld_rw.$feld_format.">".$feld_spezial.(${$var_alert} ?? '').$feld_kommentar."</td></tr>\n";
        } elseif ($feld_typ == "datum") { // Input-Typ 'text' mit Einblendkalender
            $html_input .= "\n<tr><td{$bez_style}><b>".$feld_bezeichnung.":</b>".$tmp_code."<input type='text' id='".$feld_name."' name='".$feld_name."' value='".htmlspecialchars(stripslashes($feld_wert), ENT_QUOTES)."' ".$feld_stil.$feld_rw." class='datepicker' size='10'>".$feld_spezial.(${$var_alert} ?? '').$feld_kommentar."</td></tr>\n";
        } elseif ($feld_typ == "textarea") { // Input-Typ 'textarea'
            $html_input .= "<tr><td{$bez_style}><b>".$feld_bezeichnung.":</b>".$tmp_code."<textarea id='".$feld_name."' name='".$feld_name."'".$feld_format." style='width:95%;".$feld_stil."'".$feld_rw.">".stripslashes($feld_wert)."</textarea>".$feld_spezial.(${$var_alert} ?? '').$feld_kommentar."</td></tr>\n";
        } elseif ($feld_typ == "checkbox") { // Input-Typ 'checkbox'
            $html_input .= "<tr><td{$bez_style}><b>".$feld_bezeichnung.":</b>".$tmp_code;
            $feld_wert = explode(" ", $feld_wert);
            foreach ($tmp_feld[2][1] as $option) {
                $value = $option[1];
                $text = $option[0];
                if (in_array($value, $feld_wert)) {
                    $checked = " checked";
                } else {
                    $checked = "";
                }
                $html_input .= "<span style='padding-right:20px; white-space:nowrap;".$feld_stil."'><input type='checkbox' id='".$feld_name."' name='".$feld_name."[]'".$checked." style='margin-top:0.4em;margin-right:0.5em;' value='{$value}'><span style='vertical-align:bottom;'>{$text}".$feld_spezial.$feld_kommentar."</span></span> ";
            }
            $html_input .= "</td></tr>\n";
        } elseif ($feld_typ == "boolean") { // Input-Typ 'boolean'
            $html_input .= "<tr><td{$bez_style}><b>".$feld_bezeichnung.":</b>".$tmp_code;
            if (intval($feld_wert) != 0) {
                $checked = " checked";
            } else {
                $checked = "";
            }
            $html_input .= "<span style='padding-right:20px;".$feld_stil."'><input type='checkbox' id='".$feld_name."' name='".$feld_name."[]'".$checked." style='margin-top:0.4em;margin-right:0.5em;' value='1'><span style='vertical-align:bottom;'>".$feld_spezial.$feld_kommentar."</span></span>";
            $html_input .= "</td></tr>\n";
        } elseif ($feld_typ == "select") { // Input-Typ 'select'
            $html_input .= "<tr><td{$bez_style}><b>".$feld_bezeichnung.":</b>".$tmp_code."<select size='1' id='".$feld_name."' name='".$feld_name."[]'>";
            $feld_wert = explode(" ", $feld_wert);
            foreach ($tmp_feld[2][1] as $option) {
                $value = $option[1];
                $text = $option[0];
                if (in_array($value, $feld_wert)) {
                    $checked = " selected";
                } else {
                    $checked = "";
                }
                $html_input .= "<p><option".$checked." style='".$feld_stil."' value='{$value}'>{$text}</option >".$feld_spezial.$feld_kommentar."";
            }
            $html_input .= "</select></td></tr>\n";
        } elseif ($feld_typ == "hidden") { // Input-Typ 'hidden'
            $html_hidden .= "<input type='hidden' id='".$feld_name."' name='".$feld_name."' value='".stripslashes($feld_wert)."'>\n";
        }
        /*
        elseif ($feld_typ == "image") //Input-Typ 'image'
            {if ($feld_stil=="") $feld_stil = "style='width:95%;'";
            $x=0;
            $laenge = $tmp_feld[2][3];
            if ($laenge==0) $laenge = 1;
            for ( $x = 0; $x < $laenge; $x++ )
                {if (is_array($feld_wert)) $feld_wert = $feld_wert[$x];
                $feld_bezeichnung_tmp = $feld_bezeichnung." ".($x+1);
                $html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code;
                if ($feld_wert!="" AND file_exists($img_folder.'/'.$feld_wert))
                    {$html_input .= "<input type='hidden' name='$feld_name' value='$feld_wert'><img src='$img_folder/$feld_wert' width='110px' style='margin-right:10px;'>".olz_buttons("button".$db_table,array(array($feld_bezeichnung." entfernen","")),"");
                    }
                else
                    {if ($feld_wert!="") $html_input .= "<div class='error'>Datei ".$feld_wert." nicht gefunden.</div>";
                    $html_input .= "<input type='file' name='".$feld_name."' class='button'>";
                    }
                $html_input .= $feld_spezial.$$var_alert.$feld_kommentar."</td></tr>\n";
                }
            }
        elseif ($feld_typ == "file") //Input-Typ 'file'
            {include 'library/phpWebFileManager/icons.inc.php';
            $ext = end(explode(".",$feld_wert));
            $ext = $fm_cfg['icons']['ext'][$ext];
            if ($ext!="") $icon = "<img src='icons/".$ext."' class='noborder' style='margin-right:10px;vertical-align:middle;'>";
            else $icon = "";
            if ($feld_stil=="") $feld_stil = "style='width:95%;'";
            if (is_array($feld_wert)) $feld_wert = $feld_wert[$x];
            $feld_bezeichnung_tmp = $feld_bezeichnung." ".($x+1);
            $html_input .= "<tr><td$bez_style><b>".$feld_bezeichnung.":</b>".$tmp_code;
            if (file_exists($def_folder."/".$feld_wert)) $file_folder = $def_folder;
            elseif (file_exists($tmp_folder."/".$feld_wert)) $file_folder = $tmp_folder;
            else $file_folder = "";
            if ($feld_wert!="" AND file_exists($file_folder."/".$feld_wert))
                {$html_input .= "<input type='hidden' name='$feld_name' value='$feld_wert'>$icon<a href='$file_folder/$feld_wert' style='vertical-align:bottom;margin-right:20px;'>$feld_wert</a>".olz_buttons("button".$db_table,array(array($feld_bezeichnung." entfernen","2")),"");
                }
            else
                {if ($feld_wert!="") $html_input .= "<div class='error'>Datei ".$feld_wert." nicht gefunden.</div>";
                $html_input .= "<input type='file' name='".$feld_name."' style='width:100%;'>";
                }
            $html_input .= $feld_spezial.$$var_alert.$feld_kommentar."</td></tr>\n";
            }
        */
    }

    if (isset($tables_img_dirs[$db_table])) {
        $html_input .= "<tr><td colspan='2' style='padding:0px 5px 0px 5px;' class='tablebar'>Bilder</td></tr><tr><td colspan='2'>".olz_images_edit($db_table, $id)."</td></tr>";
    }
    if (isset($tables_file_dirs[$db_table])) {
        $html_input .= "<tr><td colspan='2' style='padding:0px 5px 0px 5px;' class='tablebar'>Dateien</td></tr><tr><td colspan='2'>".olz_files_edit($db_table, $id)."</td></tr>";
    }

    if (isset($_SESSION['edit']['replace'])) {
        $html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Überschreiben", "3"], ["Abbrechen", "2"]], "")."</div>";
    } elseif (($_SESSION['edit']['confirm'] ?? null) == "1") {
        $html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Löschen", "5"], ["Abbrechen", "2"]], "")."</div>";
    } elseif (($_SESSION['edit']['modus'] ?? null) == "neuedit") {
        $html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Vorschau", "3"], ["Abbrechen", "2"]], "")."</div>";
    }
    /*elseif ($db_table=="galerie")
        {$html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table,array(array("Abbrechen","2"),array("Speichern","4")),"")."</div>";}*/
    else {
        $html_menu = "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Vorschau", "3"], ["Löschen", "5"], ["Abbrechen", "2"]], "")."</div>";
    }

    $_SESSION['edit']['vorschau'] = "0";
}
// -------------------------------------------------------------
// Menü
// -------------------------------------------------------------
echo $html_menu ?? '';
if (($alert ?? '') != '') {
    echo "<div class='buttonbar'><span class='error'>".$alert."</span></div>";
}
$alert = "";
if (($html_input ?? '').($html_hidden ?? '') > "") {
    echo "<table class='liste'>".$html_input."</table>".$html_hidden;
}
