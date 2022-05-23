<script>
// Zähler +1
function KartenIncr(idnr){
    //break; // für ZOL 2016 abgeschaltet
    document.getElementById("kat"+idnr).innerHTML=document.getElementById("kat"+idnr).innerHTML/1+1;
    document.getElementById("diff"+idnr).innerHTML=document.getElementById("diff"+idnr).innerHTML/1+1;
    setStyle(idnr);
    KartenStand();
}
//Zähler -1
function KartenDecr(idnr){
    //break; // für ZOL 2016 abgeschaltet
    document.getElementById("kat"+idnr).innerHTML=document.getElementById("kat"+idnr).innerHTML/1-1;
    document.getElementById("diff"+idnr).innerHTML=document.getElementById("diff"+idnr).innerHTML/1-1;
    setStyle(idnr);
    KartenStand();
}

// Negativwerte rot
function setStyle(idnr){
if(document.getElementById("diff"+idnr).innerHTML/1<0){
    document.getElementById("diff"+idnr).style.color="red";
    document.getElementById("kat"+idnr).style.color="red";}
else{
    document.getElementById("diff"+idnr).style.color="black";
    document.getElementById("kat"+idnr).style.color="black";}
}

// Stand Nachdrucke in Cookie abspeichern
function KartenStand(){
    var datum = new Date(2015,05,12);
    delimiter = "";
    karten = "";
    count = 0;
    test = document.getElementById("kat"+count);
    while(test){
        karten = karten + delimiter + document.getElementById("kat"+count).innerHTML/1;
        delimiter = "/";
        count = count+1;
        test = document.getElementById("kat"+count);
    }
document.cookie = "karten="+karten+";expires"+datum;
}

// Stand Nachdrucke aus Cookie auslesen und in Tabelle schreiben bei Neuladen des Dokumentes
window.onload = function KartenStandLesen(){
    kartenstand = getCookie("karten");
    if(kartenstand!=null) kartenstand = kartenstand.split("/");
    count = 0;
    test = document.getElementById("kat"+count);
    while(test){
    if(kartenstand!=null){
        document.getElementById("kat"+count).innerHTML=kartenstand[count];
        document.getElementById("diff"+count).innerHTML=((document.getElementById("diff"+count).innerHTML/1)+kartenstand[count]/1);}
        setStyle(count);
        count = count+1;
        test = document.getElementById("kat"+count);
    }
}

// Stand Nachdrucke auf 0 zurücksetzen
function KartenStandReset(){
    $btn = confirm("Zählerstand zurücksetzen?");
    if($btn){
        count = 0;
        test = document.getElementById("kat"+count);
        while(test){
            document.getElementById("kat"+count).innerHTML="0";
            setStyle(count);
            count = count+1;
            test = document.getElementById("kat"+count);
            }
        document.cookie="karten=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        window.location.reload();
    }
}

//Cookie auslesen
function getCookie(name) { 
    var mein_cookie = document.cookie; 
    if (mein_cookie.indexOf(name) == -1) { 
    return null; 
    }
    var anfang = mein_cookie.indexOf(name) + name.length + 1;  
    var ende = mein_cookie.indexOf(";", anfang); 
    if (ende == -1) { 
        ende = mein_cookie.length; 
    } 
    var laenge = ende - anfang;
    var cookie_wert = unescape(mein_cookie.substr(anfang,laenge));
 	return cookie_wert; 
}

</script>


<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/date.php';

$db_table = "olz_result";
$event = (isset($_GET['event'])) ? $_GET['event'] : $_SESSION['event'];
$_SESSION['event'] = $event;
$_GET['event'] = $event;

$include_param = "karten";
include_once "parse_result.php";
$sql = "SELECT * FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$gruppen = explode(";", $row['kat_gruppen']);
$bahnen = [];
foreach ($gruppen as $gruppe_tmp) {
    array_push($bahnen, explode(" ", $gruppe_tmp));
}
$karten = explode(";", $row['karten']);
$event_name = $row['name'];

$result_file = "zol/".$event.".txt";
$var = filemtime($result_file);
$datum = $_DATE->olzDate("t.m.jj", $var).date(", H:i:s", $var);
$zeit = olz_current_date("H:i:s");

