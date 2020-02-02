<?php
$start = microtime(1);

$timestamp = (strtotime(date("Y-m-d H:i:s"))-strtotime(date("Y-m-d")));

if ($_GET["visitor"]=="map") $_GET["mode"] = "kml";
if ($_GET["visitor"]=="cronjob") $_GET["mode"] = "check";
if (($_SESSION['auth'] == "all") OR (in_array("termine" ,preg_split("/ /",$_SESSION['auth'])))) {
    $zugriff = "1";
} else if ($_GET["mode"]=="kml" && $_GET["visitor"]=="map") {
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
} else if ($_GET["mode"]=="check" && $_GET["visitor"]=="cronjob") {
    $zugriff = "1";
    include "admin/olz_init.php";
} else {
    if ($_GET["visitor"]=="cronjob") {
        mail("simon.hatt@olzimmerberg.ch","CronJob nicht ausgeführt","Der CronJob konnte um ".date("H:i:s")." am ".date("d.m.Y")." nicht ausgeführt werden","From: OL Zimmerberg<system@olzimmerberg.ch>");
    }
    echo "Kein Zugriff. <br>".(strtotime(date("Y-m-d H:i:s"))-strtotime(date("Y-m-d")))."<br>";
    $zugriff = "0";
}

if ($zugriff == "1") {
    
    echo "<table><tr><td>";
    
    include_once "parsers.php";
    
    if ($status=="Hinzufügen") {
        $sql = "INSERT into termine (datum,datum_end,datum_off,titel,text,typ,link,xkoord,ykoord,on_off,go2ol,solv_uid) VALUES ('".$_POST["datum"]."','".$_POST["datum_end"]."','".$_POST["datum_off"]."','".$_POST["titel"]."','".$_POST["text"]."','".$_POST["typ"]."','".$_POST["link"]."','".$_POST["xkoord"]."','".$_POST["ykoord"]."','".$_POST["on_off"]."','".$_POST["go2ol"]."','".$_POST["solv_uid"]."')";
        $db->query($sql);
        echo "<div style='position:absolute; margin-top:20px; background-color:#ffffff;'>".$sql."</div>";
    }
    /*
    if ($status=="Ändern") {
        echo arraytostr($_POST["uids"])."<br>";
        for ($j=0; $j<count($_POST["uids"]); $j++) {
            $texts = $_POST[md5($_POST["uids"][$j]."text")];
            echo "TEXTS: ".arraytostr($texts)."<br>";
            $text = "";
            if (is_array($texts)) {
                $text_keys = array_keys($texts);
                for ($i=0; $i<count($text_keys); $i++) {
                    $text_tmp = trim($texts[$text_keys[$i]]);
                    if ($text_tmp!="") {
                        if ($text!="") {
                            $text.="\n";
                        }
                        $text.=$text_tmp;
                    }
                }
                echo "TEXT: ".$text."<br>";
            }
            $koord = $_POST[md5($_POST["uids"][$j]."koord")];
            $sql_koord_tmp = "";
            if (isset($koord)) {
                $array = explode(" / ",$koord);
                $sql_koord_tmp = ", xkoord='".$array[0]."', ykoord='".$array[1]."'";
            }
            echo "KOORDS: ".$sql_koord_tmp."<br>";
            $links = $_POST[md5($_POST["uids"][$j]."link")];
            echo "LINKS: ".arraytostr($links)."<br>";
            $link = "";
            $link_keys = array_keys($links);
            for ($i=0; $i<count($link_keys); $i++) {
                $link_tmp = trim($links[$link_keys[$i]]);
                if ($link_tmp!="") {
                    if ($link!="") {
                        $link.="\n";
                    }
                    $array = explode(" / ",$link_tmp);
                    if (count($array)==2) {
                        $array[0] = strtolower($array[0]);
                        $linktype = "linkext";
                        if (strlen($array[0])-5 < strpos($array[0],".pdf")) {
                            $linktype = "linkpdf";
                        }
                        $link_tmp = "<div class='".$linktype."'><a href='".$array[0]."'>".$array[1]."</a></div>";
                    }
                    $link.=$link_tmp;
                }
            }
            echo "LINK: ".$link."<br>";
            $go2ols = $_POST[md5($_POST["uids"][$j]."go2ol")];
            echo "GO2OLS: ".arraytostr($go2ols)."<br>";
            $go2ol_sql_tmp = "";
            if (0<count($go2ols)) {
                $go2ol_sql_tmp = ", go2ol='".$go2ols[0]."'";
            }
            echo "GO2OL: ".$go2ol_sql_tmp."<br>";
            $meldeschlusse = $_POST[md5($_POST["uids"][$j]."meldeschluss")];
            echo "MELDESCHLÜSSE: ".arraytostr($meldeschlusse)."<br>";
            for ($i=0; $i<count($meldeschlusse); $i++) {
                $meldeschluss_tmp = explode(" / ",$meldeschlusse[$i]);
                $result = $db->query("SELECT * from termine WHERE solv_uid='".$_POST["uids"][$j]."'",$conn_id);
                $row = mysqli_fetch_array($result);
                print_r($meldeschluss_tmp);echo "<br>";print_r($row);
                echo "INSERT into termine (datum,datum_end,datum_off,newsletter,newsletter_datum,titel,text,link,typ,on_off) VALUES ('".date("Y-m-d",$meldeschluss_tmp[0])."','".date("Y-m-d",$meldeschluss_tmp[0])."','".date("Y-m-d",$meldeschluss_tmp[0])."','1','".date("Y-m-d",$meldeschluss_tmp[0]-86400*3)."','Meldeschluss ".$row["titel"]." [".$meldeschluss_tmp[1]."]','Laufdatum: ".date("d.m.Y",strtotime($row["datum"]))."','".addslashes("<div class='".$linktype."'><a href='#id".$row["id"]."'>Lauf</a></div>")."','meldeschluss".$_POST["uids"][$j]."','1')";
                $db->query("INSERT into termine (datum,datum_end,datum_off,newsletter,newsletter_datum,titel,text,link,typ,on_off) VALUES ('".date("Y-m-d",$meldeschluss_tmp[0])."','".date("Y-m-d",$meldeschluss_tmp[0])."','".date("Y-m-d",$meldeschluss_tmp[0])."','1','".date("Y-m-d",$meldeschluss_tmp[0]-86400*3)."','Meldeschluss ".$row["titel"]." [".$meldeschluss_tmp[1]."]','Laufdatum: ".date("d.m.Y",strtotime($row["datum"]))."','".addslashes("<div class='".$linktype."'><a href='#id".$row["id"]."'>Lauf</a></div>")."','meldeschluss".$_POST["uids"][$j]."','1')",$conn_id);
            }
            $sql = "UPDATE termine SET text='".$text."', link='".addslashes($link)."'".$sql_koord_tmp.$go2ol_sql_tmp." WHERE solv_uid='".$_POST["uids"][$j]."'";
            $db->query($sql,$conn_id);
        }
    }
    */
    if ($status=="IDs setzen") {
        $keys = array_keys($_POST);
        for ($i=0; $i<count($keys); $i++) {
            if (substr($keys[$i],0,3)=="olz") {
                $sql = "UPDATE termine SET solv_uid='".$_POST[$keys[$i]]."' WHERE id='".substr($keys[$i],3)."'";
                $db->query($sql,$conn_id);
            }
        }
    }
    /*
    $pathinfo = pathinfo($_FILES["import"]["name"]);
    if ($status=="CSV-Import" && strtolower($pathinfo["extension"])=="csv") {
        $path = $_FILES["import"]["tmp_name"];
        $fp = fopen($path,"r");
        $file = fread($fp,filesize($path));
        fclose($fp);
        $rowbreaks = array("\n","\r");
        $colbreaks = array(",",";");
        $rows = array();
        for ($i=0; $i<count($rowbreaks) && count($rows)<=1; $i++) {
            $rows = explode($rowbreaks[$i],$file);
            $rows_tmp = $rows;
            for ($j=0; $j<count($colbreaks) && count($rows_tmp[0])<=1; $j++) {
                $rows_tmp = $rows;
                for ($k=0; $k<count($rows_tmp); $k++) {
                    $rows_tmp[$k] = explode($colbreaks[$j],$rows_tmp[$k]);
                }
            }
        }
        $rows = $rows_tmp;
        $translation = array("datum"=>"datum", "beginn"=>"datum", "ende"=>"datum_end", "schluss"=>"datum_end", "ausschalten"=>"datum_off", "ausblenden"=>"datum_off", "off"=>"datum_off", "titel"=>"titel", "text"=>"text", "typ"=>"typ", "link"=>"link", "xkoord"=>"xkoord", "ykoord"=>"ykoord", "koordinaten"=>"koords", "aktiv"=>"on_off", "go2ol"=>"go2ol", "solv"=>"solv_uid", "newsletter"=>"newsletter");
        $expressions = array_keys($translation);
        $structure = array("datum"=>false, "datum_end"=>false, "datum_off"=>false, "titel"=>false, "text"=>false, "typ"=>false, "link"=>false, "xkoord"=>false, "ykoord"=>false, "koords"=>false, "on_off"=>false, "go2ol"=>false, "solv_uid"=>false, "newsletter"=>false);
        $headerfound = false;
        $error = "";
        $sql = array();
        for ($i=0; $i<count($rows); $i++) {
            if (!$headerfound) {
                for ($j=0; $j<count($rows[$i]); $j++) {
                    $content = trim(strtolower($rows[$i][$j]));
                    for ($k=0; $k<count($expressions); $k++) {
                        if (!is_bool(stristr($content,$expressions[$k]))) {
                            $structure[$translation[$expressions[$k]]] = $j;
                            $headerfound = true;
                        }
                    }
                }
            } else {
                $row = array("datum"=>"0000-00-00", "datum_end"=>"0000-00-00", "datum_off"=>"0000-00-00", "titel"=>"Untitled", "text"=>"", "typ"=>"ol", "link"=>"", "xkoord"=>"", "ykoord"=>"", "on_off"=>"1", "go2ol"=>"", "solv_uid"=>"0", "newsletter"=>"0");
                for ($j=0; $j<count($rows[$i]); $j++) {
                    $feld = array_search($j,$structure);
                    if (!is_bool($feld)) {
                        $row[$feld] = trim($rows[$i][$j]);
                        if ($feld=="koords") {
                            $coordinates = parse_map($row[$feld]);
                            $row["xkoord"] = $coordinates["x"];
                            $row["ykoord"] = $coordinates["y"];
                        }
                    }
                }
                $datum = date("Y-m-d",strtotime($row["datum"]));
                $datum_end = ($row["datum_end"]=="0000-00-00")?"0000-00-00":date("Y-m-d",strtotime($row["datum_end"]));
                $datum_off = ($row["datum_off"]=="0000-00-00")?"0000-00-00":date("Y-m-d",strtotime($row["datum_off"]));
                $titel = $row["titel"];
                $text = $row["text"];
                $typ = $row["typ"];
                $link = $row["link"];
                $xkoord = $row["xkoord"];
                $ykoord = $row["ykoord"];
                $on_off = ($row["on_off"])?1:0;
                $go2ol = $row["go2ol"];
                $solv_uid = $row["solv_uid"];
                $newsletter = ($row["newsletter"])?1:0;
                if (strtotime($datum)<0) {
                    $error .= "FEHLER in Reihe ".$i.": \"".$row["datum"]."\" ist kein gültiges Datum.<br>";
                }
                if (strtotime($datum_end)<0) {
                    $error .= "FEHLER in Reihe ".$i.": \"".$row["datum_end"]."\" ist kein gültiges Datum.<br>";
                }
                if (strtotime($datum_off)<0) {
                    $error .= "FEHLER in Reihe ".$i.": \"".$row["datum_off"]."\" ist kein gültiges Datum.<br>";
                }
                if (strlen($titel)<=0) {
                    $error .= "FEHLER in Reihe ".$i.": Kein Titel angegeben.<br>";
                }
                if (($xkoord<400000 || 900000<$xkoord) && $xkoord!="") {
                    $error .= "FEHLER in Reihe ".$i.": \"".$row["xkoord"]."\" ist keine gültige X-Koordinate (Schweizer Landeskoordinaten).<br>";
                }
                if (($ykoord<50000 || 400000<$ykoord) && $ykoord!="") {
                    $error .= "FEHLER in Reihe ".$i.": \"".$row["ykoord"]."\" ist keine gültige Y-Koordinate (Schweizer Landeskoordinaten).<br>";
                }
                if (!is_numeric($solv_uid)) {
                    $error .= "FEHLER in Reihe ".$i.": \"".$row["solv_uid"]."\" ist keine gültige SOLV-UID.<br>";
                }
                array_push($sql,"INSERT termine SET datum='$datum', datum_end='$datum_end', datum_off='$datum_off', titel='$titel', text='$text', typ='$typ', link='$link', xkoord='$xkoord', ykoord='$ykoord', on_off='$on_off', go2ol='$go2ol', solv_uid='$solv_uid', newsletter='$newsletter'");
            }
        }
        if (0<strlen($error)) {
            echo $error;
        } else {
            for ($i=0; $i<count($sql); $i++) {
                $db->query($sql[$i]);
                echo $sql[$i]."<br>";
            }
        }
    }
    */
    
    // ---
//echo "***";    
    
    $rechts = "";
    
    if ($_GET["mode"]=="show") {
        $_SESSION["termine_helper"] = "show";
    }
    
    if ($_GET["mode"]=="import") {
        $_SESSION["termine_helper"] = "import";
    }
    
    if ($_GET["mode"]=="check") {
        $_SESSION["termine_helper"] = "check";
    }
    
    if ($_GET["mode"]=="compare") {
        $_SESSION["termine_helper"] = "compare";
    }
    
    if ($_GET["mode"]=="add") {
        $_SESSION["termine_helper"] = "add";
        $_SESSION["termine_helper_add_step"] = "0";
        $year = date("Y");
        if (8<date("m")) $year++;
        echo "YEAR: ".$year."<br>";
        $_SESSION["termine_helper_add_termine"] = solvdataforyear($year);
    }
    
    if ($_GET["mode"]=="solvuids" OR $mode=="Alle zeigen") {
        $_SESSION["termine_helper"] = "solvuids";
        //$_SESSION["termine_helper_solvuids_termine"] = solvdataforyear(false);
        $alle_zeigen = ($mode=="Alle zeigen");
    }
    
    
    // SHOW
    if ($_SESSION["termine_helper"]=="show") {
        $sql = "select * from termine WHERE solv_uid!='0' ORDER BY datum DESC";
        // DB-ABFRAGE
        $result = $db->query($sql);
        
        echo "<table class='liste'>";
        while ($row = mysqli_fetch_array($result))
        {$datum = $row['datum'];
            $datum_end = $row['datum_end'];
            $titel = $row['titel'];
            $text = $row['text'];
            $text = olz_mask_email($text,"","");
            $link = $row['link'];
            $id = $row['id'];
            $on_off = $row['on_off'];
            $newsletter = $row['newsletter'];
            $datum_anmeldung = $row['datum_anmeldung'];
            $xkoord = $row['xkoord'];
            $ykoord = $row['ykoord'];
            $go2ol = $row['go2ol'];
            
            if ($datum_end == "0000-00-00") $datum_end = $datum;
            if ($titel > "") $text = "<b>".$titel."</b><br>".$text;
            if ($link == "") $link = "&nbsp;";
            else $link = str_replace("&","&amp;",str_replace("&amp;","&",$link));
            $link = str_replace("www.solv.ch","www.o-l.ch",$link);
            
            if (($datum_anmeldung!='0000-00-00') AND ($datum_anmeldung<>'') AND ($zugriff) AND ($datum_anm>$heute))
        {$link = "<div class='linkint'><a href='index.php?page=13&amp;id_anm=$id'>Online-Anmeldung</a></div>".$link; }
            
            if ($newsletter) $icn_newsletter = "<img src='icns/mail2.gif' class='noborder' style='margin-left:4px;vertical-align:top;' title='Newsletter-Benachrichtigung' alt=''>";
            else $icn_newsletter = "";
            
            //Tagesanlass
            if (($datum_end==$datum) OR ($datum_end=="0000-00-00"))
        {$datum_tmp = olz_date("t. MM ",$datum).olz_date(" (W)",$datum);}
            //Mehrtägig innerhalb Monat
            elseif (olz_date ("m",$datum)==olz_date("m",$datum_end))
        {$datum_tmp =olz_date("t.-",$datum). olz_date("t. ",$datum_end). olz_date("MM",$datum).olz_date(" (W-",$datum).olz_date("W)",$datum_end);}
            //Mehrtägig monatsübergreifend
        else {$datum_tmp = olz_date("t.m.-",$datum). olz_date("t.m. ",$datum_end). olz_date("jjjj",$datum).olz_date(" (W-",$datum).olz_date("W)",$datum_end);}
            
            if ($on_off==0) $class = " class='off'";
            elseif ($datum_end < $heute) $class = " class='passe'";
            else $class = "";
            
            // HTML-Ausgabe
            if (($xkoord > 0) AND ($datum_end > $heute))
        {$maplink = "<div id='map_$id'><a href='http://map.search.ch/$xkoord,$ykoord' target='_blank' onclick=\"map('$id',$xkoord,$ykoord);return false;\" class='linkmap'>Karte zeigen</a></div>";}
            else
        {$maplink = "";}
            if ((0 < strlen($go2ol)) AND ($datum_end > $heute))
        {$go2ollink = "<div><a href='http://www.go2ol.ch/".$go2ol."' target='_blank' class='linkext'>GO2OL</a></div>";}
            else
        {$go2ollink = "";}
            echo olz_monate($datum)."<tr".$class.">\n\t<td id='id".$id."' style='width:25%;'>".$datum_tmp.$icn_newsletter."</td><td style='width:55%;'$id_spalte>".$text."<div id='map$id' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div></td><td style='width:20%;'>".$maplink.$go2ollink.$link."</td>\n</tr>\n";
        }
        echo "</table>";
    }
    
    // IMPORT
    /*
    if ($_SESSION["termine_helper"]=="import") {
        echo "<table class='raster'>
        <tr><td style='width:30%;'>Datum (Beginn)</td><td style='width:70%;'><input type='text' name='datum' value='' style='width:10em;'></td></tr>
        <tr><td>Datum (Ende)</td><td><input type='text' name='datum_end' value='' style='width:10em;'></td></tr>
        <tr><td>Datum (Ausschalten)</td><td><input type='text' name='datum_off' value='' style='width:10em;'></td></tr>
        <tr><td>Titel</td><td><input type='text' name='titel' value='' style='width:90%;'></td></tr>
        <tr><td>Text</td><td><textarea name='text' style='width:90%; height:5em;'></textarea></td></tr>
        <tr><td>Typ</td><td><input type='text' name='typ' value='ol' style='width:10em;'><input type='checkbox'>Klubanlass</td></tr>
        <tr><td>Link (wird später automatisch aktualisiert)</td><td><textarea name='link' style='width:90%; height:5em;'></textarea></td></tr>
        <tr><td>Koordinaten</td><td>X:<input type='text' name='xkoord' value='' style='width:7em;'> - Y:<input type='text' name='ykoord' value='' style='width:7em;'></td></tr>
        <tr><td>Aktiv</td><td><input type='checkbox' name='on_off' value='1' checked></td></tr>
        <tr><td>GO2OL Code</td><td><input type='text' name='go2ol' value='' style='width:10em;'></td></tr>
        <tr><td>SOLV UID</td><td><input type='text' name='solv_uid' value='' style='width:10em;'></td></tr>
        <tr><td>Newsletter</td><td><input type='checkbox' name='on_off' value='1'></td></tr>
        </table>";
        echo "<h2>Import einer CSV-Datei</h2><div>Erschaffe eine CSV-Datei nach dem folgenden Muster(z.B. mit Excel, dann Exportieren):<br>In der ersten Reihe (Zeile) stehen die Feldbezeichnungen:<br>- Datum (z.B. 22.5.2010)<br>- Ende (z.B. 24.5.2010)<br>- Ausblenden (z.B. 25.5.2010)<br>- Titel<br>- Text<br>- Typ (z.B. ol training)<br>- Link (HTML-Code)<br>- Koordinaten (z.B. MapSearch-Link)<br>- Aktiv (0 oder 1)<br>- GO2OL (das GO2OL-Kürzel)<br>- SOLV (die SOLV unique ID)<br>- Newsletter (0 oder 1)</div><input type='file' name='import'><input type='submit' name='status' value='CSV-Import'>";
    }
    */
    
    // CHECK
    
    if ($_SESSION["termine_helper"]=="check") {
        $infos = "";
        $console = "";
        $solv = solvdataforyear(false);
        $solvbyid = array();
        for ($i=0; $i<count($solv); $i++) {
            $result = $db->query("SELECT id FROM termine WHERE solv_uid='".intval($solv[$i]["uniqueid"])."'", $conn_id);
            if (0<mysqli_num_rows($result)) $solvbyid[$solv[$i]["uniqueid"]] = $solv[$i];
        }
        print_r(array_keys($solvbyid));
        echo "<br><br>";
        
        $go2ol = go2oldata();
        $go2olbyid = array();
        for ($i=0; $i<count($go2ol); $i++) {
            $result = $db->query("SELECT id FROM termine WHERE solv_uid='".intval($go2ol[$i]["solv_uid"])."'", $conn_id);
            if (0<mysqli_num_rows($result)) $go2olbyid[$solv[$i]["uniqueid"]] = $go2ol[$i];
        }
        print_r(array_keys($go2olbyid));
        echo "<br><br>";
        
        $result = $db->query("SELECT * FROM termine WHERE solv_uid IN ('".implode("', '", array_merge(array_keys($solvbyid), array_keys($go2olbyid)))."')", $conn_id);
        $num = mysqli_num_rows($result);
        for ($i=0; $i<$num; $i++) {
            $row = mysqli_fetch_array($result);
            //print_r($row);echo "<br><br>";
        }

        //mail("simon.hatt@olzimmerberg.ch","Terminaktualisierungen OL Zimmerberg","Update ausgeführt","From: OL Zimmerberg<system@olzimmerberg.ch>");
        //mail("simon.hatt@olzimmerberg.ch","Terminaktualisierungen OL Zimmerberg","Einloggen: http://www.olzimmerberg.ch/index.php?page=10\nAlle Updaten:\n".$update_all_link."\n".$infos."\n\n--------------------------------------------------\n\n\n".$console,"From: OL Zimmerberg<system@olzimmerberg.ch>");
    }
    
    // COMPARE
    /*
    if ($_SESSION["termine_helper"]=="compare") {
        echo "<table class='liste'>
<tr><td style='background-color:#cccccc;'></td><td style='background-color:#cccccc; width:40%; text-align:center;'><h3><img src='favicon.gif' alt='' class='noborder'> OLZ</h3></td><td style='background-color:#cccccc; width:40%; text-align:center;'><h3><img src='icns/ol.gif' alt='' class='noborder'> SOLV</h3></td></tr>";
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
    
    if ($_SESSION["termine_helper"]=="add") {
        if ($status=="Weiter") {
            $_SESSION["termine_helper_add_step"] += 1;
        }
        if ($status=="Zurück") {
            $_SESSION["termine_helper_add_step"] -= 1;
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
        if ($i<count($solv_termine)) {
            $class = "no";
            $checked = "";
            $abkletters = array("S","O","M");
            for ($j=0; $j<count($abkletters); $j++) {
                if (!is_bool(strpos($solv_termine[$i]["abk"],strtoupper($abkletters[$j]))) || !is_bool(strpos($solv_termine[$i]["abk"],strtolower($abkletters[$j])))) {
                    $class = "maybe";
                }
            }
            if (!is_bool(strpos($solv_termine[$i]["abk"],"**A")) || $solv_termine[$i]["region"]=="ZH/SH") {
                $class = "yes";
                $checked = " checked";
            }
            echo "<div class='bg$class'><div class='$class' style='font-weight:bold; padding:5px;'>".date("d.m.Y",$solv_termine[$i]["datum"])." - ".$solv_termine[$i]["name"]." - ".$solv_termine[$i]["region"]." - ".$solv_termine[$i]["verein"]." - ".$solv_termine[$i]["karte"]." - ".$solv_termine[$i]["abk"]."</div>";
            $coordinates = parse_map($solv_termine[$i]["map"]);
            echo "<table class='raster'>
<tr><td style='width:30%;'>Datum (Beginn)</td><td style='width:70%;'><input type='text' name='datum' value='".date("Y-m-d",$solv_termine[$i]["datum"])."' style='width:10em;'></td></tr>
<tr><td>Datum (Ende)</td><td><input type='text' name='datum_end' value='".date("Y-m-d",$solv_termine[$i]["datum"])."' style='width:10em;'></td></tr>
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
    
    if ($_SESSION["termine_helper"]=="solvuids") {
		$year = date("Y");
        if (8<date("m")) $year++;
        $sql_tmp = ($alle_zeigen) ? "" : "(solv_uid='0' OR solv_uid IS NULL) AND" ;
        $sql = "SELECT * FROM termine WHERE $sql_tmp datum>='".$year."-01-01' AND datum<='".$year."-12-31' AND (typ LIKE '%ol%') AND (titel NOT LIKE '%Meldeschluss%')";
        $result = $db->query($sql,$conn_id);
echo $year."***";
        echo "<table style='border-collapse:collapse;' cellspacing='0' class='liste'><thead><tr><td>OLZ Termin</td><td>SOLV Termin</td><td>ID</td></tr></thead>";
        while ($row = mysqli_fetch_array($result)) {
            $solv_uid = ($row['solv_uid']>0) ? $row['solv_uid'] : "" ;
            $matching = array();
            $maxsimval = -1;
            $maxsimind = -1;
            $sql_solv = "SELECT * FROM termine_solv WHERE date='".$row["datum"]."'";
            $result_solv = $db->query($sql_solv,$conn_id);
            $num_solv = mysqli_num_rows($result_solv);
            for ($i=0; $i<$num_solv; $i++) {
                $row_solv = mysqli_fetch_array($result_solv);
                $sim = is_similar($row["titel"], $row_solv["event_name"]);
                array_push($matching,array($row_solv,$sim));
                if ($maxsimval<$sim || $maxsimval==-1) {
                    $maxsimval = $sim;
                    $maxsimind = count($matching)-1;
                }
            }
            
            if (0<count($matching)) {
                $color = ($solv_uid>0)? "#c6ff8e" : "#fdb4b5" ;
                echo "<tr><td style='background-color:$color;width:270px;'>".olz_date('t.m.jj / ',$row['datum']).$row["titel"]."</td><td style='background-color:$color;width:100px;'>";
                echo "<select name='olz".$row["id"]."' style='width:280px;'><option value='0'>---</option>";
                for ($i=0; $i<count($matching); $i++) {
                    $selected = "";
                    if ($maxsimind == $i && 0.0002<$maxsimval) {
                        $selected = " selected";
                    }
                    $similarity = $matching[$i][1];
                    echo "<option value='".$matching[$i][0]["solv_uid"]."'".$selected.">".round($similarity*100,0)." - ".$matching[$i][0]["event_name"]."</option>";
                }
                echo "</select></td><td style='padding-left:4px;width:30px;background-color:$color;'>$solv_uid</td>";
            } else {
                echo "<tr><td>".olz_date('t.m.jj / ',$row['datum']).$row["titel"]."</td><td></td><td></td>";
            }
            echo "</tr>\n";
        }
        echo "</table><p><input type='submit' name='status' value='IDs setzen' class='dropdown'><input type='submit' name='mode' value='Alle zeigen' style='margin-left:10px;' class='dropdown'></p>";
    }
    
    echo "</td><td style='width:10px;'></td><td style='width:25%;'>";
    
    echo $rechts;
    echo "<h2>Termine-Tools</h2><h3><a href='?mode=show' class='linkint'>OLZ Termine Anzeigen</a></h3>OLZ Termine, die mit dem SOLV synchronisiert werden, anzeigen<div style='opacity:0.5;'><h3><a href='?mode=import' class='linkint'>CSV Import</a></h3>OLZ Termine aus CSV-Datei hinzufügen</div><h3><a href='?mode=add' class='linkint'>SOLV Import</a></h3>Termine vom SOLV neu hinzufügen<h3><a href='?mode=solvuids' class='linkint'>SOLV IDS vergeben</a></h3>OLZ-Terminen SOLV-Termine zuordnen<div style='opacity:0.5;'><h3><a href='?mode=check' class='linkint'>SOLV Prüfen</a></h3>OLZ-Termine, denen eine SOLV-ID zugeordnet ist, überprüfen; Mail verschicken<h3><a href='?mode=compare' class='linkint'>SOLV Vergleichen/Anpassen</a></h3>OLZ-Termine und SOLV-Termine synchronisieren<br>Typischerweise durch Link im Mail</div>";
    echo "</td></tr></table>";
}


// ---

// FUNCTIONS

function is_similar($str1, $str2) {
    $str1 = trim(strtolower($str1));
    $str2 = trim(strtolower($str2));
    $maxlen = strlen($str1)+strlen($str2);
    $diff = levenshtein($str1, $str2)/$maxlen;
    return 1-$diff;
}
/*
function compare_uid($uid,$go2ol=array()) {
    global $conn_id;
    $infos = "";
    $console = "";
    $needsupdate = false;
    $compare_results = array();
    $result = $db->query("SELECT * FROM termine WHERE solv_uid='".$uid."' LIMIT 1",$conn_id);
    $olz = mysqli_fetch_array($result);
    $solv = solvdataforuid($uid);

    if (!is_array($olz) || !is_array($solv) || count($olz)==0 || count($solv)==0) {
        return false;
    }
    $compare_results["olz"] = $olz;
    $compare_results["solv"] = $solv;
    
    $dashes = "";
    $len = (100-strlen($olz["titel"]))/2;
    for ($i=0; $i<$len; $i++) {
        $dashes.="-";
    }
    $infos .= $dashes.$olz["titel"].$dashes."\n";
    $console .= $dashes.$olz["titel"].$dashes."\n\n";
    $console .= "OLZ: ".arraytostr($olz)."\n\n";
    $console .= "SOLV: ".arraytostr($solv)."\n\n";
    
    // SOLV ("datum","meldeschluss","region","abk","links"=>("href","text"),"name","verein","karte","map","rangliste","startliste")
    $link_compare = array();
    $link_compare["all_links"] = array();
    $link_compare["olz_links"] = array();
    $link_compare["solv_links"] = array();
    $olzlinks = parse_links($olz["link"]);
    // Rangliste
    if ($solv["rangliste"]) {
        $exists = false;
        for ($i=0; $i<count($olzlinks) && !$exists; $i++) {
            if (stristr($olzlinks[$i]["text"],"rangliste") || stristr($olzlinks[$i]["text"],"resultate")) {
                $exists = true;
            }
            if (strtolower($olzlinks[$i]["href"])==strtolower($solv["rangliste"])) {
                $exists = true;
            }
        if ($exists) {array_splice($olzlinks,$i,1);}
        }
        array_push($link_compare["all_links"],array("href"=>"http://www.o-l.ch/cgi-bin/results?type=rang&unique_id=".$uid."&club=OL+Zimmerberg","text"=>"Rangliste"));
        $linkid = count($link_compare["all_links"])-1;
        array_push($link_compare["solv_links"],$linkid);
        if (!$exists) {
            $infos .= "Rangliste verfügbar\n";
            $needsupdate = true;
        } else {
            array_push($link_compare["olz_links"],$linkid);
        }
    }
    // Startliste
    if ($solv["startliste"]) {
        $exists = false;
        for ($i=0; $i<count($olzlinks) && !$exists; $i++) {
            if (stristr($olzlinks[$i]["text"],"startliste")) {
                $exists = true;
            }
            if (strtolower($olzlinks[$i]["href"])==strtolower($solv["startliste"])) {
                $exists = true;
            }
        if ($exists) {array_splice($olzlinks,$i,1);}
        }
        array_push($link_compare["all_links"],array("href"=>"http://www.o-l.ch/cgi-bin/results?type=start&unique_id=".$uid."&club=OL+Zimmerberg","text"=>"Startliste"));
        $linkid = count($link_compare["all_links"])-1;
        array_push($link_compare["solv_links"],$linkid);
        if (!$exists) {
            $infos .= "Startliste verfügbar\n";
            $needsupdate = true;
        } else {
            array_push($link_compare["olz_links"],$linkid);
        }
    }
    // Andere Links (v.a. Ausschreibung)
    if (0<count($solv["links"])) {
        for ($j=0; $j<count($solv["links"]); $j++) {
            $exists = false;
            for ($i=0; $i<count($olzlinks) && !$exists; $i++) {
                if (strtolower($olzlinks[$i]["text"])==strtolower($solv["links"][$j]["text"])) {
                    $exists = true;
                }
                if (strtolower($olzlinks[$i]["href"])==strtolower($solv["links"][$j]["href"])) {
                    $exists = true;
                }
            if ($exists) {array_splice($olzlinks,$i,1);}
            }
            array_push($link_compare["all_links"],array("href"=>$solv["links"][$j]["href"],"text"=>$solv["links"][$j]["text"]));
            $linkid = count($link_compare["all_links"])-1;
            array_push($link_compare["solv_links"],$linkid);
            if (!$exists) {
                $infos .= $solv["links"][$j]["text"]." verfügbar\n";
                $needsupdate = true;
            } else {
                array_push($link_compare["olz_links"],$linkid);
            }
        }
    }
    for ($i=0; $i<count($olzlinks); $i++) {
        array_push($link_compare["all_links"],array("href"=>$olzlinks[$i]["href"],"text"=>$olzlinks[$i]["text"]));
        $linkid = count($link_compare["all_links"])-1;
        array_push($link_compare["olz_links"],$linkid);
    }
    $compare_results["links"] = $link_compare;
    // Text - Veranstalter, Karte, Region
    $text_compare = array();
    $text_compare["all_texts"] = array();
    $text_compare["olz_texts"] = array();
    $text_compare["solv_texts"] = array();
    if (0<strlen($olz["text"])) {
        array_push($text_compare["all_texts"],array($olz["text"]));
        $textid = count($text_compare["all_texts"])-1;
        array_push($text_compare["olz_texts"],$textid);
    }
    if (0<strlen($solv["verein"])) {
        array_push($text_compare["all_texts"],array($solv["verein"]));
        $textid = count($text_compare["all_texts"])-1;
        array_push($text_compare["solv_texts"],$textid);
    }
    if (0<strlen($solv["karte"])) {
        array_push($text_compare["all_texts"],array($solv["karte"]));
        $textid = count($text_compare["all_texts"])-1;
        array_push($text_compare["solv_texts"],$textid);
    }
    if (0<strlen($solv["region"])) {
        array_push($text_compare["all_texts"],array($solv["region"]));
        $textid = count($text_compare["all_texts"])-1;
        array_push($text_compare["solv_texts"],$textid);
    }
    if (strlen($olz["text"])<3) {
        if (3<strlen($solv["verein"].$solv["karte"].$solv["region"])) {
            $infos .= "Text verfügbar: ".$solv["verein"]." - ".$solv["karte"]." - ".$solv["region"]."\n";
        }
    }
    $compare_results["texts"] = $text_compare;
    // Koordinaten (map.search.ch)
    $koord_compare = array();
    $koord_compare["all_koords"] = array();
    $koord_compare["olz_koords"] = array();
    $koord_compare["solv_koords"] = array();
    if ($olz["xkoord"]!=0 && $olz["ykoord"]!=0) {
        array_push($koord_compare["all_koords"],array("x"=>$olz["xkoord"],"y"=>$olz["ykoord"]));
        $koordid = count($koord_compare["all_koords"])-1;
        array_push($koord_compare["olz_koords"],$koordid);
    }
    if ($solv["map"]["x"]!=0 && $solv["map"]["y"]!=0) {
        array_push($koord_compare["all_koords"],array("x"=>$solv["map"]["x"],"y"=>$solv["map"]["y"]));
        $koordid = count($koord_compare["all_koords"])-1;
        array_push($koord_compare["solv_koords"],$koordid);
    }
    if ($olz["xkoord"]==0 && $olz["ykoord"]==0) {
        if ($solv["map"]["x"]!=0 && $solv["map"]["y"]!=0) {
            $infos .= "Koordinaten verfügbar: ".$solv["map"]["x"]." / ".$solv["map"]["y"]."\n";
            $needsupdate = true;
        }
    }
    $compare_results["koords"] = $koord_compare;
    // Meldeschluss
    $go2ol_compare = array();
    $go2ol_compare["all_go2ols"] = array();
    $go2ol_compare["olz_go2ols"] = array();
    $go2ol_compare["solv_go2ols"] = array();
    $meldeschluss_compare = array();
    $meldeschluss_compare["all_meldeschlusse"] = array();
    $meldeschluss_compare["olz_meldeschlusse"] = array();
    $meldeschluss_compare["solv_meldeschlusse"] = array();
    if ($olz["go2ol"]) {
        array_push($go2ol_compare["all_go2ols"],array($olz["go2ol"]));
        $go2olid = count($go2ol_compare["all_go2ols"])-1;
        array_push($go2ol_compare["olz_go2ols"],$go2olid);
    }
    for ($i=0; $i<count($go2ol); $i++) {
        if ($olz["solv_uid"] == $go2ol[$i]["solv_uid"]) {
            //echo "---".$solv["name"]."---<br>";
            $go2olcode = parse_go2olcode($go2ol[$i]["link"]);
            if ($olz["go2ol"] != $go2olcode) {
                if (strlen($olz["go2ol"])==0) {
                    $infos .= "GO2OL Code verfügbar: ".$go2olcode."\n";
                    $needsupdate = true;
                }
                array_push($go2ol_compare["all_go2ols"],array($go2olcode));
                $go2olid = count($go2ol_compare["all_go2ols"])-1;
            } else {
                $go2olid = 0;
            }
            array_push($go2ol_compare["solv_go2ols"],$go2olid);
            for ($j=0; $j<count($go2ol[$i]["meldeschluss"]); $j++) {
                $result = $db->query("SELECT * FROM termine WHERE datum='".date("Y-m-d",$go2ol[$i]["meldeschluss"][$j])."' AND typ LIKE '%meldeschluss".$olz["solv_uid"]."%'",$conn_id);
                $num = mysqli_num_rows($result);
                if ($j==0) {
                    if (count($go2ol[$i]["meldeschluss"])==1) {
                        $typ = "klassisch &amp; elektronisch";
                    } else {
                        $typ = "klassisch";
                    }
                } else {
                    $typ = "elektronisch";
                }
                if (0<$num) {
                    array_push($meldeschluss_compare["all_meldeschlusse"],array("datum"=>$go2ol[$i]["meldeschluss"][$j],"typ"=>$typ));
                    $meldeschlussid = count($meldeschluss_compare["all_meldeschlusse"])-1;
                    array_push($meldeschluss_compare["olz_meldeschlusse"],$meldeschlussid);
                } else {
                    array_push($meldeschluss_compare["all_meldeschlusse"],array("datum"=>$go2ol[$i]["meldeschluss"][$j],"typ"=>$typ));
                    $meldeschlussid = count($meldeschluss_compare["all_meldeschlusse"])-1;
                    array_push($meldeschluss_compare["solv_meldeschlusse"],$meldeschlussid);
                    $infos .= "Meldeschluss verfügbar: ".$go2ol[$i]["meldeschluss"][$j]."\n";
                    $needsupdate = true;
                }
            }
        }
    }
    
    ---
    
    if ($solv["meldeschluss"]) {
        $result = $db->query("SELECT * FROM termine WHERE datum='".date("Y-m-d",$solv["meldeschluss"])."'",$conn_id);
        $num = mysqli_num_rows($result);
        if ($num == 0) {
            echo $solv["name"]." - ".date("Y-m-d",$solv["meldeschluss"])." - KEIN OLZ-MELDESCHLUSS<br>";
        }
        while($row = mysqli_fetch_array($result)) {
            if (!is_bool(stristr($row["titel"],"Meldeschluss"))) {
                echo $solv["name"]." - ".date("Y-m-d",$solv["meldeschluss"])." - ".$row["titel"]." - TRUSTED<br>";
            } else {
                echo $solv["name"]." - ".date("Y-m-d",$solv["meldeschluss"])." - ".$row["titel"]." - NOT TRUSTED<br>";
            }
        }
        for ($i=0; $i<count($go2ol); $i++) {
            if (($go2ol[$i]["meldeschluss"][0] <= $solv["meldeschluss"] && $solv["meldeschluss"] <= $go2ol[$i]["meldeschluss"][1]) || ($go2ol[$i]["meldeschluss"][1] <= $solv["meldeschluss"] && $solv["meldeschluss"] <= $go2ol[$i]["meldeschluss"][0])) {
                if (strtotime($olz["datum"]) <= $go2ol[$i]["datum"] && $go2ol[$i]["datum"] <= strtotime($olz["datum_end"])) {
                    $go2olcode = parse_go2olcode($go2ol[$i]["link"]);
                    if ($olz["go2ol"] != $go2olcode) {
                        if (strlen($olz["go2ol"])==0) {
                            $infos .= "GO2OL Code verfügbar: ".$go2olcode."\n";
                            $needsupdate = true;
                        }
                        array_push($go2ol_compare["all_go2ols"],array($go2olcode));
                        $go2olid = count($go2ol_compare["all_go2ols"])-1;
                    } else {
                        $go2olid = 0;
                    }
                    array_push($go2ol_compare["solv_go2ols"],$go2olid);
                }
            }
        }
    }
    
    ---
    
    $compare_results["go2ols"] = $go2ol_compare;
    $compare_results["meldeschlusse"] = $meldeschluss_compare;
    $compare_results["needsupdate"] = $needsupdate;
    $compare_results["infos"] = $infos;
    $compare_results["console"] = $console;
    return $compare_results;
}
*/
echo "Dauer: ".(microtime(1)-$start)
?>