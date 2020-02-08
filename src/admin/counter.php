<?php
if ($user == "") {
    $db_table = "counter"; //Datenbank-Tabelle
    $db->query("UPDATE $db_table SET counter = (counter+1) WHERE (page = '$page')");
}
?>
