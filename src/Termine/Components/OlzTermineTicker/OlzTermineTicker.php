<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

namespace Olz\Termine\Components\OlzTermineTicker;

use Olz\Components\Common\OlzComponent;
use Olz\Utils\AbstractDateUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

class OlzTermineTicker extends OlzComponent {
    public function getHtml($args = []): string {
        $date_utils = AbstractDateUtils::fromEnv();
        $code_href = EnvUtils::fromEnv()->getCodeHref();
        $db = DbUtils::fromEnv()->getDb();
        $out = '';

        $textlaenge_def = isset($args["eintrag_laenge"]) ? intval($args["eintrag_laenge"]) : 80;
        $listenlaenge = isset($args["eintrag_anzahl"]) ? intval($args["eintrag_anzahl"]) : 8;
        $sql_where = isset($args["sql_where"]) ? $args["sql_where"] : "";
        $title = isset($args["titel"]) ? $args["titel"] : "Termine";
        $heute_highlight = isset($args["heute_highlight"]) ? $args["heute_highlight"] : true;
        // Konstanten
        $db_table = "termine";
        $heute = $date_utils->getCurrentDateInFormat("Y-m-d");
        $out .= "<div class='layout'>";
        $out .= "<h4 class='tablebar'>".$title."</h4>";
        // Tabelle auslesen
        $sql = "SELECT * FROM {$db_table} WHERE ((start_date >= '{$heute}') OR (end_date >= '{$heute}')) AND (on_off = 1)".$sql_where." ORDER BY start_date ASC LIMIT {$listenlaenge}";
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
            $start_date = $row['start_date'];
            $end_date = $row['end_date'];
            $timestamp_start = $start_date ? strtotime($start_date) : 0;
            $timestamp_heute = $heute ? strtotime($heute) : 0;
            $timestamp_end = $end_date ? strtotime($end_date) : 0;
            $diff_start = ($timestamp_start - $timestamp_heute) / 86400;
            $diff_end = ($timestamp_end - $timestamp_start) / 86400;
            $time = $diff_start * 86400;
            $class_heute = "";
            if ($diff_start < 0.95) { // Sommerzeitwechsel: (strtotime('2014-03-31')-strtotime('2014-03-30'))/86400 = 0.958...
                $case_tmp = 1;
                if (($end_date != '0000-00-00' and $end_date !== null) and $diff_end > 6) {
                    $end_date = '(bis '.$date_utils->olzDate('WW t.m.', $end_date).')';
                } elseif (($end_date != '0000-00-00' and $end_date !== null) and $diff_end > 0) {
                    $end_date = '(bis '.$date_utils->olzDate('WW', $end_date).')';
                } else {
                    $end_date = '';
                }
                $datum = $end_date;
                if ($heute_highlight) {
                    $class_heute = " class='heute'";
                }
            } elseif ($diff_start < (7.95 - $wotag)) {
                $case_tmp = 2;
                if (($end_date != '0000-00-00' and $end_date !== null) and $diff_end > 6) {
                    $end_date = '-'.$date_utils->olzDate('WW (t.m.)', $end_date);
                } elseif (($end_date != '0000-00-00' and $end_date !== null) and $diff_end > 0) {
                    $end_date = '-'.$date_utils->olzDate('WW', $end_date);
                } else {
                    $end_date = '';
                }
                // $end_date = ($end_date!='0000-00-00' AND $end_date!=$start_date) ? '-'.$date_utils->olzDate('W',$end_date) : '' ;
                $datum = $date_utils->olzDate('WW', $start_date).$end_date.":";
            } elseif ($diff_start < (14.95 - $wotag)) {
                $case_tmp = 3;
                $end_date = (($end_date != '0000-00-00' and $end_date !== null) and $end_date != $start_date) ? '-'.$date_utils->olzDate('t.m.(W)', $end_date) : '';
                $datum = $date_utils->olzDate('W, t.m.', $start_date).$end_date;
            } elseif ($flag == 1) {
                $case_tmp = 4;
                $datum = $date_utils->olzDate('t.m.', $start_date);
            } else {
                $case_tmp = 5;
                $datum = $date_utils->olzDate('t.m.', $start_date);
            }
            if ($case_tmp < 4) {
                $flag = 0;
            }
            // if ($case!=$case_tmp and 0<strlen($sections[$case_tmp-1])) $out .= "<div class='tablebar'>".str_replace("[x]",$diff_start,$sections[$case_tmp-1])."</div>";
            if ($case != $case_tmp and strlen($sections[$case_tmp - 1]) > 0) {
                $out .= "<h2{$class_heute} style='margin-top:15px;'>".str_replace("[x]", $diff_start, $sections[$case_tmp - 1])."</h2>";
            }
            $case = $case_tmp;
            // ENDE TEST

            $title = strip_tags(str_replace("<br>", ", ", $row['title']));
            $text = strip_tags(str_replace("<br>", ", ", $row['text']));
            $id_tmp = $row['id'];
            $start_date = $datum;
            if ($title == "") {
                $title = $text;
            } elseif ($text != "") {
                $title = $title." - ".$text;
            }
            $mehr = "";
            if ($textlaenge_def < strlen($start_date) + strlen($title)) {
                $title = mb_substr($title, 0, $textlaenge_def - strlen($start_date));
                $title = mb_substr($title, 0, mb_strrpos($title, " "));
                $mehr = " ...";
            }

            if ($time < 86400 * 3) {
                if ($pulse != "") {
                    $pulse .= ",";
                }
                $pulse .= "\"terminticker".$id_tmp."\"";
            }

            $out .= "<p{$class_heute}><a href='{$code_href}termine/".$id_tmp."' id='terminticker".$id_tmp."' onmouseover='olz.olzTermineTickerMouseIn(\"terminticker".$id_tmp."\")' onmouseout='olz.olzTermineTickerMouseOut(\"terminticker".$id_tmp."\")'><span style='font-weight:bold;margin-right:6px;'>".$start_date."</span> ".$title.$mehr."</a></p>";
        }
        $out .= "</div>";
        return $out;
    }
}
