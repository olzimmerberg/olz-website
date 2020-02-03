<?php

require_once("image_tools.php");
require_once("file_tools.php");

// OLZ Statistik Trainings/Wettkämpfe 2014
$statistik = ($_SESSION['user']=='ursu') ? true : true;
$header_spalten = $statistik ? 2 : 3 ;

$colors = array("dd0000","00cc00","005500"); // Farbe Randbalken

$db_table = "aktuell";
$zugriff = (($_SESSION['auth'] == "all") OR (in_array($db_table ,explode(' ',$_SESSION['auth'])))) ? true : false ;
$button_name = "button".$db_table;
if(isset($$button_name)) $_SESSION['edit']['db_table'] = $db_table;

$sql = "SELECT * FROM $db_table WHERE (on_off != '0') AND (typ LIKE '%box%') ORDER BY typ ASC";
$result = $db->query($sql);

if($_SESSION['user']=='ursu' AND 0){
echo "<table style='width:100%;' cellspacing='0'><tr>";

while ($row = mysqli_fetch_array($result)){
    $var = explode(' ',$row['typ']); // 'box 2 1 3' > Box 2. Spalte, 1. Zeile, 3.Farbe
    $spalte = $var[1];
    $zeile = $var[2];
    $farbe = $var[3];
    if($counter<3 AND $spalte<=$header_spalten){
        $html_header[($spalte-1)][$zeile] = "<div class='box_halb'><h3>".$row['titel']."</h3>".$row['textlang']."</div>" ;
        $counter = $counter+1 ;
        }
    }
//var_dump($html_header);
foreach($html_header as $html_spalte){
    $var = "<td rowspan='2' class='box_ganz'>".implode('',$html_spalte)."</td>";
    echo $var;
    }
if($statistik){
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%training%')";
    $result = $db->query($sql);
    $training = mysqli_fetch_array($result);
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%ol%')";
    $result = $db->query($sql);
    $ol = mysqli_fetch_array($result);

    $statistik_text = "<td rowspan='2' class='box_ganz'><div style='border:none;'>
    <h3>Statistik 2014, bis heute:</h3>
    <p><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[1]."</span> Trainings mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[0]."</span> TeilnehmerInnen
    <br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[1]."</span> Wettkampf mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[0]."</span> TeilnehmerInnen</p>
    </div></td>";
    echo $statistik_text;}
echo "</tr></table>";

}
else{
$ganze = array();
while ($row = mysqli_fetch_array($result))
{
    $wichtig = substr($row["typ"],3+strpos(strtolower($row["typ"]),"box"));
    if ($wichtig=="" || !in_array($wichtig,array(0,1,2))) $wichtig = 2;

    // Dateicode einfügen
    $textlang = $row["textlang"];
    preg_match_all("/<datei([0-9]+)(\s+text=(\"|\')([^\"\']+)(\"|\'))?([^>]*)>/i", $textlang, $matches);
    //preg_match_all("/<datei([0-9]+)[^>]*(\s+file=(\"|\')([^\"\']+)(\"|\'))[^>]*>/i", $textlang, $matches_file);

    for ($i=0; $i<count($matches[0]); $i++) {
        $tmptext = $matches[4][$i];
        $tmpfile = $matches_file[4][$i];
		//if($_SESSION['auth']=='all') echo $i."***2".$matches_file[4][$i]."<br>";
        if (mb_strlen($tmptext)<1) $tmptext = "Datei ".$matches[1][$i];
        $tmp_html = olz_file($db_table, $row["id"], intval($matches[1][$i]), $tmptext);
        $textlang = str_replace($matches[0][$i], $tmp_html, $textlang);
    }

    $tmp = array("id"=>$row["id"], "wichtig"=>$wichtig, "titel"=>$row["titel"], "textlang"=>$textlang);
    array_push($ganze,$tmp);
}

$html_first_row = "";
for ($i=0; $i<$header_spalten; $i++) {
    $html_first_row = "<div style='position:absolute; top:0px; right:".floor(($i+($statistik?1:0))*252)."px;'>".htmlbox($ganze[0],1)."</div>".$html_first_row;
    array_splice($ganze,0,1);
}
if($statistik){
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%training%')";
    $result = $db->query($sql);
    $training = mysqli_fetch_array($result);
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%ol%')";
    $result = $db->query($sql);
    $ol = mysqli_fetch_array($result);

    $statistik_text = "<div style='position:absolute; top:0px; right:0px;'><div class='box_ganz'><div style='border:none;'>
    <h3>Statistik 2014:</h3>
    <p><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[1]."</span> Trainings mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[0]."</span> TeilnehmerInnen
    <br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[1]."</span> Wettkämpfe mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[0]."</span> TeilnehmerInnen</p>
    </div></div></div>";

    // OLZ Trophy 2017
    echo "<div style='float:right;'><a href='?page=20'><img src='".$data_href."img/trophy2016.png' alt='trophy' style='position:relative; top:10px;' class='noborder' /></a></div>";

    // OLZ JOM-Counter 2019
    $jom_solv_uids = array(9610, 9543, 9781, 9636, 9542, 9541, 9380, 9390, 9950, 9815, 9821);
    $sql = "SELECT sp1.name AS name, COUNT(*) AS cnt, GROUP_CONCAT(se.name SEPARATOR '\n') AS events FROM solv_results sr LEFT JOIN solv_people sp ON (sr.person=sp.id) LEFT JOIN solv_people sp1 ON (sp.same_as=sp1.id) LEFT JOIN solv_events se ON (sr.event=se.solv_uid) WHERE sr.event IN ('".implode("', '", $jom_solv_uids)."') AND sr.class IN ('H10', 'H12', 'H14', 'H16', 'H18', 'D10', 'D12', 'D14', 'D16', 'D18') GROUP BY sp1.id ORDER BY cnt DESC"; // cnt DESC, se.date ASC
    $result = $db->query($sql);
    $num_people = $result->num_rows;
    $sum_starts = 0;
    $htmlout = "<table>";
    $htmlout .= "<tr><th>Name</th><th style='text-align:right;'>Starts</th></tr>";
    for ($i=0; $i<$result->num_rows; $i++) {
        $row = $result->fetch_assoc();
        $htmlout .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
        $sum_starts += $row['cnt'];
    }
    $htmlout .= "</table>";
    $sql = "SELECT name FROM solv_events se WHERE se.solv_uid IN ('".implode("', '", $jom_solv_uids)."') AND se.date<=DATE(CURRENT_TIMESTAMP)";
    $result = $db->query($sql);
    $num_JOMs = $result->num_rows;
    echo "<a href='' onclick='var elem = document.getElementById(&quot;jom-counter-ranking&quot;); elem.style.display = (elem.style.display==&quot;block&quot;?&quot;none&quot;:&quot;block&quot;); return false;'><div style='position:absolute; top:0px; right:150px;'><div style='width:80px;' class='box_ganz'><div style='width:80px; border:0px;'><h2 style='font-size:12px; border:0px; text-align:center;'>JOM-Zähler</h2>";
    echo "<div style='margin-top:5px; font-size:20px; text-align:center;' title='Anzahl Starts von OLZ-JOM-Läufern'>&#9651;".$sum_starts."</div>";
    echo "<div style='margin-top:5px; font-size:20px; text-align:center;' title='Durchschnittliche Anzahl OLZ-JOM-Läufer pro Anlass'>&#216;".number_format($sum_starts/$num_JOMs, 1, ".", "")."</div>";
    echo "<div style='margin-top:5px; font-size:20px; text-align:center;' title='Anzahl OLZ-JOM-Läufer'>&#9786;".$num_people."</div>";
    echo "</div></div></div></a>";
    echo "<div style='position:absolute; top:0px; right:252px; z-index:1000; display:none;' id='jom-counter-ranking'><div class='box_ganz'><div style='margin-top:8px; border:0px; overflow-y:scroll;'><div style='padding:5px;'>";
    echo $htmlout;
    echo "</div></div></div></div>";

    /*
    // OLZ JOM Team Challenge 2015
    echo "<div style='position:absolute; top:0px; right:0px;'><div class='box_ganz'><div style='border-left:5px solid rgb(255,250,0);'><h2>Nachwuchs Challenge</h2><form name='Formularh' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>";
    olz_text_insert(9, false);
    echo "</form></div></div></div>";
    */
    //echo $statistik_text;
    }


/*function htmlboxhalbe($entry) {
    global $zugriff,$root_path,$colors;
    $edit_admin = ($zugriff)?"<a href='index.php?page=2&amp;id=".$entry["id"]."&amp;buttonaktuell=start' class='linkedit'>&nbsp;</a>":"";

    if (!$entry){
        return "<td rowspan='2' style='vertical-align:middle;'><div class='box_halb'></div></td>";
        }
    else{
    $titel = ($entry["titel"].$entry["textkurz"]!="") ? $edit_admin.$entry["titel"].$entry["textkurz"] : "" ;
    return "<td rowspan='2' style='vertical-align:middle;'><div class='box_halb' style='border-color:#".$colors[$entry["wichtig"]].";'><h3>".$titel."</h3><p>".olz_br($entry["textlang"])."</p></div></td>";}
}*/

echo $html_first_row;
}

function htmlbox($entry,$typ) {
    global $zugriff,$colors,$button_name;
    $edit_admin = ($zugriff)?"<a href='index.php?page=2&amp;id=".$entry["id"]."&amp;".$button_name."=start' class='linkedit'>&nbsp;</a>":"";
    if (!$entry) return "<div class='box_ganz'>&nbsp;</div>";
    $titel = ($entry["titel"]!="") ? $edit_admin.$entry["titel"] : ""; // Wieso???
    return "<div class='box_ganz'><div style='border-color:#".$colors[$entry["wichtig"]].";'><h3>".$titel."</h3><div style='padding:0px 5px;' class='box_content'>".olz_br($entry["textlang"])."</div></div></div>";
}

?>
