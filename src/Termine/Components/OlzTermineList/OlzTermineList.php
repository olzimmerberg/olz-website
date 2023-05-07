<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

namespace Olz\Termine\Components\OlzTermineList;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Schema\OlzEventData\OlzEventData;
use Olz\Termine\Components\OlzTermineFilter\OlzTermineFilter;
use Olz\Termine\Components\OlzTermineSidebar\OlzTermineSidebar;
use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzTermineList extends OlzComponent {
    public function getHtml($args = []): string {
        global $db_table, $monate, $heute;

        require_once __DIR__.'/../../../../_/config/date.php';
        require_once __DIR__.'/../../../../_/library/wgs84_ch1903/wgs84_ch1903.php';

        $db = $this->dbUtils()->getDb();
        $date_utils = $this->dateUtils();
        $file_utils = FileUtils::fromEnv();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $validated_get_params = $http_utils->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
            'buttontermine' => new FieldTypes\StringField(['allow_null' => true]),
        ], $_GET);

        $current_filter = json_decode($_GET['filter'] ?? '{}', true);
        $termine_utils = TermineFilterUtils::fromEnv();

        if (!$termine_utils->isValidFilter($current_filter)) {
            $enc_json_filter = urlencode(json_encode($termine_utils->getDefaultFilter()));
            $http_utils->redirect("termine.php?filter={$enc_json_filter}", 308);
        }

        $termine_list_title = $termine_utils->getTitleFromFilter($current_filter);
        $is_not_archived = $termine_utils->isFilterNotArchived($current_filter);
        $allow_robots = $is_not_archived;

        $out = '';

        $out .= OlzHeader::render([
            'title' => $termine_list_title,
            'description' => "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.",
            'norobots' => !$allow_robots,
        ]);

        $enc_current_filter = urlencode(json_encode($current_filter));

        $out .= "
        <div class='content-right optional'>
        <div>";
        $out .= OlzTermineSidebar::render();
        $out .= "</div>
        </div>
        <div class='content-middle'>
        <form name='Formularl' method='post' action='termine.php?filter={$enc_current_filter}#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";

        // -------------------------------------------------------------
        // ZUGRIFF
        if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
            $zugriff = "1";
        } else {
            $zugriff = "0";
        }
        $button_name = 'button'.$db_table;
        if (isset($_GET[$button_name])) {
            $_POST[$button_name] = $_GET[$button_name];
        }
        if (isset($_POST[$button_name])) {
            $_SESSION['edit']['db_table'] = $db_table;
        }

        // -------------------------------------------------------------
        // USERVARIABLEN PRÜFEN
        if (isset($_GET['id']) and is_ganzzahl($_GET['id'])) {
            $id = $_GET['id'] ?? null;
            $_SESSION[$db_table."id_"] = $id;
        } else {
            $id = $_SESSION[$db_table."id_"] ?? null;
        }

        // -------------------------------------------------------------
        // DATENSATZ EDITIEREN
        if ($zugriff) {
            $functions = ['neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'start' => 'start',
                'duplicate' => 'duplicate',
                'undo' => 'undo', ];

            $id = $_SESSION[$db_table.'id_'] ?? null;
            $jahr = $_SESSION[$db_table.'jahr_'] ?? null;
            $monat = $_SESSION[$db_table.'monat_'] ?? null;
            $monatzahl = (array_search($monat, $monate) % 12) + 1;
            $periode = $jahr."-".substr("00".$monatzahl, strlen($monatzahl))."-01";
        } else {
            $functions = [];
        }

        $function = array_search($_POST[$button_name] ?? null, $functions);
        if ($function != "") {
            ob_start();
            include __DIR__.'/../../../../_/admin/admin_db.php';
            $out .= ob_get_contents();
            ob_end_clean();
        }
        if (!isset($do)) {
            $do = null;
        }
        if ($_SESSION['edit']['table'] ?? null != null) {
            $db_edit = "1";
        } else {
            $db_edit = "0";
        }

        // -------------------------------------------------------------
        //  Wiederkehrendes Datum speichern
        // -------------------------------------------------------------
        if ($function == 'save' and ($_SESSION[$db_table]['repeat'] ?? null) == 'repeat') {
            if ($termin_[0] !== $_SESSION[$db_table."datum"]) { // UPDATE Startdatum
                $sql = "DELETE FROM {$db_table} WHERE id='".$_SESSION[$db_table."id"]."'";
                $result = $db->query($sql);
            }
            foreach ($termin_ as $tmp_termin) {
                if ($tmp_termin == $_SESSION[$db_table."datum"]) { // UPDATE Startdatum
                    $sql = "UPDATE {$db_table} SET datum_end=NULL WHERE id='".$_SESSION[$db_table."id"]."'";
                    $result = $db->query($sql);
                } else { // TERMIN speichern
                    $sql_tmp = [];
                    foreach ($db_felder as $tmp_feld) {
                        if ($tmp_feld[0] == 'datum') {
                            array_push($sql_tmp, $tmp_feld[0]." = '".date("Y-m-d", $tmp_termin)."'");
                        } elseif ($tmp_feld[0] == 'datum_end') {
                            array_push($sql_tmp, $tmp_feld[0]." = NULL");
                        } elseif ($tmp_feld[0] !== 'id') {
                            $var = $tmp_feld[0];
                            array_push($sql_tmp, $var." = '".$_SESSION[$db_table.$var]."'");
                        }
                    }
                    $sql = "INSERT {$db_table} SET ".implode(",", $sql_tmp);
                    $result = $db->query($sql);
                }
                unset($_SESSION[$db_table]);
                $counter = $counter + 1;
            }
        }

        if (($function == 'neu' or $function == 'edit') and $_SESSION['edit']['modus'] == 'neuedit') {
            $checked = ($_SESSION[$db_table]['repeat'] ?? null == 'repeat') ? ' checked' : '';
            $intervall = (isset($_SESSION[$db_table]['intervall'])) ? $_SESSION[$db_table]['intervall'] : '7';
            $_SESSION[$db_table]['termin_'] = $termin_ ?? null;
            $out .= "<table class='liste'>";
            $out .= "<tr><td style='width:20%;padding-top:4px;'><b>Termin wiederholen</b></td><td style='width:80%'><p><input type='checkbox' name='modus_termin' value='repeat'{$checked}><span style='margin-left:20px;'>(Achtung: Für das Wiederholen von Terminen muss ein Enddatum angegeben werden)</span></p></td></tr>";
            $out .= "<tr><td style='width:20%;padding-top:4px;'><b>Intervall (Tage)</b></td><td style='width:80%'><input type='text' name='intervall_termin' value='{$intervall}'></td></tr></table>";
        }

        // -------------------------------------------------------------
        // MENÜ
        if ($zugriff) {
            $out .= "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")." <span class='linkint'><a href='termine_tools.php'>Termine-Tools</a></span></div>";
        }

        $out .= OlzTermineFilter::render();
        $out .= "<h1>{$termine_list_title}</h1>";

        // -------------------------------------------------------------
        //  VORSCHAU - LISTE
        $filter_where = $termine_utils->getSqlFromFilter($current_filter);
        $sql_where = <<<ZZZZZZZZZZ
        (
            (t.datum_off>='{$date_utils->getIsoToday()}')
            OR (t.datum_off='0000-00-00')
            OR t.datum_off IS NULL
        )
        AND (t.on_off = '1')
        AND {$filter_where}
        ZZZZZZZZZZ;

        if (($do ?? null) == 'vorschau') {
            $sql_where = "(t.id ='{$id}')";
        } // Proforma-Abfrage

        // $sql = "SELECT * FROM ".$db_table." LEFT JOIN solv_termine ON solv_uid=unique_id ".$sql;
        $sql = <<<ZZZZZZZZZZ
        (
            SELECT
                t.datum as datum,
                t.datum_end as datum_end,
                t.zeit as zeit,
                t.zeit_end as zeit_end,
                t.titel as titel,
                t.text as text,
                t.link as link,
                t.solv_event_link as solv_event_link,
                t.id as id,
                t.typ as typ,
                t.on_off as on_off,
                t.newsletter as newsletter,
                t.xkoord as xkoord,
                t.ykoord as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid
            FROM termine t
            WHERE {$sql_where}
        ) UNION ALL (
            SELECT
                se.deadline as datum,
                NULL as datum_end,
                '00:00:00' as zeit,
                NULL as zeit_end,
                CONCAT('Meldeschluss für ', t.titel) as titel,
                '' as text,
                '' as link,
                '' as solv_event_link,
                CONCAT('SOLV', se.solv_uid) as id,
                'meldeschluss' as typ,
                t.on_off as on_off,
                NULL as newsletter,
                NULL as xkoord,
                NULL as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid
            FROM termine t JOIN solv_events se ON (t.solv_uid = se.solv_uid)
            WHERE se.deadline IS NOT NULL AND {$sql_where}
        ) UNION ALL (
            SELECT
                DATE(t.deadline) as datum,
                NULL as datum_end,
                TIME(t.deadline) as zeit,
                NULL as zeit_end,
                CONCAT('Meldeschluss für ', t.titel) as titel,
                '' as text,
                '' as link,
                '' as solv_event_link,
                CONCAT('DEADLINE', t.id) as id,
                'meldeschluss' as typ,
                t.on_off as on_off,
                NULL as newsletter,
                NULL as xkoord,
                NULL as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid
            FROM termine t
            WHERE t.deadline IS NOT NULL AND {$sql_where}
        )
        ORDER BY datum ASC
        ZZZZZZZZZZ;

        // if ($zugriff) {
        //     $out .= $sql;
        // }
        // DB-ABFRAGE
        $result = $db->query($sql);

        $out .= "<table class='liste'>";
        $id_spalte = " id='Spalte1'";
        while ($row = mysqli_fetch_array($result)) {
            if (($do ?? null) == 'vorschau') {
                $row = $vorschau;
            }
            $datum = $row['datum'];
            $datum_end = $row['datum_end'];
            $zeit = $row['zeit'];
            $zeit_end = $row['zeit_end'];
            $titel = $row['titel'];
            $text = $row['text'];
            $text = \olz_br(olz_mask_email($text, "", ""));
            $link = $row['link'] ?? '';
            $event_link = $row['solv_event_link'] ?? '';
            $id = $row['id'];
            $typ = $row['typ'];
            $on_off = $row['on_off'];
            $newsletter = $row['newsletter'];
            $xkoord = $row['xkoord'];
            $ykoord = $row['ykoord'];
            $go2ol = $row['go2ol'];
            $solv_uid = $row['solv_uid'];
            $row_solv = false;
            if ($solv_uid) {
                $sane_solv_uid = intval($solv_uid);
                $result_solv = $db->query("SELECT * FROM solv_events WHERE solv_uid='{$sane_solv_uid}'");
                $row_solv = $result_solv->fetch_assoc();
            }
            $has_olz_location = ($xkoord > 0 && $ykoord > 0);
            $has_solv_location = (
                $typ != 'meldeschluss'
                && $row_solv
                && $row_solv['coord_x'] > 0
                && $row_solv['coord_y'] > 0
            );
            $lat = $has_olz_location ? CHtoWGSlat($xkoord, $ykoord) :
                ($has_solv_location ? CHtoWGSlat($row_solv['coord_x'], $row_solv['coord_y']) : null);
            $lng = $has_olz_location ? CHtoWGSlng($xkoord, $ykoord) :
                ($has_solv_location ? CHtoWGSlng($row_solv['coord_x'], $row_solv['coord_y']) : null);
            $location_name = $has_olz_location ? null :
                ($has_solv_location ? $row_solv['location'] : null);
            $has_location = $has_olz_location || $has_solv_location;

            // Dateicode einfügen
            preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $link, $matches);
            for ($i = 0; $i < count($matches[0]); $i++) {
                $tmptext = $matches[4][$i];
                if (mb_strlen($tmptext) < 1) {
                    $tmptext = "Datei ".$matches[1][$i];
                }
                $tmp_html = $file_utils->olzFile($db_table, $id, intval($matches[1][$i]), $tmptext);
                $link = str_replace($matches[0][$i], $tmp_html, $link);
            }
            // Karte zeigen
            if ($has_olz_location && $datum >= $heute) {
                $lv95_e = $xkoord + 2000000;
                $lv95_n = $ykoord + 1000000;
                $swisstopo_url = "https://map.geo.admin.ch/?lang=de&bgLayer=ch.swisstopo.pixelkarte-farbe&layers=ch.bav.haltestellen-oev&E={$lv95_e}&N={$lv95_n}&zoom=8&crosshair=marker";
                $link .= "<div id='map_{$id}'><a href='{$swisstopo_url}' target='_blank' onclick=\"olz.toggleMap('{$id}',{$xkoord},{$ykoord});return false;\" class='linkmap'>Karte zeigen</a></div>";
            }
            // SOLV-Karte zeigen
            elseif ($has_solv_location && $datum >= $heute) {
                $lv95_e = $row_solv["coord_x"] + 2000000;
                $lv95_n = $row_solv["coord_y"] + 1000000;
                $swisstopo_url = "https://map.geo.admin.ch/?lang=de&bgLayer=ch.swisstopo.pixelkarte-farbe&layers=ch.bav.haltestellen-oev&E={$lv95_e}&N={$lv95_n}&zoom=8&crosshair=marker";
                $link .= "<div id='map_{$id}'><a href='{$swisstopo_url}' target='_blank' onclick=\"olz.toggleMap('{$id}',".$row_solv["coord_x"].",".$row_solv["coord_y"].");return false;\" class='linkmap'>Karte zeigen</a></div>";
            }
            // Anmeldungs-Link zeigen
            if ($go2ol > "" and $datum >= $heute) {
                $link .= "<div class='linkext'><a href='https://go2ol.ch/".$go2ol."/' target='_blank'>Anmeldung</a></div>\n";
            } elseif ($row_solv && $row_solv['entryportal'] == 1 and $datum >= $heute) {
                $link .= "<div class='linkext'><a href='https://www.go2ol.ch/index.asp?lang=de' target='_blank'>Anmeldung</a></div>\n";
            } elseif ($row_solv && $row_solv['entryportal'] == 2 and $datum >= $heute) {
                $link .= "<div class='linkext'><a href='https://entry.picoevents.ch/' target='_blank'>Anmeldung</a></div>\n";
            }
            if (strpos($link, 'Ausschreibung') == 0 and ($row['solv_event_link'] ?? '') > "") {
                $class = strpos($row['solv_event_link'], ".pdf") > 0 ? 'linkpdf' : 'linkext';
                $link .= "<div class='{$class}'><a href='".$row['solv_event_link']."' target='_blank'>Ausschreibung</a></div>";
            }
            if ($typ != 'meldeschluss' && $row_solv && isset($row_solv['deadline']) && $row_solv['deadline'] && $row_solv['deadline'] != "0000-00-00") {
                $text .= ($text == "" ? "" : "<br />")."Meldeschluss: ".$date_utils->olzDate("t. MM ", $row_solv['deadline']);
            }
            // Ranglisten-Link zeigen
            if ($solv_uid > 0 and $datum <= $heute and strpos($link, "Rangliste") == "" and strpos($link, "Resultat") == "" and strpos($typ, "ol") >= 0) {
                $link .= "<div><a href='http://www.o-l.ch/cgi-bin/results?unique_id=".$solv_uid."&club=zimmerberg' target='_blank' class='linkol'>Rangliste</a></div>\n";
            }
            // SOLV-Ausschreibungs-Link zeigen
            if ($row_solv && ($row_solv["event_link"] ?? false) and strpos($link, "Ausschreibung") == "" and strpos($typ, "ol") >= 0 and $datum <= $heute) {
                $ispdf = preg_match("/\\.pdf$/", $row_solv["event_link"]);
                $link .= "<div><a href='".$row_solv["event_link"]."' target='_blank' class='link".($ispdf ? "pdf" : "ext")."'>Ausschreibung</a></div>\n";
            }

            if ($typ != 'meldeschluss') {
                $titel = "<a href='termine.php?filter={$enc_current_filter}&id={$id}'>{$titel}</a>";
            }
            // SOLV-Übersicht-Link zeigen
            if ($row_solv) {
                $titel .= "<a href='https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row_solv['solv_uid']."' target='_blank' class='linkol' style='margin-left: 20px; font-weight: normal;'>O-L.ch</a>\n";
            }

            if ($datum_end == "0000-00-00" || !$datum_end) {
                $datum_end = $datum;
            }
            if ($titel > "") {
                $text = "<b>".$titel."</b><br>".$text;
            }
            if ($link == "") {
                $link = "&nbsp;";
            } else {
                $link = str_replace("&", "&amp;", str_replace("&amp;", "&", $link));
            }
            $link = str_replace("www.solv.ch", "www.o-l.ch", $link);

            if ($zugriff && $typ != 'meldeschluss' && (($do ?? null) != 'vorschau')) {
                // Berbeiten-/Duplizieren-Button
                $edit_admin = "<a href='termine.php?filter={$enc_current_filter}&id={$id}&{$button_name}=start' class='linkedit' title='Termin bearbeiten'>&nbsp;</a><a href='termine.php?filter={$enc_current_filter}&id={$id}&{$button_name}=duplicate' class='linkedit2 linkduplicate' title='Termin duplizieren'>&nbsp;</a>";
            } else {
                $edit_admin = "";
            }

            // Tagesanlass
            if (($datum_end == $datum) or ($datum_end == "0000-00-00") or !$datum_end) {
                $datum_tmp = $date_utils->olzDate("t. MM ", $datum).$date_utils->olzDate(" (W)", $datum);
                if ($zeit && $zeit != "00:00:00") {
                    $datum_tmp .= "<br />".date("H:i", strtotime($zeit));
                    if ($zeit_end && $zeit_end != "00:00:00") {
                        $datum_tmp .= " &ndash; ".date("H:i", strtotime($zeit_end));
                    }
                }
            }
            // Mehrtägig innerhalb Monat
            elseif ($date_utils->olzDate("m", $datum) == $date_utils->olzDate("m", $datum_end)) {
                $datum_tmp = $date_utils->olzDate("t.-", $datum).$date_utils->olzDate("t. ", $datum_end).$date_utils->olzDate("MM", $datum).$date_utils->olzDate(" (W-", $datum).$date_utils->olzDate("W)", $datum_end);
            }
            // Mehrtägig monatsübergreifend
            else {
                $datum_tmp = $date_utils->olzDate("t.m.-", $datum).$date_utils->olzDate("t.m. ", $datum_end).$date_utils->olzDate("jjjj", $datum).$date_utils->olzDate(" (W-", $datum).$date_utils->olzDate("W)", $datum_end);
            }
            if ($uid ?? $row['id'] == null) {
                $class = " class='selected'";
            } elseif ($datum_end < $heute) {
                $class = " class='passe'";
            } elseif ($on_off == 0) {
                $class = " class='off'";
            } else {
                $class = "";
            }

            // HTML-Ausgabe
            if ((($_SESSION['termin_filter'] ?? null) == "resultat" and (strpos($link, "Rangliste") > "" or strpos($link, "Resultat") > "")) or (($_SESSION['termin_filter'] ?? null) != "resultat")) {
                $out .= olz_monate($datum);
                $out .= OlzEventData::render([
                    'name' => $row['titel'],
                    'start_date' => $date_utils->olzDate('jjjj-mm-tt', $datum),
                    'end_date' => $datum_end ? $date_utils->olzDate('jjjj-mm-tt', $datum_end) : null,
                    'location' => $has_location ? [
                        'lat' => $lat,
                        'lng' => $lng,
                        'name' => $location_name,
                    ] : null,
                ]);
                $out .= "<tr".$class.">\n\t<td style='width:25%;'><div style='position:absolute; margin-top:-50px;' id='id".$id."'>&nbsp;</div>".$edit_admin.$datum_tmp."</td><td style='width:55%;'{$id_spalte}>".$text."<div id='map{$id}' style='display:none;width:100%;text-align:left;margin:0px;padding-top:4px;clear:both;'></div></td><td style='width:20%;'>".$link."</td>\n</tr>\n";
            }
            $id_spalte = "";
        }
        $out .= "</table>";
        $out .= "</form>
        </div>";

        $out .= OlzFooter::render();

        // -------------------------------------------------------------
        //  Wiederkehrendes Datum anzeigen
        // -------------------------------------------------------------
        if (($do ?? null) == 'vorschau' and ($modus_termin ?? null) == 'repeat') {
            $_SESSION[$db_table]['repeat'] = $modus_termin;
            $_SESSION[$db_table]['intervall'] = $intervall_termin;
            if ($vorschau['datum_end'] > '' and $vorschau['datum_end'] !== '0000-00-00') {
                $var1 = explode('-', $vorschau['datum_end']);
                $var1 = mktime(2, 0, 0, $var1[1], $var1[2], $var1[0]);
                $var2 = explode('-', $vorschau['datum']);
                $var2 = mktime(2, 0, 0, $var2[1], $var2[2], $var2[0]);
                $count_termine = round(round(($var1 - $var2) / 86400) / $intervall_termin);
                for ($x = 0; $x <= $count_termine; $x++) {
                    $tmp_termin = ($var2 + $x * $intervall_termin * 86400);
                    if (isset($_SESSION[$db_table]['termin_'])) {
                        $checked = (in_array($tmp_termin, $_SESSION[$db_table]['termin_'])) ? " checked" : "";
                    } else {
                        $checked = " checked";
                    }
                    $out .= "<input type='checkbox' name='termin_[]' value='".$tmp_termin."'{$checked}>".date('d.m.Y', $var2 + $x * $intervall_termin * 86400)."<br>";
                }
            }
        }

        return $out;
    }
}
