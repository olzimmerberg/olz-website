<?php

function olz_termin_detail($args = []): string {
    global $db, $_DATE;

    require_once __DIR__.'/../../../image_tools.php';
    require_once __DIR__.'/../../../components/common/olz_location_map/olz_location_map.php';
    require_once __DIR__.'/../../../components/schema/olz_event_data/olz_event_data.php';
    require_once __DIR__.'/../../../library/wgs84_ch1903/wgs84_ch1903.php';

    $db_table = 'termine';
    $button_name = 'button'.$db_table;
    $id = $args['id'];
    $arg_row = $args['row'] ?? null;
    $can_edit = $args['can_edit'] ?? false;
    $is_preview = $args['is_preview'] ?? false;
    $out = "";

    $sql = "SELECT * FROM {$db_table} WHERE (id = '{$id}') ORDER BY datum DESC";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row["datum"]));
    }
    $jahr = $_SESSION[$db_table.'jahr_'];

    $result = $db->query($sql);

    // Aktuelle Nachricht
    while ($row = mysqli_fetch_array($result)) {
        if ($is_preview) {
            $row = $arg_row;
        } else {
            $id = intval($row['id']);
        }

        $datum = $row['datum'];
        $datum_end = $row['datum_end'];
        $zeit = $row['zeit'];
        $zeit_end = $row['zeit_end'];
        $titel = $row['titel'];
        $text = $row['text'];
        $link = $row['link'] ?? '';
        $event_link = $row['solv_event_link'];
        $typ = $row['typ'];
        $on_off = $row['on_off'];
        $newsletter = $row['newsletter'];
        $datum_anmeldung = $row['datum_anmeldung'];
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
            ($has_solv_location ? CHtoWGSlat($row_solv['coord_x'], $row_solv['coord_y']) : null);
        $has_location = $has_olz_location || $has_solv_location;

        $out .= olz_event_data([
            'name' => $titel,
            'start_date' => $_DATE->olzDate('jjjj-mm-tt', $datum),
            'end_date' => $datum_end ? $_DATE->olzDate('jjjj-mm-tt', $datum_end) : null,
            'location' => $has_location ? [
                'lat' => $lat,
                'lng' => $lng,
            ] : null,
        ]);

        $out .= "<div class='olz-termin-detail'>";

        // Karte zeigen
        if ($has_olz_location) {
            $out .= olz_location_map($xkoord, $ykoord, 13, 720, 360);
        // SOLV-Karte zeigen
        } elseif ($has_solv_location) {
            $out .= olz_location_map($row_solv['coord_x'], $row_solv['coord_y'], 13, 720, 360);
        }

        // Date & Title
        $edit_admin = '';
        if ($can_edit && $typ != 'meldeschluss' && !$is_preview) {
            // Berbeiten-/Duplizieren-Button
            $edit_admin = "<a href='termine.php?id={$id}&{$button_name}=start' class='linkedit' title='Termin bearbeiten'>&nbsp;</a><a href='termine.php?id={$id}&{$button_name}=duplicate' class='linkedit2 linkduplicate' title='Termin duplizieren'>&nbsp;</a>";
        }
        if ($datum_end == "0000-00-00" || !$datum_end) {
            $datum_end = $datum;
        }
        if (($datum_end == $datum) or ($datum_end == "0000-00-00") or !$datum_end) {
            $datum_tmp = $_DATE->olzDate("t. MM ", $datum).$_DATE->olzDate(" (W)", $datum);
            // Tagesanlass
            if ($zeit != "00:00:00") {
                $datum_tmp .= " ".date("H:i", strtotime($zeit));
                if ($zeit_end != "00:00:00") {
                    $datum_tmp .= " &ndash; ".date("H:i", strtotime($zeit_end));
                }
            }
        } elseif ($_DATE->olzDate("m", $datum) == $_DATE->olzDate("m", $datum_end)) {
            // Mehrtägig innerhalb Monat
            $datum_tmp = $_DATE->olzDate("t.-", $datum).$_DATE->olzDate("t. ", $datum_end).$_DATE->olzDate("MM", $datum).$_DATE->olzDate(" (W-", $datum).$_DATE->olzDate("W)", $datum_end);
        } else {
            // Mehrtägig monatsübergreifend
            $datum_tmp = $_DATE->olzDate("t.m.-", $datum).$_DATE->olzDate("t.m. ", $datum_end).$_DATE->olzDate("jjjj", $datum).$_DATE->olzDate(" (W-", $datum).$_DATE->olzDate("W)", $datum_end);
        }
        if ($row_solv) {
            // SOLV-Übersicht-Link zeigen
            $titel .= "<a href='https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row_solv['solv_uid']."' target='_blank' class='linkol' style='margin-left: 20px; font-weight: normal;'>O-L.ch</a>\n";
        }
        $out .= "<h2>{$edit_admin} {$datum_tmp}: {$titel}</h2>";

        // Text
        $text = olz_br(olz_mask_email($text, "", ""));
        if ($typ != 'meldeschluss' && $row_solv && isset($row_solv['deadline']) && $row_solv['deadline'] && $row_solv['deadline'] != "0000-00-00") {
            $text .= ($text == "" ? "" : "<br />")."Meldeschluss: ".$_DATE->olzDate("t. MM ", $row_solv['deadline']);
        }
        $out .= "<div>".$text."</div>";

        // Link
        $link = replace_file_tags($link, $id);
        if ($row_solv && ($go2ol > "" or $row_solv['entryportal'] == 1 or $row_solv['entryportal'] == 2)) {
            // Manueller Anmeldungs-Link entfernen
            $var = "Anmeldung";
            $pos1 = strpos($link, $var);
            if ($pos1 > 0) {
                $pos2 = strrpos(substr($link, 0, $pos1), "<");
                $pos3 = strpos(substr($link, $pos1), ">");
                $search = substr($link, $pos2, ($pos1 - $pos2 + $pos3 + strlen($var)));
                $link = str_replace($search, "", $link);
            }
        }
        if ($go2ol > "" and $datum >= $heute) {
            $link .= "<div class='linkext'><a href='https://go2ol.ch/".$go2ol."/' target='_blank'>Anmeldung</a></div>\n";
        } elseif ($row_solv && $row_solv['entryportal'] == 1 and $datum >= $heute) {
            $link .= "<div class='linkext'><a href='https://www.go2ol.ch/index.asp?lang=de' target='_blank'>Anmeldung</a></div>\n";
        } elseif ($row_solv && $row_solv['entryportal'] == 2 and $datum >= $heute) {
            $link .= "<div class='linkext'><a href='https://entry.picoevents.ch/' target='_blank'>Anmeldung</a></div>\n";
        }
        if (strpos($link, 'Ausschreibung') == 0 and $row['solv_event_link'] > "") {
            $class = strpos($row['solv_event_link'], ".pdf") > 0 ? 'linkpdf' : 'linkext';
            $link .= "<div class='{$class}'><a href='".$row['solv_event_link']."' target='_blank'>Ausschreibung</a></div>";
        }
        if ($solv_uid > 0 and $datum <= $heute and strpos($link, "Rangliste") == "" and strpos($link, "Resultat") == "" and strpos($typ, "ol") >= 0) {
            // Ranglisten-Link zeigen
            $link .= "<div><a href='http://www.o-l.ch/cgi-bin/results?unique_id=".$solv_uid."&club=zimmerberg' target='_blank' class='linkol'>Rangliste</a></div>\n";
        }
        if ($row_solv && ($row_solv["event_link"] ?? false) and strpos($link, "Ausschreibung") == "" and strpos($typ, "ol") >= 0 and $datum <= $heute) {
            // SOLV-Ausschreibungs-Link zeigen
            $ispdf = preg_match("/\\.pdf$/", $row_solv["event_link"]);
            $link .= "<div><a href='".$row_solv["event_link"]."' target='_blank' class='link".($ispdf ? "pdf" : "ext")."'>Ausschreibung</a></div>\n";
        }
        if ($link == "") {
            $link = "&nbsp;";
        } else {
            $link = str_replace("&", "&amp;", str_replace("&amp;", "&", $link));
        }
        $link = str_replace("www.solv.ch", "www.o-l.ch", $link);
        $out .= "<div>".$link."</div>";

        $out .= "</div>";
    }
    return $out;
}
