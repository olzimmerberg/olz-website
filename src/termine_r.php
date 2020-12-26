<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

require_once __DIR__.'/config/database.php';

echo "<h2>Trainings</h2>";

// NÄCHSTES TRAINIG
//Konstanten
$db_table = "termine";

//Tabelle auslesen
$sql = "select * from {$db_table} WHERE ((datum_end >= '{$heute}') OR (datum_end = '0000-00-00') OR (datum_end IS NULL)) AND (datum >= '{$heute}') AND (typ LIKE '%training%') AND (on_off = '1') ORDER BY datum ASC";
//echo $sql;
$result = $db->query($sql);

$row = mysqli_fetch_array($result);
$datum = strtotime($row['datum']);
$titel = $row['titel'];
$text = $row['text'];
$id_training = $row['id'];

$datum = date("j. ", $datum).utf8_encode(strftime("%B", $datum));
if ($titel == "") {
    $titel = substr(str_replace("<br>", " ", $text), 0, $textlaenge);
}

if ($row['datum'] > 0) {
    echo "<p><b>Nächstes Training: </b>{$datum}<br>{$titel}, {$text}</p>";
}
echo get_olz_text(1);
echo "<h2>Downloads und Links</h2>";
echo get_olz_text(2);
echo "<h2>Newsletter</h2>";
echo get_olz_text(3);
