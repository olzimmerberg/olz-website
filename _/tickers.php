<?php

// =============================================================================
// Versuch, die verschiedenen Typen von Einträgen irgendwie zu vereinheitlichen.
// TODO(simon): Was davon ist in Gebrauch? Was soll geschehen?
// =============================================================================

use Olz\Utils\AbstractDateUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

function termine_ticker($settings) {
    $date_utils = AbstractDateUtils::fromEnv();
    $code_href = EnvUtils::fromEnv()->getCodeHref();
    $db = DbUtils::fromEnv()->getDb();

    $textlaenge_def = isset($settings["eintrag_laenge"]) ? intval($settings["eintrag_laenge"]) : 80;
    $listenlaenge = isset($settings["eintrag_anzahl"]) ? intval($settings["eintrag_anzahl"]) : 8;
    $sql_where = isset($settings["sql_where"]) ? $settings["sql_where"] : "";
    $titel = isset($settings["titel"]) ? $settings["titel"] : "Termine";
    $heute_highlight = isset($settings["heute_highlight"]) ? $settings["heute_highlight"] : true;
    // Konstanten
    $db_table = "termine";
    $heute = $date_utils->getCurrentDateInFormat("Y-m-d");
    echo "<div class='layout'>";
    echo "<h4 class='tablebar'>".$titel."</h4>";
    // Tabelle auslesen
    $sql = "select * from {$db_table} WHERE ((datum >= '{$heute}') OR (datum_end >= '{$heute}')) AND (on_off = 1)".$sql_where." ORDER BY datum ASC LIMIT {$listenlaenge}";
    $result = $db->query($sql);

    // TEST uu/1.4.2011
    // Was, wenn ein mehrtägiger Event vor x Tagen begonnen hat? simon/23.5.2011
    $pulse = "";
    $wotag = $date_utils->getCurrentDateInFormat("w");
    if ($wotag == 0) {
        $wotag = 7;
    }
    $sections = ["Heute", "Diese Woche", "Nächste Woche", "In [x] Tagen", "Spätere Termine"];
    $flag = 1;

    $case = null;
    while ($row = mysqli_fetch_array($result)) {
        $datum_tmp = $row['datum'];
        $datum_end = $row['datum_end'];
        $timestamp_tmp = $datum_tmp ? strtotime($datum_tmp) : 0;
        $timestamp_heute = $heute ? strtotime($heute) : 0;
        $timestamp_end = $datum_end ? strtotime($datum_end) : 0;
        $diff = ($timestamp_tmp - $timestamp_heute) / 86400;
        $diff_end = ($timestamp_end - $timestamp_tmp) / 86400;
        $time = $diff * 86400;
        $class_heute = "";
        if ($diff < 0.95) { // Sommerzeitwechsel: (strtotime('2014-03-31')-strtotime('2014-03-30'))/86400 = 0.958...
            $case_tmp = 1;
            if (($datum_end != '0000-00-00' and $datum_end !== null) and $diff_end > 6) {
                $datum_end = '(bis '.$date_utils->olzDate('WW t.m.', $datum_end).')';
            } elseif (($datum_end != '0000-00-00' and $datum_end !== null) and $diff_end > 0) {
                $datum_end = '(bis '.$date_utils->olzDate('WW', $datum_end).')';
            } else {
                $datum_end = '';
            }
            $datum = $datum_end;
            if ($heute_highlight) {
                $class_heute = " class='heute'";
            }
        } elseif ($diff < (7.95 - $wotag)) {
            $case_tmp = 2;
            if (($datum_end != '0000-00-00' and $datum_end !== null) and $diff_end > 6) {
                $datum_end = '-'.$date_utils->olzDate('WW (t.m.)', $datum_end);
            } elseif (($datum_end != '0000-00-00' and $datum_end !== null) and $diff_end > 0) {
                $datum_end = '-'.$date_utils->olzDate('WW', $datum_end);
            } else {
                $datum_end = '';
            }
            // $datum_end = ($datum_end!='0000-00-00' AND $datum_end!=$datum_tmp) ? '-'.$date_utils->olzDate('W',$datum_end) : '' ;
            $datum = $date_utils->olzDate('WW', $datum_tmp).$datum_end.":";
        } elseif ($diff < (14.95 - $wotag)) {
            $case_tmp = 3;
            $datum_end = (($datum_end != '0000-00-00' and $datum_end !== null) and $datum_end != $datum_tmp) ? '-'.$date_utils->olzDate('t.m.(W)', $datum_end) : '';
            $datum = $date_utils->olzDate('W, t.m.', $datum_tmp).$datum_end;
        } elseif ($flag == 1) {
            $case_tmp = 4;
            $datum = $date_utils->olzDate('t.m.', $datum_tmp);
        } else {
            $case_tmp = 5;
            $datum = $date_utils->olzDate('t.m.', $datum_tmp);
        }
        if ($case_tmp < 4) {
            $flag = 0;
        }
        // if ($case!=$case_tmp and 0<strlen($sections[$case_tmp-1])) echo "<div class='tablebar'>".str_replace("[x]",$diff,$sections[$case_tmp-1])."</div>";
        if ($case != $case_tmp and strlen($sections[$case_tmp - 1]) > 0) {
            echo "<h2{$class_heute} style='margin-top:15px;'>".str_replace("[x]", $diff, $sections[$case_tmp - 1])."</h2>";
        }
        $case = $case_tmp;
        // ENDE TEST

        $titel = strip_tags(str_replace("<br>", ", ", $row['titel']));
        $text = strip_tags(str_replace("<br>", ", ", $row['text']));
        $id_tmp = $row['id'];
        $datum_tmp = $datum;
        if ($titel == "") {
            $titel = $text;
        } elseif ($text != "") {
            $titel = $titel." - ".$text;
        }
        $mehr = "";
        if ($textlaenge_def < strlen($datum_tmp) + strlen($titel)) {
            $titel = mb_substr($titel, 0, $textlaenge_def - strlen($datum_tmp));
            $titel = mb_substr($titel, 0, mb_strrpos($titel, " "));
            $mehr = " ...";
        }

        if ($time < 86400 * 3) {
            if ($pulse != "") {
                $pulse .= ",";
            }
            $pulse .= "\"terminticker".$id_tmp."\"";
        }

        echo "<p{$class_heute}><a href='{$code_href}termine/".$id_tmp."' id='terminticker".$id_tmp."' onmouseover='olz.mousein(\"terminticker".$id_tmp."\")' onmouseout='olz.mouseout(\"terminticker".$id_tmp."\")'><span style='font-weight:bold;margin-right:6px;'>".$datum_tmp."</span> ".$titel.$mehr."</a></p>";
    }
    echo "</div>";
}
