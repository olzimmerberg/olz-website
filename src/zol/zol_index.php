<?php
session_start();

//-------------------------------------------
// Einstellungen
//-------------------------------------------
$db_server_local = "localhost:3306";
$db_user_local = "root";
$db_pw_local = "root";
$db_name_local = "db12229638-1";
$event_default = "zol_190512";

//-------------------------------------------
// UMGEBUNG
//-------------------------------------------
if ($_SERVER['REMOTE_ADDR'] == "::1") {
    $local = 1;
}
$local = 0; // 1 im lokalen WLAN, 0 auf dem olzimmberg.ch-Server

//-------------------------------------------
// Datenbankverbindung
//-------------------------------------------
if ($local) {
    $db = new mysqli($db_server_local, $db_user_local, $db_pw_local, $db_name_local);
} else {
    $db = new mysqli("localhost", "db12229638-1", "Atjiz2ZYty6bN3Tw", "db12229638-1");
}
if ($db->connect_error) {
    die("Connect Error (".$db->connect_errno.") ".$db->connect_error);
}
$db->query("SET NAMES utf8");
function DBEsc($str) {
    global $db;
    return $db->escape_string($str);
}
error_reporting(0);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 TRANSITIONAL//EN">
<html>

    <head>
        <title>Live-Resultate</title>
        <meta http-equiv='content-type' content='text/html;charset=utf-8'>
        <meta http-equiv='refresh' content='60'>

<?php
//-------------------------------------------
// EINSTELLUNGEN
//-------------------------------------------
$event = (isset($_GET['event'])) ? $_GET['event'] : $_SESSION['event'];
$event = empty($event) ? $event_default : $event;
$_SESSION['event'] = $event;
$breite = "450"; // Tabellenbreite gesamt

$db_table = "olz_result";
$kategorie = $_GET['kategorie'];
?>
 <style type="text/css">
    * {font-family:Verdana, Calibre, Arial;font-size:14px;}
    .rang {width:5%;text-align:right;padding-right:5px;}
    .name {width:40%;}
    .jg {width:5%;}
    .club {width:35%;}
    .zeit {width:15%;text-align:right;padding-right:5px;}
    .head td{padding:5px;background-color:#A9E87E;font-weight:bold;font-size:120%;}
    .grey td{background-color:#FFF;}
    td {vertical-align:top;padding:1px;overflow:hidden;}
    table {border-collapse:separate;}
    .title {padding-left:5px;font-weight:bold;font-size:120%;margin-bottom:15px;margin-top:10px;}
 </style>
<script type="text/javascript">
// Kategorie ein-/ausblenden
function toggleKat(kat) {
    var datum = new Date(2015,05,12);
    e = document.getElementById(kat);
    e.style.display=="none" ? e.style.display="block" : e.style.display="none";
    id_vis = getCookie("id_vis");

    if(e.style.display=="block") id_vis = id_vis+kat+"/" ;
    else id_vis = id_vis.split(kat+"/").join("");
    id_vis = id_vis.split("null").join("");
    document.cookie = "id_vis="+id_vis+";expires"+datum;
}

// Sichtbarkeit aus Cookie auslesen
window.onload = function setVis(){
    id_vis = getCookie("id_vis");
    id_vis = id_vis.split("null").join("");
    id_vis = id_vis.split("/");
    count = 0;
    while(id_vis[count]){
        if(id_vis[count]!=null) document.getElementById(id_vis[count]).style.display="block";
        count=count+1;
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


    </head>
<?php

// Counter mit Textdatei
$DateinameIP = "counter.txt"; // Track-Datei
$Zeitsperre = 10; // Zeitsperre in Sekunden
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
    $db->query("UPDATE event SET counter_ip_lan = (counter_ip_lan+1) WHERE (name_kurz = '{$event}')");
}
$db->query("UPDATE event SET counter_hit_lan = (counter_hit_lan+1) WHERE (name_kurz = '{$event}')");

echo "<body style='height:99%; background-repeat:repeat; background-image:url(olzimmerberg.ch/icns/mainbg.png);width:".$breite."px;margin:0 auto;'>";

//-------------------------------------------
// EINSTELLUNGEN
//-------------------------------------------
$spalten = [['rang', 5], ['name', 40], ['jg', 5], ['club', 35], ['zeit', 15]];
$spalten_count = count($spalten);

$sql = "SELECT distinct kat,stand FROM {$db_table} WHERE event='{$event}'";
$result = $db->query($sql);
$kat1 = [];
while ($row = mysqli_fetch_array($result)) {
    array_push($kat1, $row[0]);
}

$sql = "SELECT * FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$name = $row['name'];
$stand = $row['stand'];

$stand = (is_null($stand)) ? "" : "<span style='float:right;font-weight:normal;font-size:80%;margin-top:2px;'>Stand: ".date("d.n.y H:i:s", strtotime($stand)).date(" | H:i:s")."</span>";
echo "<div class='title'>{$name}</div><div style='padding:0 5 0 5;'>Live-Resultate".$stand."</div>";

echo "<table style='width:100%;'>";

$sql = "SELECT * FROM {$db_table} WHERE event='{$event}' ORDER BY kat ASC, rang ASC";
$result = $db->query($sql);

$kat_tmp = "";
$bg = "#FFF";
while ($row = mysqli_fetch_array($result)) {
    $kat = $row['kat'];
    $rang = $row['rang'];
    $name = $row['name'];
    $jg = $row['jg'];
    $club = $row['club'];
    $zeit = $row['zeit'];
    $stand = $row['stand'];
    $anzahl = $row['anzahl'];

    $jg = str_pad($row['jg'], 2, '0', STR_PAD_LEFT);
    $rang = ($rang == 9999) ? '---' : $rang;

    if ($kat_tmp != $kat) {
        if ($kat_tmp > "") {
            echo "</tbody>";
        }
        $kat_tmp = $kat;
        echo "<tr class='head'><td colspan=".count($spalten)."><a href='javascript:;' style='text-decoration:none;color:black;' onclick='toggleKat(\"{$kat}\")'><div style='width:100%;'>".$kat_tmp."<span style='margin-left:30px;float:right;'>".$anzahl."</span></div></a></td></tr><tbody id='{$kat}' style='display:none;'>";
    }
    echo "<tr style='background-color:{$bg};'>";
    $bg = ($bg == "#FFF") ? "#DDD" : "#FFF";
    foreach ($spalten as $_spalte) {
        echo "<td class='".$_spalte[0]."' style='width:".$_spalte[1]."%'>".${$_spalte[0]}."</td>";
    }
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
?>
    </body>
</html>