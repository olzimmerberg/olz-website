<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

?>

<!--<script type="text/javascript" src="http://map.search.ch/api/map.js"></script>-->
<script type="text/javascript" src="library/wgs84_ch1903/wgs84_ch1903.js"></script>
<!--<h2>Termine</h2>-->
<script type="text/javascript">
    function map(id,xkoord,ykoord) {
    var div;
    mapid = "map"+id;
    div = document.getElementById(mapid);
    if(div.style.display=="none") {
    div.style.display="";
    breite = document.getElementById('Spalte1').offsetWidth-20;

    // Neue Mapbox Karte
    var lat = CHtoWGSlat(xkoord, ykoord);
    var lng = CHtoWGSlng(xkoord, ykoord);
    // Link (im Moment wird noch auf Search.ch verlinkt, denn dort sieht man öV Haltestellen)
    div.innerHTML="<a href='http://map.search.ch/"+xkoord+","+ykoord+"' target='_blank'><img src='https://api.mapbox.com/styles/v1/allestuetsmerweh/ckgf9qdzm1pn319ohqghudvbz/static/pin-l+009000("+lng+","+lat+")/"+lng+","+lat+",13,0/"+breite+"x300?access_token=pk.eyJ1IjoiYWxsZXN0dWV0c21lcndlaCIsImEiOiJHbG9tTzYwIn0.kaEGNBd9zMvc0XkzP70r8Q' class='noborder' style='margin:0px;padding:0px;align:center;border:1px solid #000000;'><\/a>";

    mapid = "map_"+id;
    div = document.getElementById(mapid);
    div.innerHTML="<a href='' onclick=\"map('"+id+"',"+xkoord+","+ykoord+");return false;\" class='linkmap'>Karte ausblenden<\/a >"
    }
    else {
    div.style.display="none";
    div.innerHTML="";
    mapid = "map_"+id;
    div = document.getElementById(mapid);
    div.innerHTML="<a href='' onclick=\"map('"+id+"',"+xkoord+","+ykoord+");return false;\" class='linkmap'>Karte zeigen<\/a >"
        }
    return false;}
</script>

<?php
$db_table = "termine";
$ter_filter = [["alle", "Alle Termine"], ["training", "Training"], ["ol", "Wettkämpfe"], ["resultat", "Resultate"], ["club", "Vereinsanlässe"]];

//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth'] == "all") or (in_array($db_table, preg_split("/ /", $_SESSION['auth'])))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}
$button_name = "button".$db_table;
if (isset(${$button_name})) {
    $_SESSION['edit']['db_table'] = $db_table;
}

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($id) and is_ganzzahl($id)) {
    $_SESSION[$db_table."id_"] = $id;
} else {
    $id = $_SESSION[$db_table."id_"];
}
if (isset($jahr) and in_array($jahr, $jahre)) {
    $_SESSION[$db_table."jahr_"] = $jahr;
} else {
    $jahr = $_SESSION[$db_table."jahr_"];
}
if ($jahr == "") {
    $_SESSION[$db_table.'jahr_'] = olz_date("jjjj", "");
}
if (isset($monat) and in_array($monat, $monate)) {
    $_SESSION[$db_table."monat_"] = $monat;
} else {
    $monat = $_SESSION[$db_table."monat_"];
}
if ($monat == "") {
    $_SESSION[$db_table.'monat_'] = "alle";
}
if (isset($filter) and in_array($filter, ['alle', 'training', 'ol', 'club', 'resultat'])) {
    $_SESSION['termin_filter'] = $filter;
} elseif (!isset($_SESSION['termin_filter'])) {
    $_SESSION['termin_filter'] = "alle";
}
$id = $_SESSION[$db_table.'id_'];
$jahr = $_SESSION[$db_table.'jahr_'];
$monat = $_SESSION[$db_table.'monat_'];
$monatzahl = array_search($monat, $monate) + 1;
$periode = $jahr."-".substr("00".$monatzahl, strlen($monatzahl))."-01";

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
} else {
    $functions = [];
}

