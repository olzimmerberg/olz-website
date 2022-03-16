<?php

// =============================================================================
// Tool-Sammlung, um das Verwalten der Termine zu vereinfachen.
// TODO(simon): Was davon funktioniert überhaupt noch? Tests?
// =============================================================================

require_once __DIR__.'/config/database.php';
require_once __DIR__.'/config/date.php';

// $_GET["visitor"]=="map"
// $_GET["visitor"]=="cronjob"

// RECHTE SPALTE
echo "<div style='float:right;width:20%;'>";
echo "<h2>Termine-Tools</h2>";
echo "<h3><a href='?mode=show' class='linkint'>OLZ Termine Anzeigen</a></h3>"; // show
echo "OLZ Termine, die mit dem SOLV synchronisiert werden, anzeigen";
echo "<div style='opacity:0.5;'><h3><a href='?mode=import' class='linkint'>CSV Import</a></h3>"; // import
echo "OLZ Termine aus CSV-Datei hinzufügen</div>";
echo "<h3><a href='?mode=add' class='linkint'>SOLV Import</a></h3>"; // add
echo "Termine vom SOLV neu hinzufügen";
echo "<h3><a href='?mode=solvuids' class='linkint'>SOLV IDS vergeben</a></h3>"; // solvuids
echo "OLZ-Terminen SOLV-Termine zuordnen";
echo "<div style='opacity:0.5;'><h3><a href='?mode=check' class='linkint'>SOLV Prüfen</a></h3>"; // check
echo "OLZ-Termine, denen eine SOLV-ID zugeordnet ist, überprüfen; Mail verschicken";
echo "<h3><a href='?mode=compare' class='linkint'>SOLV Vergleichen/Anpassen</a></h3>"; // compare
echo "OLZ-Termine und SOLV-Termine synchronisieren<br>Typischerweise durch Link im Mail</div>";
echo "</div>";

// LINKE SPALTE
echo "<div style='float:left;width:80%;'>";

$start = microtime(1);
$timestamp = (strtotime(olz_current_date("Y-m-d H:i:s")) - strtotime(olz_current_date("Y-m-d")));

if ($_GET["visitor"] == "map") {
    $_GET["mode"] = "kml";
}
if ($_GET["visitor"] == "cronjob") {
    $_GET["mode"] = "check";
}
if ((($_SESSION['auth'] ?? null) == 'all') or (in_array("termine", preg_split('/ /', $_SESSION['auth'] ?? '')))) {
    $zugriff = "1";
} elseif ($_GET["mode"] == "kml" && $_GET["visitor"] == "map") {
    header("Content-Type:application/vnd.google-earth.kml+xml");
    echo "<kml xmlns=\"http://www.opengis.net/kml/2.2\" xmlns:gx=\"http://www.google.com/kml/ext/2.2\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/kml/2.2 https://developers.google.com/kml/schema/kml22gx.xsd\">
    <Placemark>
    <Style>
        <IconStyle>
            <Icon>
                <href>https://map.geo.admin.ch/1429715345/img/maki/circle-stroked-24@2x.png</href>
                <gx:w>48</gx:w>
                <gx:h>48</gx:h>
            </Icon>
            <hotSpot x=\"24\" y=\"24\" xunits=\"pixels\" yunits=\"pixels\"/>
        </IconStyle>
    </Style>
    <Point>
        <coordinates>8.694924196906893,47.207471152887145</coordinates>
    </Point>
    </Placemark>
</kml>";
    return;
} elseif ($_GET["mode"] == "check" && $_GET["visitor"] == "cronjob") {
    $zugriff = "1";
    require_once __DIR__.'/config/init.php';
} else {
    if ($_GET["visitor"] == "cronjob") {
        mail("simon.hatt@olzimmerberg.ch", "CronJob nicht ausgeführt", "Der CronJob konnte um ".olz_current_date("H:i:s")." am ".olz_current_date("d.m.Y")." nicht ausgeführt werden", "From: OL Zimmerberg<system@olzimmerberg.ch>");
    }
    echo "Kein Zugriff. <br>".(strtotime(olz_current_date("Y-m-d H:i:s")) - strtotime(olz_current_date("Y-m-d")))."<br>";
    $zugriff = "0";
}

