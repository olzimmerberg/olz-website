<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

require_once __DIR__.'/config/database.php';
require_once __DIR__.'/config/date.php';
require_once __DIR__.'/utils/TermineUtils.php';

$db_table = "termine";

$ter_filter = [["alle", "Alle Termine"], ["training", "Training"], ["ol", "Wettkämpfe"], ["club", "Vereinsanlässe"]];

//-------------------------------------------------------------
// ZUGRIFF
if ((($_SESSION['auth'] ?? null) == 'all') or (in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? '')))) {
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

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($_GET['id']) and is_ganzzahl($_GET['id'])) {
    $id = $_GET['id'] ?? null;
    $_SESSION[$db_table."id_"] = $id;
} else {
    $id = $_SESSION[$db_table."id_"] ?? null;
}

//-------------------------------------------------------------
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
    include 'admin/admin_db.php';
}
if (!isset($do)) {
    $do = null;
}
if ($_SESSION['edit']['table'] ?? null != null) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}

//-------------------------------------------------------------
//  Wiederkehrendes Datum speichern
//-------------------------------------------------------------
if ($function == 'save' and $_SESSION[$db_table]['repeat'] == 'repeat') {
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
    echo "<table class='liste'>";
    echo "<tr><td style='width:20%;padding-top:4px;'><b>Termin wiederholen</b></td><td style='width:80%'><p><input type='checkbox' name='modus_termin' value='repeat'{$checked}><span style='margin-left:20px;'>(Achtung: Für das Wiederholen von Terminen muss ein Enddatum angegeben werden)</span></p></td></tr>";
    echo "<tr><td style='width:20%;padding-top:4px;'><b>Intervall (Tage)</b></td><td style='width:80%'><input type='text' name='intervall_termin' value='{$intervall}'></td></tr></table>";
}

//-------------------------------------------------------------
// MENÜ
if ($zugriff) {
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")." <span class='linkint'><a href='termine_tools.php'>Termine-Tools</a></span></div>";
}

echo "<div style='padding:4px 3px 10px 3px;'><b>Termin-Typ: </b>";
$type_options = $termine_utils->getUiTypeFilterOptions($current_filter);
echo implode(" | ", array_map(function ($option) {
    $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
    $enc_json_filter = urlencode(json_encode($option['new_filter']));
    $name = $option['name'];
    $ident = $option['ident'];
    return "<a href='termine.php?filter={$enc_json_filter}' id='filter-type-{$ident}'{$selected}>{$name}</a>";
}, $type_options));
echo "<br /><b>Datum: </b>";
$date_range_options = $termine_utils->getUiDateRangeFilterOptions($current_filter);
echo implode(" | ", array_map(function ($option) {
    $selected = $option['selected'] ? " style='text-decoration:underline;'" : "";
    $enc_json_filter = urlencode(json_encode($option['new_filter']));
    $name = $option['name'];
    $ident = $option['ident'];
    return "<a href='termine.php?filter={$enc_json_filter}' id='filter-date-{$ident}'{$selected}>{$name}</a>";
}, $date_range_options));
echo "</div>";

//-------------------------------------------------------------
//  VORSCHAU - LISTE
$filter_where = $termine_utils->getSqlFromFilter($current_filter);
$sql_where = <<<ZZZZZZZZZZ
(
    (t.datum_off>='{$_DATE->getIsoToday()}')
    OR (t.datum_off='0000-00-00')
    OR t.datum_off IS NULL
)
AND (t.on_off = '1')
AND {$filter_where}
ZZZZZZZZZZ;

if (($do ?? null) == 'vorschau') {
    $sql_where = "(t.id ='{$id}')";
} // Proforma-Abfrage

//$sql = "SELECT * FROM ".$db_table." LEFT JOIN solv_termine ON solv_uid=unique_id ".$sql;
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
        t.datum_anmeldung as datum_anmeldung,
        t.xkoord as xkoord,
        t.ykoord as ykoord,
        t.go2ol as go2ol,
        t.solv_uid as solv_uid
    FROM termine t
    WHERE {$sql_where}
) UNION ALL (
    SELECT
        se.deadline as datum,
        se.deadline as datum_end,
        '00:00:00' as zeit,
        '00:00:00' as zeit_end,
        CONCAT('Meldeschluss für ', t.titel) as titel,
        '' as text,
        '' as link,
        '' as solv_event_link,
        CONCAT('SOLV', se.solv_uid) as id,
        'meldeschluss' as typ,
        t.on_off as on_off,
        NULL as newsletter,
        NULL as datum_anmeldung,
        NULL as xkoord,
        NULL as ykoord,
        t.go2ol as go2ol,
        t.solv_uid as solv_uid
    FROM termine t JOIN solv_events se ON (t.solv_uid = se.solv_uid)
    WHERE se.deadline IS NOT NULL AND {$sql_where}
)
ORDER BY datum ASC
ZZZZZZZZZZ;

// if ($zugriff) {
//     echo $sql;
// }
// DB-ABFRAGE
$result = $db->query($sql);

echo "<table class='liste'>";
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
    $text = olz_br(olz_mask_email($text, "", ""));
    $link = $row['link'];
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
    $tn = ($zugriff == 1) ? "(".$row['teilnehmer'].($solv_uid > 0 ? ";SOLV" : "").") " : "";

    // Dateicode einfügen
    preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $link, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        $tmptext = $matches[4][$i];
        if (mb_strlen($tmptext) < 1) {
            $tmptext = "Datei ".$matches[1][$i];
        }
        $tmp_html = olz_file($db_table, $id, intval($matches[1][$i]), $tmptext);
        $link = str_replace($matches[0][$i], $tmp_html, $link);
    }
    //Karte zeigen
    if ($xkoord > 0 and $datum >= $heute) {
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
    if (strpos($row['link'], 'Ausschreibung') == 0 and $row['solv_event_link'] > "") {
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
    if ($row_solv && $row_solv["event_link"] and strpos($link, "Ausschreibung") == "" and strpos($typ, "ol") >= 0 and $datum <= $heute) {
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
    if ((($_SESSION['termin_filter'] ?? null) == "resultat" and (strpos($link, "Rangliste") > "" or strpos($link, "Resultat") > "")) or (($_SESSION['termin_filter'] ?? null) != "resultat")) {
        echo olz_monate($datum)."<tr".$class.">\n\t<td style='width:25%;'><div style='position:absolute; margin-top:-50px;' id='id".$id."'>&nbsp;</div>".$edit_admin.$edit_anm.$datum_tmp.$icn_newsletter."</td><td style='width:55%;'{$id_spalte}>".$tn.$text."<div id='map{$id}' style='display:none;width:100%;text-align:left;margin:0px;padding-top:4px;clear:both;'></div></td><td style='width:20%;'>".$link."</td>\n</tr>\n";
    }
    $id_spalte = "";
}
echo "</table>";

//-------------------------------------------------------------
//  Wiederkehrendes Datum anzeigen
//-------------------------------------------------------------
if (($do ?? null) == 'vorschau' and $modus_termin == 'repeat') {
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
            echo "<input type='checkbox' name='termin_[]' value='".$tmp_termin."'{$checked}>".date('d.m.Y', ($var2 + $x * $intervall_termin * 86400))."<br>";
        }
    }
}