$function = array_search(${$button_name}, $functions);
if ($zugriff and ($function != "")) {
    include 'admin/admin_db.php';
}
if ($_SESSION['edit']['table'] == $db_table) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}

//-------------------------------------------------------------
// MENÜ
if ($zugriff and $db_edit == "0") {
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")." <span class='linkint'><a href='?page=15'>Termine-Tools</a></span></div>";
    echo "<div class='buttonbar'>".olz_buttons("jahr", $jahre, $jahr)."</div>";
    echo "<div class='buttonbar'>".olz_buttons("monat", $monate, $monat)."</div>";
}

if ($db_edit == "0") {
    // Filter-Dropdown nach Typ
    echo "<div style='padding:4px 3px 10px 3px;'><b>Auswahl:</b>  ";
    $delimiter = "";
    foreach ($ter_filter as $this_filter) {
        if ($_SESSION['termin_filter'] == $this_filter[0]) {
            $selected = " style='text-decoration:underline;'";
        }
        echo $delimiter."<a href='?filter=".$this_filter[0]."'{$selected}>".$this_filter[1]."</a>\n";
        $delimiter = "|";
        $selected = "";
    }
    // Vergangene ein-/ausblenden
    if (($_SESSION['termin_filter'] == "resultat") or ($periode < date("Y-m-d"))) {
        $show = "1";
        $_SESSION['show_bak'] = $_SESSION['show'];
    } elseif (isset($_SESSION['show_bak'])) {
        $show = $_SESSION['show_bak'];
        unset($_SESSION['show_bak']);
    } elseif (!isset($show)) {
        $show = '0';
    }
    $_SESSION['show'] = $show;
    if ($show == "1") {
        $checked = " checked";
    }
    echo "<span style='float:right;'><input type='checkbox' name='show' value='1' onclick='submit()'".$checked." style='vertical-align:middle;margin:0 0.5em 0em 0em;'>Vergangene einblenden</span></div>\n";
} else {
    echo "<input type='hidden' name='show' value='".$_SESSION['show']."'>";
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

//-------------------------------------------------------------
//  VORSCHAU - LISTE
if (($db_edit == "0") or ($do == "vorschau")) {// ADMIN Mysql-Abfrage definieren
    if ($zugriff) {
        if ($monat == "alle") {
            $sql = "WHERE YEAR(datum)= '{$jahr}'";
        } else {
            $sql = "WHERE (MONTH(datum)='".(array_search($monat, $monate) + 1)."') AND (YEAR(datum)='{$jahr}')";
        }
    }
    // USER Mysql-Abfrage definieren
    else {
        if (isset($id)) {
            $sql = "WHERE (id = '{$id}')";
        } else {
            $sql = "WHERE (datum >= '".$_SESSION[$db_table.'jahr_']."-01-01') AND ((datum_off>='".date("Y-m-d")."') OR (datum_off='0000-00-00') OR datum_off IS NULL) AND (on_off = '1')";
        }
    }
    if ($show == "0") {
        $sql .= " AND ((datum >= '{$heute}') OR (datum_end >= '{$heute}'))";
    }
    if (isset($uid)) {
        $sql .= " OR (id={$uid})";
    }
    if ($_SESSION['termin_filter'] == "alle") {
        $sql = $sql." ORDER BY datum ASC";
    }
    //elseif ($_SESSION['termin_filter'] == "resultat") $sql = $sql." AND ((link LIKE '%resultat%') OR (link LIKE '%rangliste%')) ORDER BY datum DESC";
    elseif ($_SESSION['termin_filter'] == "resultat") {
        $sql = $sql." AND ((typ LIKE '%ol%') AND (datum <= '{$heute}')) ORDER BY datum DESC";
    } else {
        $sql = $sql." AND (typ LIKE '%".$_SESSION['termin_filter']."%') ORDER BY datum ASC";
    }
    if ($do == "vorschau") {
        $sql = "WHERE (id ='{$id}')";
    } // Proforma-Abfrage

    //$sql = "SELECT * FROM ".$db_table." LEFT JOIN solv_termine ON solv_uid=unique_id ".$sql;
    $sql = "SELECT * FROM ".$db_table." ".$sql;
    //if($zugriff) echo $sql;
    // DB-ABFRAGE
    $result = $db->query($sql);

    echo "<table class='liste'>";
    $id_spalte = " id='Spalte1'";
    while ($row = mysqli_fetch_array($result)) {
        if ($do == "vorschau") {
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
            $result_solv = $db->query("SELECT * FROM solv_events WHERE solv_uid='".intval($solv_uid)."'");
            $row_solv = $result_solv->fetch_assoc();
        }
        $tn = ($zugriff == 1) ? "(".$row['teilnehmer'].($solv_uid > 0 ? ";SOLV" : "").") " : "";
        //Karte zeigen
        if ($xkoord > 0 and $datum >= $heute) {
            $link .= "<div id='map_{$id}'><a href='http://map.search.ch/{$xkoord},{$ykoord}' target='_blank' onclick=\"map('{$id}',{$xkoord},{$ykoord});return false;\" class='linkmap'>Karte zeigen</a></div>";
        }
        //SOLV-Karte zeigen
        elseif ($row_solv["coord_x"] > 0 and $datum >= $heute) {
            $link .= "<div id='map_{$id}'><a href='http://map.search.ch/".$row_solv["coord_x"].",".$row_solv["coord_y"]."' target='_blank' onclick=\"map('{$id}',".$row_solv["coord_x"].",".$row_solv["coord_y"].");return false;\" class='linkmap'>Karte zeigen</a></div>";
        }
        //Anmeldungs-Link zeigen
        //Manueller Anmeldungs-Link entfernen
        if (($go2ol > "" or $row_solv["entryportal"] == 1 or $row_solv["entryportal"] == 2)) {
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
            $link .= "<div class='linkext'><a href='http://go2ol.ch/".$go2ol."/' target='_blank'>Anmeldung</a></div>\n";
        } elseif ($row_solv["entryportal"] == 1 and $datum >= $heute) {
            $link .= "<div class='linkext'><a href='http://www.go2ol.ch/index.asp?lang=de' target='_blank'>Anmeldung</a></div>\n";
        } elseif ($row_solv["entryportal"] == 2 and $datum >= $heute) {
            $link .= "<div class='linkext'><a href='http://entry.picoevents.ch/' target='_blank'>Anmeldung</a></div>\n";
        }
        if (strpos($row['link'], 'Ausschreibung') == 0 and $row['solv_event_link'] > "") {
            $class = strpos($row['solv_event_link'], ".pdf") > 0 ? 'linkpdf' : 'linkext';
            $umbruch = $row['link'] == "" ? "" : "<br>";
            $link .= $umbruch."<a href='".$row['solv_event_link']."' target='_blank' class='{$class}'>Ausschreibung</a>";
        }
        if ($row_solv && isset($row_solv["deadline"]) && $row_solv["deadline"] && $row_solv["deadline"] != "0000-00-00") {
            $text .= ($text == "" ? "" : "<br />")."Meldeschluss: ".olz_date("t. MM ", $row_solv["deadline"]);
        }
        //Ranglisten-Link zeigen
        if ($solv_uid > 0 and $datum <= $heute and strpos($link, "Rangliste") == "" and strpos($link, "Resultat") == "" and strpos($typ, "ol") >= 0) {
            $link .= "<div><a href='http://www.o-l.ch/cgi-bin/results?unique_id=".$solv_uid."&club=zimmerberg' target='_blank' class='linkext'>Rangliste</a></div>\n";
        }
        //SOLV-Ausschreibungs-Link zeigen
        if ($row_solv["event_link"] and strpos($link, "Ausschreibung") == "" and strpos($typ, "ol") >= 0 and $datum <= $heute) {
            $ispdf = preg_match("/\\.pdf$/", $row_solv["event_link"]);
            $link .= "<div><a href='".$row_solv["event_link"]."' target='_blank' class='link".($ispdf ? "pdf" : "ext")."'>Ausschreibung</a></div>\n";
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
            $link = "<div class='linkint'><a href='index.php?page=13&amp;id_anm={$id}'>Online-Anmeldung</a></div>".$link;
        }

        if ($zugriff and ($do != 'vorschau')) {
            //Berbeiten-/Duplizieren-Button
            $edit_admin = "<a href='index.php?id={$id}&{$button_name}=start' class='linkedit' title='Termin bearbeiten'>&nbsp;</a><a href='index.php?id={$id}&{$button_name}=duplicate' class='linkedit2 linkduplicate' title='Termin duplizieren'>&nbsp;</a>";
            if ($datum_anmeldung && ($datum_anmeldung != '') and ($datum_anmeldung != '0000-00-00')) {
                $edit_anm = "<a href='index.php?page=14&amp;id_anm={$id}&buttonanm_felder=start' class='linkedit' title='Online-Anmeldung bearbeiten'>&nbsp;</a>";
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
            $datum_tmp = olz_date("t. MM ", $datum).olz_date(" (W)", $datum);
            if ($zeit != "00:00:00") {
                $datum_tmp .= "<br />".date("H:i", strtotime($zeit));
                if ($zeit_end != "00:00:00") {
                    $datum_tmp .= " &ndash; ".date("H:i", strtotime($zeit_end));
                }
            }
        }
        //Mehrtägig innerhalb Monat
        elseif (olz_date("m", $datum) == olz_date("m", $datum_end)) {
            $datum_tmp = olz_date("t.-", $datum).olz_date("t. ", $datum_end).olz_date("MM", $datum).olz_date(" (W-", $datum).olz_date("W)", $datum_end);
        }
        //Mehrtägig monatsübergreifend
        else {
            $datum_tmp = olz_date("t.m.-", $datum).olz_date("t.m. ", $datum_end).olz_date("jjjj", $datum).olz_date(" (W-", $datum).olz_date("W)", $datum_end);
        }
        if ($uid == $row['id']) {
            $class = " class='selected'";
        } elseif ($datum_end < $heute) {
            $class = " class='passe'";
        } elseif ($on_off == 0) {
            $class = " class='off'";
        } else {
            $class = "";
        }

        // HTML-Ausgabe
        if (($_SESSION['termin_filter'] == "resultat" and (strpos($link, "Rangliste") > "" or strpos($link, "Resultat") > "")) or ($_SESSION['termin_filter'] != "resultat")) {
            echo olz_monate($datum)."<tr".$class.">\n\t<td id='id".$id."' style='width:25%;'>".$edit_admin.$edit_anm.$datum_tmp.$icn_newsletter."</td><td style='width:55%;'{$id_spalte}>".$tn.$text."<div id='map{$id}' style='display:none;width:100%;text-align:left;margin:0px;padding-top:4px;clear:both;'></div></td><td style='width:20%;'>".$link."</td>\n</tr>\n";
        }
        $id_spalte = "";
    }
    echo "</table>";

    //-------------------------------------------------------------
    //  Wiederkehrendes Datum anzeigen
    //-------------------------------------------------------------
    if ($do == 'vorschau' and $modus_termin == 'repeat') {
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
} elseif (($function == 'neu' or $function == 'edit') and $_SESSION['edit']['modus'] == 'neuedit') {
    $checked = ($_SESSION[$db_table]['repeat'] == 'repeat') ? ' checked' : '';
    $intervall = (isset($_SESSION[$db_table]['intervall'])) ? $_SESSION[$db_table]['intervall'] : '7';
    $_SESSION[$db_table]['termin_'] = $termin_;
    echo "<table class='liste'>";
    echo "<tr><td style='width:20%;padding-top:4px;'><b>Termin wiederholen</b></td><td style='width:80%'><p><input type='checkbox' name='modus_termin' value='repeat'{$checked}><span style='margin-left:20px;'>(Achtung: Für das Wiederholen von Terminen muss ein Enddatum angegeben werden)</span></p></td></tr>";
    echo "<tr><td style='width:20%;padding-top:4px;'><b>Intervall (Tage)</b></td><td style='width:80%'><input type='text' name='intervall_termin' value='{$intervall}'></td></tr></table>";
}
?>
