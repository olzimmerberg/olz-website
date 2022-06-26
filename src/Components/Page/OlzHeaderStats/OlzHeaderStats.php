<?php

namespace Olz\Components\Page\OlzHeaderStats;

class OlzHeaderStats {
    public static function render($args = []) {
        // OLZ Statistik Trainings/Wettkämpfe 2014
        // ---------------------------------------

        global $db;
        require_once __DIR__.'/../../../../_/config/database.php';

        $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%training%')";
        $result = $db->query($sql);
        $training = mysqli_fetch_array($result);
        $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%ol%')";
        $result = $db->query($sql);
        $ol = mysqli_fetch_array($result);

        return "<div style='position:absolute; top:0px; right:0px;'><div class='box-ganz'><div style='border:none;'>
        <h3>Statistik 2014:</h3>
        <p><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[1]."</span> Trainings mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[0]."</span> TeilnehmerInnen
        <br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[1]."</span> Wettkämpfe mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[0]."</span> TeilnehmerInnen</p>
        </div></div></div>";
    }
}