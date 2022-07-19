<?php

require_once __DIR__.'/../config/database.php';

if ($_GET['modus'] == 'logo') {
    echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'
        'http://www.w3.org/TR/html4/loose.dtd'>
<html><head>
<meta http-equiv='content-type' content='text/html;charset=utf-8'>
<meta name='Content-Language' content='de'>
<title>OL Zimmerberg</title>
<style type='text/css'>
html { 
  background: url(../icns/headerbg_zol.png) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
</style>
</head>";
    echo "<body>";
    echo "<a href='show_result.php?event=zol_131027'><img src='../icns/olz_logo.png' style='position:absolute;top:100px;left:400px;'></a>
<div style='position:absolute;top:500px;left:200px;color:yellow;font-family:Verdana;font-size:48px;'>Herzlich willkommen am 6. Zimmerberg OL</div>";
    echo "</body></html>";
} else {
    echo "<div class='titel'>Events</div>";
    echo "<div style='margin-bottom:10px;'><b>Anleitung:</b><br>1. Neuer Datensatz für Event erzeugen und Name für Exportdatei angeben.<br>2. Automatisches publizieren in OE2010 (Auswertungssoftware) einrichten > 'Ergebnisse' > 'Vorläufig' > 'Kategorien' > 'Einstellungen' > 'Automatischer Bericht', Publizieren, Intervall angeben > 'Start' > Textdatei, Tabs einfügen, lokales Verzeichnis und Dateinamen angeben, 'Dateien ins Internet hochladen' > 'OK' > Server und Zugangsdaten angeben > 'Hochladen'</div><div style='margin-bottom:10px;'><b>Funktionen:</b><br>'Daten importieren': Die Exportdatei wird ausgelesen und in eine Mysql-Tabelle geschrieben. Die Resultate stehen dann für die Online-Abfrage zur Verfügung.<br>'Resultate zeigen (Loop)': Die Resultate werden in einem Loop durch alle Kategorien angezeigt. Gleichzeitig wird die Exportdatei nach jeder Änderung neu ausgelesen.<br>'Resultate zeigen (Homepage): Die Resultate stehen hier online im Internet für den allgemeinen Zugang zur Verfügung.<br>'Kartenstatistik': Übersicht über Kartendruck<b><br>Wichtig:</b><br>- Damit immer die aktuellsten Resultate zur Verfügung stehen, muss auf irgendeinem Rechner eine Loop-Ansicht geöffnet sein.<br>- Im OE2010-Berichtlayout muss die Kurzbezeichnung der Kategorien gewählt werden<br>- Verschiedene Parameter der Loop-Darstellung sind in der Datei 'show_result.php' hart kodiert (Aufteilung und Reihenfolge der Kategorien, Intervall usw.)</div>";

    $event = (isset($_GET['event'])) ? $_GET['event'] : $event;
    if ($_GET['modus'] == 'import') {
        $pfad_event = "zol/";
        include __DIR__.'/parse_result.php';
        echo "<p>";
    }
    $db_table = "event";

    // -------------------------------------------------------------
    // ZUGRIFF
    if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
        $zugriff = "1";
    } else {
        $zugriff = "0";
    }
    $button_name = 'button'.$db_table;
    if (isset($_GET[$button_name])) {
        $_POST[$button_name] = $_GET[$button_name];
    }
    if (isset($_POST[$button_name])) {
        $_SESSION['edit']['db_table'] = $db_table;
    }

    // -------------------------------------------------------------
    // USERVARIABLEN PRÜFEN
    if (isset($id) and is_ganzzahl($id)) {
        $_SESSION[$db_table."id_"] = $id;
    } else {
        $id = $_SESSION[$db_table.'id_'] ?? null;
    }

    // -------------------------------------------------------------
    // DATENSATZ EDITIEREN
    if ($zugriff) {
        $functions = ['neu' => 'Neuer Eintrag',
            'edit' => 'Bearbeiten',
            'abbruch' => 'Abbrechen',
            'vorschau' => 'Vorschau',
            'save' => 'Speichern',
            'delete' => 'Löschen',
            'start' => 'start',
            'undo' => 'undo', ];
    } else {
        $functions = [];
    }

    $function = array_search($_POST[$button_name] ?? null, $functions);
    if ($zugriff and ($function != "")) {
        include __DIR__.'/../admin/admin_db.php';
    }
    if (($_SESSION['edit']['table'] ?? null) == $db_table) {
        $db_edit = "1";
    } else {
        $db_edit = "0";
    }

    // -------------------------------------------------------------
    // MENÜ
    if ($zugriff and $db_edit == "0") {
        echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
    }

    // echo "Um Resultate anzeigen zu können, muss eine Exportdatei aus der Auswertungssoftware im Verzeichnis 'zol/' vorhanden sein.<br>Anleitung:<br>1. Neuer Datensatz in der Mysql-Tabelle<p>";
    // Verzeichnis 'zol/' auslesen
    if ($handle = opendir('zol/')) {
        while (($file = readdir($handle)) !== false) {
            $info = pathinfo($file);
            if ($info['extension'] == "txt") {
                $afile[] = $info['filename'];
            }
        }
        closedir($handle);
    }
    // var_dump($vorschau);
    if ($db_edit == 0 or ($do ?? null) == 'vorschau') {
        if (($do ?? null) == 'vorschau') {
            $sql = "SELECT * FROM event ORDER BY datum DESC LIMIT 1";
        } else {
            $sql = "SELECT * FROM event ORDER BY datum DESC";
        }
        $result = $db->query($sql);

        while ($row = mysqli_fetch_array($result)) {
            if (($do ?? null) == 'vorschau') {
                $row = $vorschau;
            }
            $id_event = $row['id'];
            $name_kurz = $row['name_kurz'];
            $name_event = $row['name'];
            $datum_event = $row['datum'];
            $file_event = (in_array($name_kurz, $afile) or $local) ? "<a href='zol/parse_result.php?event=".$name_kurz."' class='linkint'>Daten importieren</a> | <a href='zol/show_result.php?event=".$name_kurz."&time=".olz_current_date("U")."' target='_blank' class='linkint'>Resultate zeigen (Loop)</a> | <a href='resultate/' class='linkint'>Resultate zeigen (Homepage)</a> | <a href='zol/karten.php' class='linkint'>Kartenstatistik</a>" : "Keine Resultatdatei vorhanden!";
            if (($do ?? null) != 'vorschau') {
                $edit_admin = "<a href='index.php?id={$id_event}&{$button_name}=start' class='linkedit' title='Event bearbeiten'>&nbsp;</a>";
            }
            // http://olzimmerberg.ch/zol/parse_result.php?event=zol_180527
            echo "<div style='margin-bottom:20px;'><b>".$edit_admin.$datum_event.": ".$name_event."</b><br>".$file_event."</div>";
        }
    }
}
