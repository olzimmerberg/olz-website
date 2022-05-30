<?php

// DEBUGBLOCK: wenn diese Datei als Standalone getestet wird
// $event = "zol_160410";

require_once __DIR__.'/../config/database.php';

if ($local) {
    $db = new mysqli("localhost:8889", "root", "root", "db12229638-1");
} else {
    $db = new mysqli("localhost", "db12229638-1", "Atjiz2ZYty6bN3Tw", "db12229638-1");
}

$temp_event = "zol_temp";
$db_name = "db12229638-1";
mysqli_select_db($db_name);
$db->query('SET NAMES utf8');
$db_table = "olz_result";

$event = (empty($_GET['event'])) ? $event : $_GET['event'];
if (empty($event)) {
    $feedback = "Variable 'event' ist leer.<br>";
}

// Resultatdatei
if ($local) {
    $root = "http://olzimmerberg.ch/zol/";
} // Resultat von olzimmerberg-Server abholen
elseif ($include_param == "karten") {
    $root = "zol/";
} else {
    $root = "";
}
$result_file = $root.$event.".txt";

// Aktualisierungsdatum Resultatdatei
$stand = ($local) ? date("Y-m-d H:i:s", filemtime_remote($result_file)) : date("Y-m-d H:i:s", filemtime($result_file));
$sql = "SELECT stand FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);

// echo microtime(true)."<br>";

if ($stand == $row['stand'] and false) {
    // Aktualisierungsdatum Resultatdatei = Stand Mysql-DB
    $stand = $row['stand'];
    echo "exiting parsing";
} else {
    $tab = "	";
    $space = " ";
    echo "start parsing<br>";
    echo microtime(true)."<br>";

    /*	$fp = fopen($result_file, 'r');
        if ($fp){
            echo 'file is open';}
        else{
            echo 'file is not opened, or not found.';}*/

    // Resultatdatei auslesen
    $results = file_get_contents($result_file);
    $results = str_replace($space.$tab, $tab, $results);
    $rows = explode("\n", $results);

    if (empty($file)) {
        $feedback .= "Resultatdatei ist leer.";
    } else {
        $feedback .= "Resultatdatei gelesen: ".olz_current_date("j.n.y H:i:s").", '".$result_file."'";
    }

    $counter = 0;
    // Resultatdatei zeilenweise auslesen und auswerten
    foreach ($rows as $row) {
        $row = explode($tab, $row); // Spalten in Zeilen aufteilen

        // Spaltendefinition (Bezeichnung gemäss OE2010, z.B. 'Pl', 'Jg', 'Name', 'Verein', 'Zeit')
        // '→Pl.→Stnr.→Name.→Jg.→Verein.→Zeit'
        if ($row[1] == 'Pl') {
            $counter_col = 0;
            foreach ($row as $col) {
                $col = trim($col);
                ${$col} = $counter_col;
                $counter_col = $counter_col + 1;
            }
            $col_def = "";
            $start = "1";
            continue;
        }
        // Kategorietitel (diese Zeile enthält keine Tabs, sondern wird nur mit Leerzeichen gegliedert)
        // 'HAL.Herren.A.lang.(33/33)...........Stand.von:.12:07.....¶'
        if ($row[0] > "") {
            if (isset($debug)) {
                echo str_replace(" ", "***", implode(" ", $row))."<br>";
            }
            $var = $row[0];
            $pos1 = strpos($var, "(");
            $pos2 = strpos($var, ")");
            $kat_tmp = trim(substr($var, 0, $pos1)); // 'HA.Herren.A.lang'
            $pos3 = strpos($kat_tmp, " ");
            $kat_tmp = ($pos3 > 0) ? substr($kat_tmp, 0, $pos3) : $kat_tmp; // 'HA'
            $kat = ($kat_tmp > "") ? $kat_tmp : $kat;
            $kat = mb_convert_encoding($kat, "utf-8", "ISO-8859-1");
            $kat = explode(chr(194), $kat); // chr(194) = ' ' ???
            $kat = $kat[0];
            $kat_count_tmp = trim(substr($var, $pos1, ($pos2 - $pos1 + 1)));
            $kat_count = ($kat_count_tmp > "") ? $kat_count_tmp : $kat_count;
            continue;
        }
        // Ranglistenzeile
        // '→1.→35.→Howald Severin.→89.→OLG Herzogenbuchsee.→31:25 ¶'

        $zeit = trim($row[$Zeit]);
        $var = preg_replace('/[^:0-9]/', '', $row[$Zeit]);
        $rang = ($var == $zeit) ? $row[$Pl] : '9999';
        $rang = ($row[$Pl] == "AK") ? '9998' : $rang;
        $name = mb_convert_encoding($row[$Name], "utf-8", "ISO-8859-1");
        $club = mb_convert_encoding($row[$Verein], "utf-8", "ISO-8859-1");
        $jg = $row[$Jg];
        if ($rang.$name.$club.$jg.$zeit > "") {
            $rang = ($rang == " ") ? "9999" : $rang;
            $sql = "INSERT {$db_table} SET rang='{$rang}',name='{$name}',jg='{$jg}',club='{$club}',zeit='{$zeit}',kat='{$kat}',stand='{$stand}',anzahl='{$kat_count}',event='{$temp_event}'";
            $result = $db->query($sql);
            $counter = $counter + 1;
        }
    }
    // Bestehende Datensätze komplett löschen
    //	echo "end parsing<br>start mysql update step 1<br>";
    //	echo microtime(true)."<br>";
    $sql = "DELETE FROM {$db_table} WHERE event='{$event}'";
    $result = $db->query($sql);
    // Neue Datensätze 'aktivieren'
    //	echo "end step 1<br>start mysql update step 2<br>";
    //	echo microtime(true)."<br>";
    $sql = "UPDATE {$db_table} SET event='{$event}' WHERE event='{$temp_event}'";
    $result = $db->query($sql);
    echo microtime(true)."<br>";

    if (file_exists($result_file)) {
        $sql = "UPDATE event SET stand='{$stand}' WHERE name_kurz='{$event}'";
        $result = $db->query($sql);
    }
}

function filemtime_remote($uri) {
    $uri = parse_url($uri);
    $handle = @fsockopen($uri['host'], 80);
    if (!$handle) {
        return 0;
    }

    fputs($handle, "GET {$uri['path']} HTTP/1.1\r\nHost: {$uri['host']}\r\n\r\n");
    $result = 0;
    while (!feof($handle)) {
        $line = fgets($handle, 1024);
        if (!trim($line)) {
            break;
        }

        $col = strpos($line, ':');
        if ($col !== false) {
            $header = trim(substr($line, 0, $col));
            $value = trim(substr($line, $col + 1));
            if (strtolower($header) == 'last-modified') {
                $result = strtotime($value);
                break;
            }
        }
    }
    fclose($handle);
    return $result;
}
