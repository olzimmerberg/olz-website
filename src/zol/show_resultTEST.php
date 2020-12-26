<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Demo</title>
<link rel="stylesheet" type="text/css" href="css/demo.css" />
<style type="text/css">
div#wn { 
    position:relative; 
    width:400px; height:800px; 
    overflow:hidden;
    border:solid 1px;
    }
div#wn2 { 
    position:relative; 
    width:400px; height:800px; 
    overflow:hidden;
    border:solid 1px;
    }
</style>

</div>
        <div id="rpt">


<?php
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/config/database.php';

include '../admin/olz_init.php';
include 'parse_result.php';
$event = 'zol_160410';
$spalten_def = [['rang', 8], ['name', 72], ['zeit', 20]]; // Spalten und Spaltenbreite in %
$breite = "100%"; // Tabellenbreite gesamt
$db_table = "olz_result";

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

?>
<script src="js/dw_con_scroller.js" type="text/javascript"></script>
<script type="text/javascript">

if ( DYN_WEB.Scroll_Div.isSupported() ) {
    
    DYN_WEB.Event.domReady( function() {
        
        // arguments: id of scroll area div, id of content div
        var wndo = new DYN_WEB.Scroll_Div('wn', 'lyr');
        var wndo2 = new DYN_WEB.Scroll_Div('wn2', 'lyr2');
        // see info online at http://www.dyn-web.com/code/scrollers/continuous/documentation.php
        wndo.makeSmoothAuto( {axis:'v', bRepeat:true, repeatId:'rpt', speed:40, bPauseResume:true} );
        wndo2.makeSmoothAuto( {axis:'v', bRepeat:true, repeatId:'rpt2', speed:40, bPauseResume:true} );
        
    });
}

</script>
</head>
<?php
echo "<body style='height:99%; background-repeat:repeat; background-image:url(../icns/mainbg.png);width:".$breite."px;margin:0 auto;'>";
echo "1";
?>
<table><tr>
<td>
<div id="wn">
    <div id="lyr">
        <div>

<?php
$sql = "SELECT * FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$name = $row['name'];
$sql = "SELECT * FROM {$db_table} WHERE kat LIKE '%D%' AND event='{$event}' ORDER BY kat ASC, rang ASC";
$result = $db->query($sql);
$kat_tmp = $kat;
$kat = "";
$counter = 1;
echo "<table style='width:".$breite.";border-bottom:solid 1px;height:600px;'>";

while ($row = mysqli_fetch_array($result)) {
    $rang = $row['rang'];
    $name = $row['name'];
    $jg = $row['jg'];
    $club = $row['club'];
    $zeit = $row['zeit'];
    $stand = $row['stand'];
    $anzahl = $row['anzahl'];
    $kat = $row['kat'];

    $jg = (strlen($jg) == 1) ? "0".$jg : $jg;
    $rang = ($rang == 9999) ? '---' : $rang;

    $style = ($counter % 2 == 0) ? " class='grey'" : "";
    $style2 = ($repeat_kat > 0 or $offset_rang > 0) ? " style='background-color:white;border:solid 1px green;'" : "";

    if ($kat != $kat_tmp) { // Spaltenkopf
        echo "<tr class='head'><td colspan=".count($spalten_def).$style2.">".$kat."<span style='margin-left:30px;'>".$anzahl."</span></td></tr>";
        $kat_tmp = $kat;
    }
    echo "<tr{$style}>";
    foreach ($spalten_def as $_spalte) {
        echo "<td class='".$_spalte[0]."' style='width:".$_spalte[1]."%'>".${$_spalte}[0]."</td>";
    }
    echo "</tr>";
    $counter = $counter + 1;
}

echo "</td></tr></table>";
?>

</div>
        <div id="rpt">
<?php
$sql = "SELECT * FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$name = $row['name'];
$sql = "SELECT * FROM {$db_table} WHERE kat LIKE '%D%' AND event='{$event}' ORDER BY kat ASC, rang ASC";
$result = $db->query($sql);
$kat_tmp = $kat;
$kat = "";
$counter = 1;
echo "<table style='width:".$breite.";border-bottom:solid 1px;height:600px;'>";

