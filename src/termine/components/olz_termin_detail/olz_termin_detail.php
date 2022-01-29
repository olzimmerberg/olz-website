<?php

function olz_termin_detail($args = []): string {
    global $db, $_DATE;

    require_once __DIR__.'/../../../image_tools.php';

    $db_table = 'termine';
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
        $text = olz_br(olz_mask_email($text, "", ""));
        $link = $row['link'] ?? '';
        $event_link = $row['solv_event_link'];
        $id = $row['id'];
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
        $tn = ($zugriff == 1) ? "(".($row['teilnehmer'] ?? '').($solv_uid > 0 ? ";SOLV" : "").") " : "";

        $edit_admin = '';
        if ($can_edit && !$is_preview) {
            $json_id = json_encode(intval($id_tmp));
            $edit_admin = $is_migrated ? <<<ZZZZZZZZZZ
            <button
                id='edit-news-button'
                class='btn btn-primary'
                onclick='return editNewsArticle({$json_id})'
            >
                <img src='icns/edit_16.svg' class='noborder' />
                Bearbeiten
            </button>
            ZZZZZZZZZZ : "<a href='termine.php?id={$id_tmp}&amp;button{$db_table}=start' class='linkedit'>&nbsp;</a>";
        }
        $out .= "<h2>".$edit_admin.$titel."</h2>";

        // Dateicode einfügen
        $link = replace_file_tags($link, $id);

        //Karte zeigen
        if ($xkoord > 0 && $ykoord > 0) {
            $link .= "<div id='map_{$id}'><a href='http://map.search.ch/{$xkoord},{$ykoord}' target='_blank' onclick=\"toggleMap('{$id}',{$xkoord},{$ykoord});return false;\" class='linkmap'>Karte zeigen</a></div>";
        }
        //SOLV-Karte zeigen
        elseif ($typ != 'meldeschluss' && $row_solv && $row_solv["coord_x"] > 0 and $datum >= $heute) {
            $link .= "<div id='map_{$id}'><a href='http://map.search.ch/".$row_solv["coord_x"].",".$row_solv["coord_y"]."' target='_blank' onclick=\"toggleMap('{$id}',".$row_solv["coord_x"].",".$row_solv["coord_y"].");return false;\" class='linkmap'>Karte zeigen</a></div>";
        }
        //Anmeldungs-Link zeigen
        //Manueller Anmeldungs-Link entfernen
        if ($row_solv && ($go2ol > "" or $row_solv['entryportal'] == 1 or $row_solv['entryportal'] == 2)) {
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
        if ($typ != 'meldeschluss' && $row_solv && isset($row_solv['deadline']) && $row_solv['deadline'] && $row_solv['deadline'] != "0000-00-00") {
            $text .= ($text == "" ? "" : "<br />")."Meldeschluss: ".$_DATE->olzDate("t. MM ", $row_solv['deadline']);
        }
        //Ranglisten-Link zeigen
        if ($solv_uid > 0 and $datum <= $heute and strpos($link, "Rangliste") == "" and strpos($link, "Resultat") == "" and strpos($typ, "ol") >= 0) {
            $link .= "<div><a href='http://www.o-l.ch/cgi-bin/results?unique_id=".$solv_uid."&club=zimmerberg' target='_blank' class='linkol'>Rangliste</a></div>\n";
        }
        //SOLV-Ausschreibungs-Link zeigen
        if ($row_solv && ($row_solv["event_link"] ?? false) and strpos($link, "Ausschreibung") == "" and strpos($typ, "ol") >= 0 and $datum <= $heute) {
            $ispdf = preg_match("/\\.pdf$/", $row_solv["event_link"]);
            $link .= "<div><a href='".$row_solv["event_link"]."' target='_blank' class='link".($ispdf ? "pdf" : "ext")."'>Ausschreibung</a></div>\n";
        }

        //SOLV-Übersicht-Link zeigen
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

        if ($datum_anmeldung and ($datum_anmeldung != '0000-00-00') and ($datum_anmeldung != '') and ($zugriff) and ($datum_anm > $heute)) {
            $link = "<div class='linkint'><a href='anmeldung.php?id_anm={$id}'>Online-Anmeldung</a></div>".$link;
        }

        if ($zugriff && $typ != 'meldeschluss' && (($do ?? null) != 'vorschau')) {
            //Berbeiten-/Duplizieren-Button
            $edit_admin = "<a href='termine.php?filter={$enc_current_filter}&id={$id}&{$button_name}=start' class='linkedit' title='Termin bearbeiten'>&nbsp;</a><a href='termine.php?filter={$enc_current_filter}&id={$id}&{$button_name}=duplicate' class='linkedit2 linkduplicate' title='Termin duplizieren'>&nbsp;</a>";
            if ($datum_anmeldung && ($datum_anmeldung != '') and ($datum_anmeldung != '0000-00-00')) {
                $edit_anm = "<a href='anmeldung.php?id_anm={$id}&buttonanm_felder=start' class='linkedit' title='Online-Anmeldung bearbeiten'>&nbsp;</a>";
            } else {
                $edit_anm = "";
            }
        } else {
            $edit_admin = "";
            $edit_anm = "";
        }

        if ($newsletter) {
            $icn_newsletter = "<img src='icns/newsletter_16.svg' class='noborder' style='margin-left:4px;vertical-align:top;' title='Newsletter-Benachrichtigung' alt=''>";
        } else {
            $icn_newsletter = "";
        }

        //Tagesanlass
        if (($datum_end == $datum) or ($datum_end == "0000-00-00") or !$datum_end) {
            $datum_tmp = $_DATE->olzDate("t. MM ", $datum).$_DATE->olzDate(" (W)", $datum);
            if ($zeit != "00:00:00") {
                $datum_tmp .= "<br />".date("H:i", strtotime($zeit));
                if ($zeit_end != "00:00:00") {
                    $datum_tmp .= " &ndash; ".date("H:i", strtotime($zeit_end));
                }
            }
        }
        //Mehrtägig innerhalb Monat
        elseif ($_DATE->olzDate("m", $datum) == $_DATE->olzDate("m", $datum_end)) {
            $datum_tmp = $_DATE->olzDate("t.-", $datum).$_DATE->olzDate("t. ", $datum_end).$_DATE->olzDate("MM", $datum).$_DATE->olzDate(" (W-", $datum).$_DATE->olzDate("W)", $datum_end);
        }
        //Mehrtägig monatsübergreifend
        else {
            $datum_tmp = $_DATE->olzDate("t.m.-", $datum).$_DATE->olzDate("t.m. ", $datum_end).$_DATE->olzDate("jjjj", $datum).$_DATE->olzDate(" (W-", $datum).$_DATE->olzDate("W)", $datum_end);
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
        // if ((($_SESSION['termin_filter'] ?? null) == "resultat" and (strpos($link, "Rangliste") > "" or strpos($link, "Resultat") > "")) or (($_SESSION['termin_filter'] ?? null) != "resultat")) {
        //     echo olz_monate($datum)."<tr".$class.">\n\t<td style='width:25%;'><div style='position:absolute; margin-top:-50px;' id='id".$id."'>&nbsp;</div>".$edit_admin.$edit_anm.$datum_tmp.$icn_newsletter."</td><td style='width:55%;'{$id_spalte}>".$tn.$text."<div id='map{$id}' style='display:none;width:100%;text-align:left;margin:0px;padding-top:4px;clear:both;'></div></td><td style='width:20%;'>".$link."</td>\n</tr>\n";
        // }
        // $id_spalte = "";
    }
    return $out;
}
