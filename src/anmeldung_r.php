<h2>OLZ-Anmeldungen intern</h2>

<?php
$db_table = "termine";

//-------------------------------------------------------------
// LISTE ANMELDUNGEN INTERN
if ($zugriff) {
    $sql = "SELECT * FROM {$db_table} WHERE (datum_anmeldung>'0000-00-00') ORDER BY datum ASC";
} else {
    $sql = "SELECT * FROM {$db_table} WHERE (datum_anmeldung>'0000-00-00') AND (datum >= '".olz_date("jjjj-mm-tt", "")."') ORDER BY datum ASC";
}

$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $datum = $row['datum'];
    $id = $row['id'];
    $titel = $row['titel'];
    $datum_ = olz_date("t. MM", $datum);
    $sql = "SELECT SUM(anzahl) FROM anmeldung WHERE (event_id={$id})";
    $result_anm = mysql_query($sql);
    $count = mysql_fetch_array($result_anm);
    if ($zugriff) {
        $edit_admin = "<a href='index.php?page=3&amp;id={$id}&amp;button{$db_table}=start' class='linkedit'>&nbsp;</a>";
    } else {
        $edit_admin = "";
    }
    echo $edit_admin."<a href='index.php?page=13&amp;id_event=".$id."' class='linkint'>".$titel." (".$datum_."): <b>".$count[0]."</b></a>";
}
?>

<h2>OLZ-Anmeldungen auf GO2OL</h2>

<?php
$db_table = "termine";
$event = [];
$url = "http://www.go2ol.ch/";

//-------------------------------------------------------------
// LISTE ANMELDUNGEN GO2OL
$sql = "SELECT * FROM {$db_table} WHERE (go2ol > '') AND (datum>='".olz_date("jjjj-mm-tt", "")."') ORDER BY datum ASC";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    array_push($event, [$row['titel'], $row['go2ol'], strtotime($row['datum'])]);
}
foreach ($event as $thisevent) {
    $url_ = $url.$thisevent[1]."/teilnehmer_verein.asp?verein=13";
    $ch = curl_init($url_);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    $file = curl_exec($ch);
    //$error = curl_getinfo($url_, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if (trim($file) == "") {//echo "Go2ol-Daten können nicht abgefragt werden.";
    } else {
        $count = substr_count($file, "class=\"tabtext\"");
        $datum_ = utf8_encode(date("j. ", $thisevent[2]).strftime("%B", $thisevent[2]));
        echo "<a href='".$url_."' target='_blank' class='linkext'>".$thisevent[0]." ({$datum_}): <b>".$count."</b></a>";
    }
}
?>