echo "<div class='titel' style='margin:10px 0px 10px 0px;'>{$event_name} / Stand Resultatdatei: {$datum} / Seite aktualisiert: {$zeit}</div>";
echo "<button style='height:30px;text-align:center;border:solid 1px grey;padding:3px;margin-bottom:10px;margin-right:5px;' type='button' onclick='KartenStandReset()'>Zähler zurücksetzen</button><button style='height:30px;text-align:center;border:solid 1px grey;padding:3px;margin-bottom:10px;' type='button' onclick='window.location.reload()'>Seite neu laden</button><span style='vertical-align:text-bottom;margin-left:20px;'>(Seite lädt sich automatisch neu alle 60 Sekunden.)</span>";
echo "<table style='table-layout:fixed;' class='kartenstatistik'><tr><td>Kategorien/Bahnen</td><td>gedruckt</td><td>angemeldet</td><td>Nachdruck</td><td>Differenz</td><tr>";
$count = 0;

foreach ($bahnen as $_bahn) {
    $kat = implode("' OR kat='", $_bahn);
    $kat = "kat='".$kat."'";
    $sql = "SELECT distinct kat,stand,anzahl FROM {$db_table} WHERE event='{$event}' AND ({$kat})";
    // echo $sql;
    $result = $db->query($sql);
    $sum1 = 0;
    $sum2 = 0;
    $var1 = [];
    while ($row = mysqli_fetch_array($result)) {
        $var = explode('/', $row['anzahl']);
        // echo $row['anzahl']."***";
        $angem = ereg_replace("[^0-9]", "", $var[0]);
        $ausgel = ereg_replace("[^0-9]", "", $var[1]);
        // echo $_bahn."/".$ausgel."***";
        $var1[] = $row['kat'];
        $sum1 = $sum1 + $ausgel;
        $sum2 = $sum2 + $angem;
    }
    $style = ($sum1 >= $karten[$count]) ? "color:red;" : "";
    echo "<tr><td style='width:100px;'>".implode("/", $_bahn)."</td><td style='width:60px;'>".$karten[$count]."</td><td style='width:60px;'>".$sum1."</td><td style='width:100px;'><button style='width:30px;height:30px;text-align:center;font-size:18px;border:solid 1px grey;margin-right:5px;' type='button' onclick='KartenIncr({$count})'>+</button><button style='width:30px;height:30px;text-align:center;font-size:18px;border:solid 1px grey;margin-right:5px;' type='button' onclick='KartenDecr({$count})'>-</button><span id='kat{$count}'>0</span></td><td style='width:60px;{$style}'><span id='diff{$count}'>".($karten[$count] - $sum1)."</span></td></tr>";
    $count = $count + 1;
}
echo "</table>";

$kat = (empty($_GET['kat'])) ? "" : $_GET['kat'];
$sql = "SELECT distinct kat,stand,anzahl FROM {$db_table} WHERE event='{$event}'";
$result = $db->query($sql);
$kat1 = [];
$kat2 = [];
$kat3 = [];

while ($row = mysqli_fetch_array($result)) {
    if (substr($row[0], 0, 1) == "H") {
        $kategorien1 .= "<tr><td style='border:solid 0px;'>".$row[0]."</td><td>".$row[2]."</td></tr>";
    } elseif (substr($row[0], 0, 1) == "D") {
        $kategorien2 .= "<tr><td style='border:solid 0px;'>".$row[0]."</td><td>".$row[2]."</td></tr>";
    } else {
        $kategorien3 .= "<tr><td style='border:solid 0px;'>".$row[0]."</td><td>".$row[2]."</td></tr>";
    }
}
sort($kategorien1);
sort($kategorien2);
sort($kategorien3);

$temp = $kategorien1.$kategorien2.$kategorien3;
if (empty($temp)) {
    echo "<div style='margin-left:10px;'>Es sind noch keine Resultate verfügbar.</div>";
} else {
    echo "<div class='titel' style='margin:10px 0px 10px 0px;'>Kategorien (angemeldet/ausgelesen) / Stand: {$datum}</div>";
}

echo "<table style='margin-bottom:20px;border-collapse:collapse;'>".$kategorien1.$kategorien2.$kategorien3."</tr></table>";
?>
