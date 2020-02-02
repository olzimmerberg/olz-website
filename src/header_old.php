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
$result = mysql_query($sql);

if($_SESSION['user']=='ursu' AND 0){
echo "<table style='width:100%;' cellspacing='0'><tr>";

while ($row = mysql_fetch_array($result)){
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
    $result = mysql_query($sql);
    $training = mysql_fetch_array($result);
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%ol%')";
    $result = mysql_query($sql);
    $ol = mysql_fetch_array($result);

    $statistik_text = "<td rowspan='2' class='box_ganz'><div style='border:none;'>
    <h3>Statistik 2014, bis heute:</h3>
    <p><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[1]."</span> Trainings mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[0]."</span> TeilnehmerInnen
    <br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[1]."</span> Wettkampf mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[0]."</span> TeilnehmerInnen</p>    
    </div></td>";
    echo $statistik_text;}
echo "</tr></table";

}
else{
$ganze = array();
$halbe = array();
while ($row = mysql_fetch_array($result))
{
    $wichtig = substr($row["typ"],3+strpos(strtolower($row["typ"]),"box"));
    if ($wichtig=="" || !in_array($wichtig,array(0,1,2))) $wichtig = 2;
    
    // Dateicode einfügen
    $textlang = $row["textlang"];
    preg_match_all("/<datei([0-9]+)(\s+text=(\"|\')([^\"\']+)(\"|\'))?([^>]*)>/i", $textlang, $matches);
    //print_r(htmlentities($textlang)); echo "<br>";
    for ($i=0; $i<count($matches[0]); $i++) {
        $tmptext = $matches[4][$i];
        if (mb_strlen($tmptext)<1) $tmptext = "Datei ".$matches[1][$i];
        $tmp_html = olz_file($db_table, $row["id"], intval($matches[1][$i]), $tmptext);
        $textlang = str_replace($matches[0][$i], $tmp_html, $textlang);
    }
    
    $tmp = array("id"=>$row["id"], "wichtig"=>$wichtig, "titel"=>$row["titel"], "textlang"=>$textlang);
    if ($row['on_off']==2) {
        array_push($halbe,$tmp);
    } else {
        array_push($ganze,$tmp);
    }
}

$html_first_row = "";
$html_second_row = "";
for ($i=0; $i<$header_spalten; $i++) {
    if (0<count($halbe)) {
        if (1==count($halbe)) {
            $html_first_row .= htmlbox($halbe[0],1);
            array_splice($halbe,0,1);
        } else {
            $html_second_row .= htmlbox($halbe[0],2);
            array_splice($halbe,0,1);
            $html_first_row .= htmlbox($halbe[0],2);
            array_splice($halbe,0,1);
        }
    } else if (0<count($ganze)) {
        $html_first_row .= htmlbox($ganze[0],1);
        array_splice($ganze,0,1);
    }
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

if($statistik){
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%training%')";
    $result = mysql_query($sql);
    $training = mysql_fetch_array($result);
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%ol%')";
    $result = mysql_query($sql);
    $ol = mysql_fetch_array($result);

    $statistik_text = "<td rowspan='2' class='box_ganz'><div style='border:none;'>
    <h3>Statistik 2014:</h3>
    <p><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[1]."</span> Trainings mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[0]."</span> TeilnehmerInnen
    <br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[1]."</span> Wettkämpfe mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[0]."</span> TeilnehmerInnen</p>    
    </div></td>";
    $html_second_row .= $statistik_text;}

echo "<table style='width:100%;' cellspacing='0'><tr>
".$html_first_row."
</tr><tr>
".$html_second_row."
</tr></table>";
}

function htmlbox($entry,$typ) {
    global $zugriff,$colors,$button_name;
    $edit_admin = ($zugriff)?"<a href='index.php?page=2&amp;id=".$entry["id"]."&amp;".$button_name."=start' class='linkedit'>&nbsp;</a>":"";
    $class = ($typ=='1') ? 'box_ganz' : 'box_halb' ;
    $rowspan = ($typ=='1') ? ' rowspan=2' : '' ;
    if (!$entry){
        return "<td $rowspan class='$class'>&nbsp;</td>";
        }
    else{
    $titel = ($entry["titel"]!="") ? $edit_admin.$entry["titel"] : "" ;
    return "<td $rowspan class='$class'><div style='border-color:#".$colors[$entry["wichtig"]].";'><h3>".$titel."</h3><div style='padding:0px 5px;'>".olz_br($entry["textlang"])."</div></div></td>";}
}

?>