<?php

// =============================================================================
// Kann am Zimmerberg OL Resultate anzeigen.
// Diese Datei wurde unabhängig von `results/` entwickelt. Es existieren
// Doppelspurigkeiten.
// =============================================================================

//-------------------------------------------
// Counter mit Textdatei
//-------------------------------------------
if ($_SESSION['auth'] != 'all') {
    $DateinameIP = "zol/counter.txt"; // Track-Datei
    $Zeitsperre = 3600; // Zeitsperre in Sekunden
    $Gefunden = false;
    $IPListe = file($DateinameIP);
    if (count($IPListe) > 0) {
        foreach ($IPListe as $Zeile) { // IP prüfen
            $GesplitteteZeile = explode("|", $Zeile);
            if (time() < ($GesplitteteZeile[0] + $Zeitsperre)) {
                $NeueIPListe[] = trim($Zeile)."\n";
            } // neue IP
        }
        if (count($NeueIPListe) > 0) {
            foreach ($NeueIPListe as $Zeile) {
                $GesplitteteZeile = explode("|", $Zeile);
                if (trim($GesplitteteZeile[1]) == session_id()) { //IP Prüfung
                    $Gefunden = true;
                }
            }
        }
    }
    $FilePointerIP = fopen($DateinameIP, "w"); // IP-Track-Datei öffnen
    if (count($IPListe) > 0 && count($NeueIPListe) > 0) {
        foreach ($NeueIPListe as $Zeile) { // IP-Liste ändern
            fwrite($FilePointerIP, trim($Zeile)."\n");
        }
    }
    if (!$Gefunden) {
        fwrite($FilePointerIP, time()."|".session_id()."\n");
    } //IP-Liste ergänzen
    fclose($FilePointerIP);

    if (!$Gefunden) {
        $db->query("UPDATE event SET counter_ip_web = (counter_ip_web+1) WHERE (name_kurz = '{$event}')", $conn_id);
    }
    $db->query("UPDATE event SET counter_hit_web = (counter_hit_web+1) WHERE (name_kurz = '{$event}')", $conn_id);
}
//-------------------------------------------

//-------------------------------------------
// EINSTELLUNGEN
//-------------------------------------------
$spalten = [['rang', 5], ['name', 40], ['jg', 5], ['club', 35], ['zeit', 15]]; // Spalten und Spaltenbreite in %
//$spalten = array(array('rang',8),array('name',72),array('zeit',20)); // Spalten und Spaltenbreite in %
$spalten_count = count($spalten);
$db_table = "olz_result";

//$event = (empty($_GET['event'])) ? "zol_130602" : $_GET['event'];
$event = (isset($_GET['event'])) ? $_GET['event'] : $_SESSION['event'];
$_SESSION['event'] = $event;

$kat = (empty($_GET['kat'])) ? "" : $_GET['kat'];
$sql = "SELECT distinct kat,stand FROM {$db_table} WHERE event='{$event}'";
$result = $db->query($sql);
$kat1 = [];
$kat2 = [];
$kat3 = [];
$kat4 = [];
while ($row = mysqli_fetch_array($result)) {
    //echo $row[0]."/".substr($row[0],-1)."<br>";
    if (substr($row[0], -1) == "T") {
        array_push($kat4, $row[0]);
    } elseif (substr($row[0], 0, 1) == "H") {
        array_push($kat1, $row[0]);
    } elseif (substr($row[0], 0, 1) == "D") {
        array_push($kat2, $row[0]);
    } else {
        array_push($kat3, $row[0]);
    }
}

foreach ($kat1 as $_kat) {
    $kategorien1 = $kategorien1."<td style='border:solid 0px;width:".round(100 / count($kat1))."%' class='result_kat'><a href='?page={$page}&amp;kat={$_kat}&event={$event}'>{$_kat}</a></td>";
}
foreach ($kat2 as $_kat) {
    $kategorien2 = $kategorien2."<td style='border:solid 0px;' class='result_kat'><a href='?page={$page}&amp;kat={$_kat}&event={$event}'>{$_kat}</a></td>";
}
foreach ($kat3 as $_kat) {
    $kategorien3 = $kategorien3."<td style='border:solid 0px;' class='result_kat'><a href='?page={$page}&amp;kat={$_kat}&event={$event}'>{$_kat}</a></td>";
}
foreach ($kat4 as $_kat) {
    $kategorien4 = $kategorien4."<td style='border:solid 0px;' class='result_kat'><a href='?page={$page}&amp;kat={$_kat}&event={$event}'>{$_kat}</a></td>";
}

$result_file = "zol/".$event.".txt";
$datum = olz_date("t.m.jj", filemtime($result_file));

$sql = "SELECT * FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$name = $row['name'];

$sql = "SELECT * FROM olz_result WHERE event='{$event}' LIMIT 1";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);

$temp = $kategorien1.$kategorien2.$kategorien3.$kategorien4;

$stand = (empty($temp)) ? "" : "<span style='float:right;font-size:100%;color:black;'>(Stand: ".date("d.n.y H:m:s", strtotime($row['stand'])).")</span>";
echo "<div class='titel tablebar'>Resultate {$name}{$stand}</div>";

if (empty($temp)) {
    echo "<div style='margin-left:10px;'>Es sind noch keine Resultate verfügbar.<br>Die Resultate können am Lauftag auf dieser Internetseite oder auf <a href='http://www.compass-zos.ch/resultate/resultate_2_nat_ol_2019.html' target='_blank' class='linkext'>compass-zos.ch</a> eingesehen werden.</div>";
} else {
    echo "<div class='titel' style='margin:10px 0px 10px 0px;'>Kategorien<a href='zol/zol_index.php?event=zol_190512' class='linkint' style='vertical-align:baseline;'>Mobile-Version</a></div>";
}
echo "<table style='margin-bottom:20px;border-collapse:collapse;'><tr>".$kategorien4."</tr><tr>".$kategorien1."</tr><tr>".$kategorien2."</tr><tr>".$kategorien3."</tr></table>";

$sql = "SELECT anzahl,stand FROM {$db_table} WHERE kat='{$kat}' AND event='{$event}' LIMIT 1";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$stand = $row['stand'];
$anzahl = $row['anzahl'];
echo "<div class='titel' style='margin:10px 0px 10px 0px;'>{$kat}  {$anzahl}</div>";
echo "<table>";

$sql = "SELECT * FROM {$db_table} WHERE kat='{$kat}' AND event='{$event}' ORDER BY rang ASC";
$result = $db->query($sql);
$counter = 1;
while ($row = mysqli_fetch_array($result)) {
    $rang = $row['rang'];
    $name = $row['name'];
    $jg = $row['jg'];
    $club = $row['club'];
    $zeit = $row['zeit'];
    $stand = $row['stand'];
    $anzahl = $row['anzahl'];

    $jg = (strlen($jg) == 1) ? "0".$jg : $jg;
    $rang = ($rang == 9999) ? '---' : $rang;
    $rang = ($rang == 9998) ? 'AK' : $rang;

    $style = ($counter % 2 == 0) ? "background-color:white;" : "";

    echo "<tr>";
    foreach ($spalten as $_spalte) {
        echo "<td style='width:".$_spalte[1]."%;padding:2px;{$style}' class=".$_spalte[0].">".${$_spalte[0]}."</td>";
    }
    echo "</tr>";
    $counter = $counter + 1;
}

echo "</tr></table>";

?>

    </body>
</html>
