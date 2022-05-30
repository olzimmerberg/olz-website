<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 TRANSITIONAL//EN">
<html>
    <head>
        <title>Live-Resultate</title>
        <meta http-equiv='content-type' content='text/html;charset=utf-8'>

<?php

require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../config/database.php';

include __DIR__.'/parse_result.php';

// -------------------------------------------
// EINSTELLUNGEN
// -------------------------------------------
$zeilen = 30; // Zeilen pro Seite
$intervall_default = 10; // Zeit pro Seite
$spalten_def = [['rang', 8], ['name', 72], ['zeit', 20]]; // Spalten und Spaltenbreite in %
$breite = "100%"; // Tabellenbreite gesamt
$spalten = 4; // Anzahl Ranglisten in der Breite
$db_table = "olz_result";

$intervall = (empty($_GET['intervall'])) ? $intervall_default : $_GET['intervall'];
$event = (empty($_GET['event'])) ? $event : $_GET['event'];
$next_kat = (empty($_GET['next_kat'])) ? 0 : $_GET['next_kat'];
$modus = empty($_GET['modus']) ? "test" : $_GET['modus'];
$kat = $_GET['kat'];
$font_size = "16px";

echo "<style type=\"text/css\">
    * {font-family:Verdana, Calibre, Arial;font-size:{$font_size};}
    .rang {width:5%;text-align:right;padding-right:5px;}
    .name {width:40%;}
    .jg {width:5%;}
    .club {width:35%;}
    .zeit {width:15%;text-align:right;padding-right:5px;}
    .head td{padding:5px;background-color:#A9E87E;font-weight:bold;}
    .grey td{background-color:#FFF;}
    td {vertical-align:top;padding:3px;overflow:hidden;}
    table {border-collapse:collapse;}
    .title {padding-left:10px;font-weight:bold;font-size:120%;margin-bottom:15px;margin-top:10px;}
    .result_kat{border:solid 1px white!important;text-align:center;overflow:hidden;background-color:#DDD;}
    .result_kat a{color:black;font-size:80%;font-weight:bold;margin:0px;text-decoration:none;}

    </style>";

// KATEGORIEN AM EVENT
$sql = "SELECT * FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$name = $row['name'];
/*$kategorien = explode(" ",implode(" ",explode(";",$row['kat_gruppen'])));
sort($kategorien);*/

$sql = "SELECT distinct kat,stand FROM {$db_table} WHERE event='{$event}' ORDER BY kat";
$result = $db->query($sql);
$kategorien = [];
while ($row = mysqli_fetch_array($result)) {
    array_push($kategorien, $row[0]);
}
$count_kat = count($kategorien);

$curr_kat = $kategorien[$next_kat];
$offset_rang = ($next_kat - array_search($curr_kat, $kategorien)) * $zeilen;
$next_kat = ($next_kat < ($count_kat - $spalten)) ? ($next_kat + $spalten) : 0;

echo "<meta http-equiv='refresh' content='{$intervall};url=show_result.php?event={$event}&intervall={$intervall}&kat={$kat}&modus={$modus}&next_kat={$next_kat}'>";
echo "</head>";
echo "<body style='height:99%; background-repeat:repeat; background-image:url(../icns/mainbg.png);width:".$breite."px;margin:0 auto;'>";
// TITEL
echo "<div class='title' style='width:".$breite.";padding-top:20px;'>{$name}<span style='margin-left:30px;float:right;font-weight:normal;font-size:80%;margin-top:5px;margin-right:30px;'>Live-Resultate ".$stand."</span><a href='index.php?modus={$modus}&event={$event}' style='color:white;text-decoration:none;'>•</a></div>";

// KATEGORIEN

echo "<table style='width:".$breite.";border-bottom:solid 1px;height:600px;'><tr>";

if ($count_kat > 0) { // Resultate vorhanden
    $offset = 0;
    $kat_tmp = "";
    for ($m = 0; $m < $spalten; $m++) { // Loop für jede Spalten
        $kat = $kategorien[$next_kat + $m];
        $repeat_kat = ($kat == $kat_tmp) ? $repeat_kat + 1 : 0;
        $offset_rang = ($kat == $kat_tmp or $m == 0) ? $offset_rang : 0;
        $offset = $repeat_kat * $zeilen + $offset_rang;

        $sql = "(SELECT * FROM {$db_table} WHERE kat='{$kat}' AND event='{$event}' ORDER BY rang ASC LIMIT {$offset},{$zeilen})";
        // echo $sql;
        $result = $db->query($sql);
        $kat_tmp = $kat;
        $kat = "";
        $counter = 1;
        echo "<td style='width:".round((100 / $spalten - 2), 0)."%;padding-right:20px;vertical-align:top;'><table width='100%;border:solid 1px;vertical-align:top;'>";

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

            $style = ($counter % 2 == 0) ? " class='grey'" : "";
            $style2 = ($repeat_kat > 0 or $offset_rang > 0) ? " style='background-color:white;border:solid 1px green;'" : "";

            if ($kat == "") { // Spaltenkopf
                $kat = $row['kat'];
                echo "<tr class='head'><td colspan=".count($spalten_def).$style2.">".$kat."<span style='margin-left:30px;'>".$anzahl."</span></td></tr>";
            }
            echo "<tr{$style}>";
            foreach ($spalten_def as $_spalte) {
                // var_dump($_spalte);
                echo "<td class='".$_spalte[0]."' style='width:".$_spalte[1]."%'>".${$_spalte[0]}."</td>";
            }
            echo "</tr>";
            $counter = $counter + 1;
            if ($counter > $zeilen) {
                break;
            }
        }
        for ($x = 0; $x < ($zeilen - $counter + 1); $x++) { // Loop für leere Zeilen
            $style = (($counter + $x) % 2 == 0) ? " class='grey'" : "";
            echo "<tr{$style}><td colspan=".count($spalten_def).">&nbsp;</td></tr>";
        }
        echo "</table></td>";
    }
    echo "</td></tr></tr>";
}

echo "</td></tr></table>";
// if($local) echo "<span style='font-size:80%;margin-left:10px;'>Die Live-Resultate können auch im lokalen WLAN unter '192.168.178.21' abgerufen werden. (Netzwerk: zol, Passwort: olzimmerberg)</span>";

?>
    </body>
</html>