if ($zugriff == "1") {
    include_once "parsers.php"; // functions 'solvdataforyear($year)', 'go2oldata()', load_url($url)
    echo "<table><tr><td>";
    if ($status == "Hinzufügen") {
        $sql = "INSERT into termine (datum,datum_end,datum_off,titel,text,typ,link,xkoord,ykoord,on_off,go2ol,solv_uid) VALUES ('".$_POST["datum"]."','".$_POST["datum_end"]."','".$_POST["datum_off"]."','".$_POST["titel"]."','".$_POST["text"]."','".$_POST["typ"]."','".$_POST["link"]."','".$_POST["xkoord"]."','".$_POST["ykoord"]."','".$_POST["on_off"]."','".$_POST["go2ol"]."','".$_POST["solv_uid"]."')";
        $db->query($sql);
        echo "<div style='position:absolute; margin-top:20px; background-color:#ffffff;'>".$sql."</div>";
    }

    if ($status == "IDs setzen") {
        $keys = array_keys($_POST);
        for ($i = 0; $i < count($keys); $i++) {
            if (substr($keys[$i], 0, 3) == "olz") {
                $sql = "UPDATE termine SET solv_uid='".$_POST[$keys[$i]]."' WHERE id='".substr($keys[$i], 3)."'";
                $db->query($sql, $conn_id);
            }
        }
    }

    if ($_GET["mode"] == "show") {
        $_SESSION["termine_helper"] = "show";
    }

    if ($_GET["mode"] == "import") {
        $_SESSION["termine_helper"] = "import";
    }

    if ($_GET["mode"] == "check") {
        $_SESSION["termine_helper"] = "check";
    }

    if ($_GET["mode"] == "compare") {
        $_SESSION["termine_helper"] = "compare";
    }

    if ($_GET["mode"] == "add") {
        $_SESSION["termine_helper"] = "add";
        $_SESSION["termine_helper_add_step"] = "0";
        $year = olz_current_date("Y");
        if (olz_current_date("m") > 8) {
            $year++;
        }
        echo "YEAR: ".$year."<br>";
        $_SESSION["termine_helper_add_termine"] = solvdataforyear($year);
    }

    if ($_GET["mode"] == "solvuids" or $mode == "Alle zeigen") {
        $_SESSION["termine_helper"] = "solvuids";
        // $_SESSION["termine_helper_solvuids_termine"] = solvdataforyear(false);
        $alle_zeigen = ($mode == "Alle zeigen");
    }

    // SHOW
    if ($_SESSION["termine_helper"] == "show") {
        $sql = "select * from termine WHERE solv_uid!='0' ORDER BY datum DESC";
        // DB-ABFRAGE
        $result = $db->query($sql);

        echo "<table class='liste'>";
        while ($row = mysqli_fetch_array($result)) {
            $datum = $row['datum'];
            $datum_end = $row['datum_end'];
            $titel = $row['titel'];
            $text = $row['text'];
            $text = olz_mask_email($text, "", "");
            $link = $row['link'];
            $id = $row['id'];
            $on_off = $row['on_off'];
            $newsletter = $row['newsletter'];
            $datum_anmeldung = $row['datum_anmeldung'];
            $xkoord = $row['xkoord'];
            $ykoord = $row['ykoord'];
            $go2ol = $row['go2ol'];

            if ($datum_end == "0000-00-00") {
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

            if (($datum_anmeldung != '0000-00-00') and ($datum_anmeldung != '') and ($zugriff) and ($datum_anm > $heute)) {
                $link = "<div class='linkint'><a href='anmeldung.php?id_anm={$id}'>Online-Anmeldung</a></div>".$link;
            }

            if ($newsletter) {
                $icn_newsletter = "<img src='icns/mail2.gif' class='noborder' style='margin-left:4px;vertical-align:top;' title='Newsletter-Benachrichtigung' alt=''>";
            } else {
                $icn_newsletter = "";
            }

            // Tagesanlass
            if (($datum_end == $datum) or ($datum_end == "0000-00-00")) {
                $datum_tmp = $_DATE->olzDate("t. MM ", $datum).$_DATE->olzDate(" (W)", $datum);
            }
            // Mehrtägig innerhalb Monat
            elseif ($_DATE->olzDate("m", $datum) == $_DATE->olzDate("m", $datum_end)) {
                $datum_tmp = $_DATE->olzDate("t.-", $datum).$_DATE->olzDate("t. ", $datum_end).$_DATE->olzDate("MM", $datum).$_DATE->olzDate(" (W-", $datum).$_DATE->olzDate("W)", $datum_end);
            }
            // Mehrtägig monatsübergreifend
            else {
                $datum_tmp = $_DATE->olzDate("t.m.-", $datum).$_DATE->olzDate("t.m. ", $datum_end).$_DATE->olzDate("jjjj", $datum).$_DATE->olzDate(" (W-", $datum).$_DATE->olzDate("W)", $datum_end);
            }

            if ($on_off == 0) {
                $class = " class='off'";
            } elseif ($datum_end < $heute) {
                $class = " class='passe'";
            } else {
                $class = "";
            }

            // HTML-Ausgabe
            if (($xkoord > 0) and ($datum_end > $heute)) {
                $maplink = "<div id='map_{$id}'><a href='http://map.search.ch/{$xkoord},{$ykoord}' target='_blank' onclick=\"map('{$id}',{$xkoord},{$ykoord});return false;\" class='linkmap'>Karte zeigen</a></div>";
            } else {
                $maplink = "";
            }
            if ((strlen($go2ol) > 0) and ($datum_end > $heute)) {
                $go2ollink = "<div><a href='http://www.go2ol.ch/".$go2ol."' target='_blank' class='linkext'>GO2OL</a></div>";
            } else {
                $go2ollink = "";
            }
            echo olz_monate($datum)."<tr".$class.">\n\t<td id='id".$id."' style='width:25%;'>".$datum_tmp.$icn_newsletter."</td><td style='width:55%;'{$id_spalte}>".$text."<div id='map{$id}' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div></td><td style='width:20%;'>".$maplink.$go2ollink.$link."</td>\n</tr>\n";
        }
        echo "</table>";
    }

    // CHECK

    if ($_SESSION["termine_helper"] == "check") {
        $infos = "";
        $console = "";
        $solv = solvdataforyear(false);
        $solvbyid = [];
        for ($i = 0; $i < count($solv); $i++) {
            // echo $solv[$i]["uniqueid"]."<br>";
            $result = $db->query("SELECT * FROM termine WHERE solv_uid='".intval($solv[$i]["uniqueid"])."'", $conn_id);
            if (mysqli_num_rows($result) > 0) {
                $solvbyid[$solv[$i]["uniqueid"]] = $solv[$i];
                $row = mysqli_fetch_array($result);
                // Koordinaten aktualisieren
                if ($solv[$i]["coordx"] > 0 and $solv[$i]["coordy"] > 0) {
                    $sql = "UPDATE termine SET xkoord=".$solv[$i]["coordx"].",ykoord=".$solv[$i]["coordy"]." WHERE id=".$row['id'];
                    $result = $db->query($sql);
                    echo "Koordinaten aktualisiert: ".$row['datum']." > ".$row['titel']." > ".$solv[$i]["coordx"].",".$solv[$i]["coordy"]."<br>";
                }

                if ($solv[$i]["link"] > "") {
                    $sql = "UPDATE termine SET solv_event_link='".$solv[$i]["link"]."' WHERE id=".$row['id'];
                    echo $sql;
                    $result = $db->query($sql);
                    echo "Ausschreibung aktualisiert: ".$row['datum']." > ".$row['titel']." > ".$solv[$i]["link"]."<br>";
                }
            }
        }
        print_r(array_keys($solvbyid));
        echo "<br><br>";

        $go2ol = go2oldata();
        $go2olbyid = [];
        for ($i = 0; $i < count($go2ol); $i++) {
            $result = $db->query("SELECT id FROM termine WHERE solv_uid='".intval($go2ol[$i]["solv_uid"])."'", $conn_id);
            if (mysqli_num_rows($result) > 0) {
                $go2olbyid[$solv[$i]["uniqueid"]] = $go2ol[$i];
            }
        }
        print_r(array_keys($go2olbyid));
        echo "<br><br>";

        $result = $db->query("SELECT * FROM termine WHERE solv_uid IN ('".implode("', '", array_merge(array_keys($solvbyid), array_keys($go2olbyid)))."')", $conn_id);
        $num = mysqli_num_rows($result);
        for ($i = 0; $i < $num; $i++) {
            $row = mysqli_fetch_array($result);
        }
    }

    // COMPARE
    /*
    if ($_SESSION["termine_helper"]=="compare") {
        echo "<table class='liste'>
<tr><td style='background-color:#cccccc;'></td><td style='background-color:#cccccc; width:40%; text-align:center;'><h3><img src='favicon.gif' alt='' class='noborder'> OLZ</h3></td><td style='background-color:#cccccc; width:40%; text-align:center;'><h3><img src='icns/orienteering_forest_16.svg' alt='' class='noborder'> SOLV</h3></td></tr>";
        $sql_tmp = "";
        if (isset($ids) && is_array($ids)) {
            $_SESSION["termine_helper_compare_limit"] = count($ids);
            $sql_tmp = "AND (";
            for ($i=0; $i<count($ids); $i++) {
                if ($i!=0) {
                    $sql_tmp .= " OR ";
                }
                $sql_tmp .= "solv_uid='".$ids[$i]."'";
            }
            $sql_tmp .= ")";
        } else {
            $limit = 1;
            if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
                $limit = $_GET["limit"];
            }
            $_SESSION["termine_helper_compare_limit"] = $limit;
            $sql_tmp = " AND datum>='".date("Y-m-d")."'";
        }
        $limit_tmp = " LIMIT 1";
        if ($_SESSION["termine_helper_compare_limit"]!=1) {
            $limit_tmp = " LIMIT ".$_SESSION["termine_helper_compare_limit"];
        }
        $result = $db->query("SELECT * FROM termine WHERE solv_uid!=''".$sql_tmp." ORDER BY datum ASC".$limit_tmp);
        $termin=0;
        $go2ol = go2oldata();
        while ($row = mysqli_fetch_array($result)) {
            $uid = $row["solv_uid"];
            echo "<input type='hidden' name='uids[".$termin."]' value='".$uid."'>";
            $compare_results = compare_uid($uid,$go2ol);
            $solv = $compare_results["solv"];
            $all_links = $compare_results["links"]["all_links"];
            $olz_links = $compare_results["links"]["olz_links"];
            $solv_links = $compare_results["links"]["solv_links"];
            $links_done = array();
            $olzlinkshtml = "";
            for ($i=0; $i<count($olz_links); $i++) {
                $olzlinkshtml .= (($i==0)?"":"<br>")."<input type='checkbox' name='".md5($uid."link")."[".($i)."]' value='".$all_links[$olz_links[$i]]["href"]." / ".$all_links[$olz_links[$i]]["text"]."' checked> <a href='".$all_links[$olz_links[$i]]["href"]."'>".$all_links[$olz_links[$i]]["text"]."</a>";
                array_push($links_done,$olz_links[$i]);
            }
            $solvlinkshtml = "";
            for ($i=0; $i<count($solv_links); $i++) {
                if (!is_bool(array_search($solv_links[$i],$links_done))) {
                    $checked = "";
                } else {
                    array_push($links_done,$solv_links[$i]);
                    $checked = " checked";
                }
                $solvlinkshtml .= (($i==0)?"":"<br>")."<input type='checkbox' name='".md5($uid."link")."[".($i+count($olz_links))."]' value='".$all_links[$solv_links[$i]]["href"]." / ".$all_links[$solv_links[$i]]["text"]."'".$checked."> <a href='".$all_links[$solv_links[$i]]["href"]."'>".$all_links[$solv_links[$i]]["text"]."</a>";
            }
            echo "<tr><td colspan='3' class='buttonbar'>".$row["titel"]."</td></tr>";
            echo "<tr><td>Datum</td><td>".date("d.m.Y",strtotime($row["datum"]))."</td><td>".date("d.m.Y",$solv["datum"])."</td></tr>";
            echo "<tr><td>Titel</td><td>".$row["titel"]."</td><td>".$solv["name"]."</td></tr>";
            $thischecked = ($row["text"]!="")?" checked":"";
            echo "<tr><td>Info</td><td>
<input type='checkbox' name='".md5($uid."text")."[0]' value=\"".str_replace("\\\"","\'",addslashes($row["text"]))."\"".$thischecked."> ".$row["text"]."</td><td>
<input type='checkbox' name='".md5($uid."text")."[1]' value='Veranstalter: ".$solv["verein"]."'> Veranstalter: ".$solv["verein"]."<br>
<input type='checkbox' name='".md5($uid."text")."[2]' value='Karte: ".$solv["karte"]."'> Karte: ".$solv["karte"]."<br>
<input type='checkbox' name='".md5($uid."text")."[3]' value='Region: ".$solv["region"]."'> Region: ".$solv["region"]."</td></tr>";
            if ($row["xkoord"]==0 && $row["ykoord"]==0 && $solv["map"]["x"]!=0 && $solv["map"]["y"]!=0) {
                echo "<tr><td>Koordinaten</td><td>".$row["xkoord"]." / ".$row["ykoord"]."</td><td>
<input type='checkbox' name='".md5($uid."koord")."' value='".$solv["map"]["x"]." / ".$solv["map"]["y"]."' checked> ".$solv["map"]["x"]." / ".$solv["map"]["y"]."</td></tr>";
            } else {
                echo "<tr><td>Koordinaten</td><td>".$row["xkoord"]." / ".$row["ykoord"]."</td><td>
<input type='checkbox' name='".md5($uid."koord")."' value='".$solv["map"]["x"]." / ".$solv["map"]["y"]."'> ".$solv["map"]["x"]." / ".$solv["map"]["y"]."</td></tr>";
            }
            echo "<tr><td>Links</td><td>".$olzlinkshtml."</td><td>".$solvlinkshtml."</td></tr>";
            $all_go2ols = $compare_results["go2ols"]["all_go2ols"];
            $olz_go2ols = $compare_results["go2ols"]["olz_go2ols"];
            $solv_go2ols = $compare_results["go2ols"]["solv_go2ols"];
            $go2ols_done = array();
            $olzgo2olhtml = "";
            for ($i=0; $i<count($olz_go2ols); $i++) {
                $olzgo2olhtml .= (($i==0)?"":"<br>")."<input type='checkbox' name='".md5($uid."go2ol")."[".($i)."]' value='".$all_go2ols[$olz_go2ols[$i]][0]."' checked> ".$all_go2ols[$olz_go2ols[$i]][0];
                array_push($go2ols_done,$olz_go2ols[$i]);
            }
            $solvgo2olhtml = "";
            for ($i=0; $i<count($solv_go2ols); $i++) {
                if (!is_bool(array_search($solv_go2ols[$i],$go2ols_done))) {
                    $checked = "";
                } else {
                    array_push($go2ols_done,$solv_go2ols[$i]);
                    $checked = " checked";
                }
                $solvgo2olhtml .= (($i==0)?"":"<br>")."<input type='checkbox' name='".md5($uid."go2ol")."[".($i+count($olz_go2ols))."]' value='".$all_go2ols[$solv_go2ols[$i]][0]."'".$checked."> ".$all_go2ols[$solv_go2ols[$i]][0];
            }
            echo "<tr><td>GO2OL</td><td>".$olzgo2olhtml."</td><td>".$solvgo2olhtml."</td></tr>";
            $all_meldeschlusse = $compare_results["meldeschlusse"]["all_meldeschlusse"];
            $olz_meldeschlusse = $compare_results["meldeschlusse"]["olz_meldeschlusse"];
            $solv_meldeschlusse = $compare_results["meldeschlusse"]["solv_meldeschlusse"];
            $meldeschlusse_done = array();
            $olzmeldeschlusshtml = "";
            for ($i=0; $i<count($olz_meldeschlusse); $i++) {
                $olzmeldeschlusshtml .= (($i==0)?"":"<br>")." ".date("d.m.Y",$all_meldeschlusse[$olz_meldeschlusse[$i]]["datum"])." (".$all_meldeschlusse[$olz_meldeschlusse[$i]]["typ"].")";
                array_push($meldeschlusse_done,$olz_meldeschlusse[$i]);
            }
            $solvmeldeschlusshtml = "";
            for ($i=0; $i<count($solv_meldeschlusse); $i++) {
                if (!is_bool(array_search($solv_meldeschlusse[$i],$meldeschlusse_done))) {
                    $checked = "";
                } else {
                    array_push($meldeschlusse_done,$solv_meldeschlusse[$i]);
                    $checked = " checked";
                }
                $solvmeldeschlusshtml .= (($i==0)?"":"<br>")."<input type='checkbox' name='".md5($uid."meldeschluss")."[".$i."]' value='".$all_meldeschlusse[$solv_meldeschlusse[$i]]["datum"]." / ".$all_meldeschlusse[$solv_meldeschlusse[$i]]["typ"]."'".$checked."> ".date("d.m.Y",$all_meldeschlusse[$solv_meldeschlusse[$i]]["datum"])." (".$all_meldeschlusse[$solv_meldeschlusse[$i]]["typ"].")";
            }
            echo "<tr><td>Meldeschlüsse</td><td>".$olzmeldeschlusshtml."</td><td>".$solvmeldeschlusshtml."</td></tr>";
            $termin++;
        }
        echo "<tr><td colspan='3'><input type='submit' name='status' value='Ändern'></td></tr>";
        echo "</table>";
    }
    */

    // ADD

    if ($_SESSION["termine_helper"] == "add") {
        if ($status == "Weiter") {
            $_SESSION["termine_helper_add_step"]++;
        }
        if ($status == "Zurück") {
            $_SESSION["termine_helper_add_step"]--;
        }
        $solv_termine = $_SESSION["termine_helper_add_termine"];
        echo "<style type='text/css'>
.yes {color:#000000;}
.maybe {color:#555555;}
.no {color:#aaaaaa;}
.bgyes {background-color:#aaffaa;}
.bgmaybe {background-color:#ccffaa;}
.bgno {background-color:#ffaaaa;}
table.raster tr {height:2em;}
</style>
<input type='submit' name='status' value='Zurück'> | <input type='submit' name='status' value='Hinzufügen'><input type='submit' name='status' value='Weiter'>
";
        $i = $_SESSION["termine_helper_add_step"];
        if ($i < count($solv_termine)) {
            $class = "no";
            $checked = "";
            $abkletters = ["S", "O", "M"];
            for ($j = 0; $j < count($abkletters); $j++) {
                if (!is_bool(strpos($solv_termine[$i]["abk"], strtoupper($abkletters[$j]))) || !is_bool(strpos($solv_termine[$i]["abk"], strtolower($abkletters[$j])))) {
                    $class = "maybe";
                }
            }
            if (!is_bool(strpos($solv_termine[$i]["abk"], "**A")) || $solv_termine[$i]["region"] == "ZH/SH") {
                $class = "yes";
                $checked = " checked";
            }
            echo "<div class='bg{$class}'><div class='{$class}' style='font-weight:bold; padding:5px;'>".date("d.m.Y", $solv_termine[$i]["datum"])." - ".$solv_termine[$i]["name"]." - ".$solv_termine[$i]["region"]." - ".$solv_termine[$i]["verein"]." - ".$solv_termine[$i]["karte"]." - ".$solv_termine[$i]["abk"]."</div>";
            $coordinates = parse_map($solv_termine[$i]["map"]);
            echo "<table class='raster'>
<tr><td style='width:30%;'>Datum (Beginn)</td><td style='width:70%;'><input type='text' name='datum' value='".date("Y-m-d", $solv_termine[$i]["datum"])."' style='width:10em;'></td></tr>
<tr><td>Datum (Ende)</td><td><input type='text' name='datum_end' value='".date("Y-m-d", $solv_termine[$i]["datum"])."' style='width:10em;'></td></tr>
<tr><td>Datum (Ausschalten)</td><td><input type='text' name='datum_off' value='0000-00-00' style='width:10em;'></td></tr>
<tr><td>Titel</td><td><input type='text' name='titel' value='".$solv_termine[$i]["name"]."' style='width:90%;'></td></tr>
<tr><td>Text</td><td><textarea name='text' style='width:90%; height:5em;'>Veranstalter: ".$solv_termine[$i]["verein"]."\nKarte: ".$solv_termine[$i]["karte"]."</textarea></td></tr>
<tr><td>Typ</td><td><input type='text' name='typ' value='ol' style='width:10em;'><input type='checkbox'>Klubanlass</td></tr>
<tr><td>Link (wird später automatisch aktualisiert)</td><td><textarea name='link' style='width:90%; height:5em;'></textarea></td></tr>
<tr><td>Koordinaten</td><td>X:<input type='text' name='xkoord' value='".$coordinates["x"]."' style='width:7em;'> - Y:<input type='text' name='ykoord' value='".$coordinates["y"]."' style='width:7em;'></td></tr>
<tr><td>Aktiv</td><td><input type='checkbox' name='on_off' value='1' checked></td></tr>
<tr><td>GO2OL Code</td><td><input type='text' name='go2ol' value='' style='width:10em;'></td></tr>
<tr><td>SOLV UID</td><td><input type='text' name='solv_uid' value='".parse_uid($solv_termine[$i]["link"])."' style='width:10em;'></td></tr>
<tr><td>Newsletter</td><td><input type='checkbox' name='on_off' value='1'></td></tr>
</table></div>";
            $rechts = "<img src='http://map.search.ch/chmap.de.jpg?layer=bg,fg,copy,ruler,circle&amp;zd=32&amp;x=0m&amp;y=0m&amp;w=360&amp;h=360&amp;base=".$coordinates["x"].",".$coordinates["y"]."' alt='Keine Koordinaten angegeben'>";
        }
    }

    // SOLV UIDs

    if ($_SESSION["termine_helper"] == "solvuids") {
        $year = olz_current_date("Y");
        if (olz_current_date("m") > 8) {
            $year++;
        }
        $sql_tmp = ($alle_zeigen) ? "" : "(solv_uid='0' OR solv_uid IS NULL) AND";
        $sql = "SELECT * FROM termine WHERE {$sql_tmp} datum>='".$year."-01-01' AND datum<='".$year."-12-31' AND (typ LIKE '%ol%') AND (titel NOT LIKE '%Meldeschluss%')";
        $result = $db->query($sql, $conn_id);

        echo "<table style='border-collapse:collapse;' cellspacing='0' class='liste'><thead><tr><td>OLZ Termin</td><td>SOLV Termin</td><td>ID</td></tr></thead>";
        while ($row = mysqli_fetch_array($result)) {
            $solv_uid = ($row['solv_uid'] > 0) ? $row['solv_uid'] : "";
            $matching = [];
            $maxsimval = -1;
            $maxsimind = -1;
            $sql_solv = "SELECT * FROM solv_events WHERE date='".$row["datum"]."'";

            $result_solv = $db->query($sql_solv, $conn_id);
            $num_solv = mysqli_num_rows($result_solv);
            for ($i = 0; $i < $num_solv; $i++) {
                $row_solv = mysqli_fetch_array($result_solv);
                $sim = is_similar($row["titel"], $row_solv["name"]);

                array_push($matching, [$row_solv, $sim]);
                if ($maxsimval < $sim || $maxsimval == -1) {
                    $maxsimval = $sim;
                    $maxsimind = count($matching) - 1;
                }
            }

            if (count($matching) > 0) {
                $color = ($solv_uid > 0) ? "#c6ff8e" : "#fdb4b5";
                echo "<tr><td style='background-color:{$color};width:270px;'>".$_DATE->olzDate('t.m.jj / ', $row['datum']).$row["titel"]."</td><td style='background-color:{$color};width:100px;'>";
                echo "<select name='olz".$row["id"]."' style='width:280px;'><option value='0'>---</option>";
                for ($i = 0; $i < count($matching); $i++) {
                    $selected = "";
                    if ($maxsimind == $i && $maxsimval > 0.0002) {
                        $selected = " selected";
                    }
                    $similarity = $matching[$i][1];
                    echo "<option value='".$matching[$i][0]["solv_uid"]."'".$selected.">".round($similarity * 100, 0)." - ".$matching[$i][0]["name"]."</option>";
                }
                echo "</select></td><td style='padding-left:4px;width:30px;background-color:{$color};'>{$solv_uid}</td>";
            } else {
                echo "<tr><td>".$_DATE->olzDate('t.m.jj / ', $row['datum']).$row["titel"]."</td><td></td><td></td>";
            }
            echo "</tr>\n";
        }
        echo "</table><p><input type='submit' name='status' value='IDs setzen' class='dropdown'><input type='submit' name='mode' value='Alle zeigen' style='margin-left:10px;' class='dropdown'></p>";
    }

    echo "</td><td style='width:10px;'></td><td style='width:25%;'>";

    echo "</td></tr></table>";
    echo "/<div>";
}

// ---

// FUNCTIONS

function is_similar($str1, $str2) {
    $str1 = trim(strtolower($str1));
    $str2 = trim(strtolower($str2));
    $maxlen = strlen($str1) + strlen($str2);
    $diff = levenshtein($str1, $str2) / $maxlen;
    return 1 - $diff;
}

echo "Dauer: ".(microtime(1) - $start);