while ($row = mysqli_fetch_array($result)) {
    $rang = $row['rang'];
    $name = $row['name'];
    $jg = $row['jg'];
    $club = $row['club'];
    $zeit = $row['zeit'];
    $stand = $row['stand'];
    $anzahl = $row['anzahl'];
    $kat = $row['kat'];

    $jg = (strlen($jg) == 1) ? "0".$jg : $jg;
    $rang = ($rang == 9999) ? '---' : $rang;

    $style = ($counter % 2 == 0) ? " class='grey'" : "";
    $style2 = ($repeat_kat > 0 or $offset_rang > 0) ? " style='background-color:white;border:solid 1px green;'" : "";

    if ($kat != $kat_tmp) { // Spaltenkopf
        echo "<tr class='head'><td colspan=".count($spalten_def).$style2.">".$kat."<span style='margin-left:30px;'>".$anzahl."</span></td></tr>";
        $kat_tmp = $kat;
    }
    echo "<tr{$style}>";
    foreach ($spalten_def as $_spalte) {
        echo "<td class='".$_spalte[0]."' style='width:".$_spalte[1]."%'>".${$_spalte}[0]."</td>";
    }
    echo "</tr>";
    $counter = $counter + 1;
}

echo "</td></tr></table>";
?>

</div>
    </div>
</div>
</td><td>
<div id="wn2">
    <div id="lyr2">
        <div>

<?php
$sql = "SELECT * FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$name = $row['name'];
$sql = "SELECT * FROM {$db_table} WHERE kat LIKE '%H%' AND event='{$event}' ORDER BY kat ASC, rang ASC";
$result = $db->query($sql);
$kat_tmp = $kat;
$kat = "";
$counter = 1;
echo "<table style='width:".$breite.";border-bottom:solid 1px;height:600px;'>";

while ($row = mysqli_fetch_array($result)) {
    $rang = $row['rang'];
    $name = $row['name'];
    $jg = $row['jg'];
    $club = $row['club'];
    $zeit = $row['zeit'];
    $stand = $row['stand'];
    $anzahl = $row['anzahl'];
    $kat = $row['kat'];

    $jg = (strlen($jg) == 1) ? "0".$jg : $jg;
    $rang = ($rang == 9999) ? '---' : $rang;

    $style = ($counter % 2 == 0) ? " class='grey'" : "";
    $style2 = ($repeat_kat > 0 or $offset_rang > 0) ? " style='background-color:white;border:solid 1px green;'" : "";

    if ($kat != $kat_tmp) { // Spaltenkopf
        echo "<tr class='head'><td colspan=".count($spalten_def).$style2.">".$kat."<span style='margin-left:30px;'>".$anzahl."</span></td></tr>";
        $kat_tmp = $kat;
    }
    echo "<tr{$style}>";
    foreach ($spalten_def as $_spalte) {
        echo "<td class='".$_spalte[0]."' style='width:".$_spalte[1]."%'>".${$_spalte}[0]."</td>";
    }
    echo "</tr>";
    $counter = $counter + 1;
}

echo "</td></tr></table>";
?>

</div>
        <div id="rpt2">
<?php
$sql = "SELECT * FROM event WHERE name_kurz='{$event}'";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$name = $row['name'];
$sql = "SELECT * FROM {$db_table} WHERE kat LIKE '%H%' AND event='{$event}' ORDER BY kat ASC, rang ASC";
$result = $db->query($sql);
$kat_tmp = $kat;
$kat = "";
$counter = 1;
echo "<table style='width:".$breite.";border-bottom:solid 1px;height:600px;'>";

while ($row = mysqli_fetch_array($result)) {
    $rang = $row['rang'];
    $name = $row['name'];
    $jg = $row['jg'];
    $club = $row['club'];
    $zeit = $row['zeit'];
    $stand = $row['stand'];
    $anzahl = $row['anzahl'];
    $kat = $row['kat'];

    $jg = (strlen($jg) == 1) ? "0".$jg : $jg;
    $rang = ($rang == 9999) ? '---' : $rang;

    $style = ($counter % 2 == 0) ? " class='grey'" : "";
    $style2 = ($repeat_kat > 0 or $offset_rang > 0) ? " style='background-color:white;border:solid 1px green;'" : "";

    if ($kat != $kat_tmp) { // Spaltenkopf
        echo "<tr class='head'><td colspan=".count($spalten_def).$style2.">".$kat."<span style='margin-left:30px;'>".$anzahl."</span></td></tr>";
        $kat_tmp = $kat;
    }
    echo "<tr{$style}>";
    foreach ($spalten_def as $_spalte) {
        echo "<td class='".$_spalte[0]."' style='width:".$_spalte[1]."%'>".${$_spalte}[0]."</td>";
    }
    echo "</tr>";
    $counter = $counter + 1;
}

echo "</td></tr></table>";
?>
</div>
    </div>
</div>
</td></tr>

</body>
</html>