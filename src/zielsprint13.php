<?php

//echo "<p style='color:#ff0000;'>Wir arbeiten daran. Das POLITT-Büro</p>";

echo "<style type='text/css'>
sup, sub {
    font-size:0.8em;
    height: 0;
    line-height: 1;
    vertical-align: baseline;
    position: relative;
}

sup {
    bottom: .8ex;
}

sub {
    top: .5ex;
}
</style>
<script type='text/javascript'>
function toggle(id) {
    var elem = document.getElementById(id);
    if (elem.style.display==\"none\") {
        elem.style.display = \"table-row\";
    } else {
        elem.style.display = \"none\";
    }
}
</script>
<h2>Zielsprint-Cup 2013 <span style='font-size:10px; font-weight:normal; padding-left:30px;'><a href='pdf/OLZ-Zielsprint-Meisterschaft-2013.pdf' class='linkpdf'>Reglement</a></span><span style='font-size:10px; font-weight:normal; padding-left:30px;'><script>document.write(MailTo(\"simon.hatt\",\"olzimmerberg.ch\",\"POLITT-Büro\",\"Lauf vergessen\"));</script></span></h2>";

if ($_SESSION["auth"]=="all") {
    if (isset($_GET["year"]) && isset($_GET["event"])) {
        echo "<a href='?year=".$_GET["year"]."'>Zurück</a><br>";
        if ($_GET["action"]=="reevaluate") {
            $result_l = mysql_query("SELECT id from solvlaufe WHERE jahr LIKE '".intval($_GET["year"])."' AND name LIKE '".mysql_real_escape_string($_GET["event"])."'");
            $num_l = mysql_num_rows($result_l);
            if ($num_l==1) {
                $row_l = mysql_fetch_array($result_l);
                mysql_query("DELETE from solvlaufe WHERE id='".$row_l["id"]."'");
                mysql_query("DELETE from solvresults WHERE lauf='".$row_l["id"]."'");
            }
        }
        $result_l = mysql_query("SELECT id from solvlaufe WHERE jahr LIKE '".intval($_GET["year"])."' AND name LIKE '".mysql_real_escape_string($_GET["event"])."'");
        $num_l = mysql_num_rows($result_l);
        if ($num_l==1) {
            $row_l = mysql_fetch_array($result_l);
            $lauf = $row_l["id"];
            echo "LAUF FOUND: ".$lauf."<br>";
        } else {
            mysql_query("INSERT solvlaufe SET jahr='".intval($_GET["year"])."', name='".mysql_real_escape_string($_GET["event"])."'");
            $lauf = mysql_insert_id();
            echo "LAUF INSERTED: ".$lauf."<br>";
            $url = "http://www.o-l.ch/cgi-bin/results?type=rang&year=".intval($_GET["year"])."&event=".urlencode($_GET["event"])."&kat=--&kind=club&club=OL+Zimmerberg&zwizt=1";
            echo "<input type='text' value='".str_replace("'","&#39;",$url)."' style='width:100%;' readonly='readonly'><br>";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $file = curl_exec($ch);
            curl_close($ch);
            
            $pres = array();
            $pre = "";
            $num = strlen($file);
            for ($i=0; $i<$num; $i++) {
                if (substr($file,$i,6)=="</pre>") {
                    $inpre = false;
                    array_push($pres,$pre);
                }
                if ($inpre) {
                    $pre .= $file[$i];
                }
                if (substr($file,$i,5)=="<pre>") {
                    $inpre = true;
                    $pre = "";
                    $i += 4;
                }
            }
            $wertung = array();
            $order = array();
            $fehler = "";
            for ($i=0; $i<$num; $i++) {
                $status = 3;
                $num = count($pres);
                $bs = array();
                $conts = array();
                $dist = "";
                $tmp = "";
                $len = strlen($pres[$i]);
                for ($j=0; $j<$len; $j++) {
                    if (substr($pres[$i],$j,4)=="</b>") {
                        if ($status==3) $dist = $tmp;
                        else array_push($bs,$tmp);
                        $status = 1;
                        $tmp = "";
                        $j += 3;
                        continue;
                    }
                    if (substr($pres[$i],$j,3)=="<b>") {
                        if ($status==3) $dist = $tmp;
                        else array_push($conts,$tmp);
                        $status = 2;
                        $tmp = "";
                        $j += 2;
                        continue;
                    }
                    if (0<$status) {
                        $tmp .= $pres[$i][$j];
                    }
                }
                if ($status==1) {
                    if ($tmp!="") array_push($conts,$tmp);
                }
                $res = preg_match("/\(\s*([0-9\.]+)\s*km\s*,\s*([0-9]+)\s*m\s*,\s*([0-9]+)\s*Po\.\s*\)\s*([0-9]+)\s*Teilnehmer/",$dist,$matches);
                $strecke = $matches[1];
                $hoehe = $matches[2];
                $posten = $matches[3];
                $teilnehmer = $matches[4];
                $linm = ($strecke*1000+$hoehe);
                $siegerzeitins = -1;
                for ($j=0; $j<count($bs) && $j<count($conts); $j++) {
                    $moreinfo = "";
                    $moreinfo .= "STRECKE: ".$strecke."km - H&Ouml;HE: ".$hoehe."m => LM: <b>".$linm."</b>m - POSTEN: ".$posten." - TEILNEHMER: ".$teilnehmer."<br>\n";
                    $res = preg_match("/^\s*(\s*([0-9]+)\.)?\s*(((?![\s]{2,})[^0-9])+)\s+([0-9]{2}|)\s+(((?![\s]{2,})[\S ])+)\s+((((?![\s]{2,})[\S ])+)\s+)?([0-9:]+|[A-Za-z\.\/ ]+)\s*$/",$bs[$j],$matches);
                    if ($res) {
                        $rang = $matches[2];
                        $name = $matches[3];
                        $jahrgang = $matches[5];
                        $wohnort = $matches[6];
                        $club = $matches[9];
                        $zeit = $matches[11];
                        $zeitins = zeitins($zeit);
                        if ($siegerzeitins==-1) $siegerzeitins = $zeitins;
                        $abgefedertezeitins = $zeitins==0?0:(atan(($zeitins/$siegerzeitins-1)*2)/2+1)*$siegerzeitins;
                        $moreinfo .= "RANG: ".trim($rang)." - NAME: ".trim($name)." - JAHRGANG: ".trim($jahrgang)." - WOHNORT: ".trim($wohnort)." - CLUB: ".trim($club)." - LAUF-ZEIT: <b>".$zeit."</b><br>\n";
                        $haszimmer = preg_match("/[Zz]immer/",$wohnort.$club);
                        if ($haszimmer) {
                            $result_p = mysql_query("SELECT * from solvpeople WHERE name LIKE '".trim($name)."' AND jahrgang LIKE '".trim($jahrgang)."' AND wohnort LIKE '".trim($wohnort)."'");
                            $num_p = mysql_num_rows($result_p);
                            if ($num_p==1) {
                                $row_p = mysql_fetch_array($result_p);
                                $person = $row_p["id"];
                                echo "PERSON FOUND: ".$person."<br>";
                            } else {
                                mysql_query("INSERT solvpeople SET name='".trim($name)."', jahrgang='".trim($jahrgang)."', wohnort='".trim($wohnort)."'");
                                $person = mysql_insert_id();
                                echo "PERSON INSERTED: ".$person."<br>";
                            }
                            $zipos = strrpos($conts[$j],"Zi");
                            $enlpos = strrpos($conts[$j],"\n",$zipos-strlen($conts[$j])-1);
                            $bnlpos = strrpos($conts[$j],"\n",$enlpos-strlen($conts[$j])-1);
                            $lastline = substr($conts[$j],$bnlpos+1,$enlpos-$bnlpos-1);
                            $res = preg_match("/\s*([0-9\.]+)\s*\(\s*([0-9]*)\s*\)\s*$/",$lastline,$matches);
                            if ($res) {
                                $zeitv = $matches[1];
                                $rangv = $matches[2];
                                $zeitvins = zeitins($zeitv);
                                if ($zeitins==0) {
                                    $zeitins = $zeitvins;
                                } else if ($zeitins!=$zeitvins) {
                                    $zeitvins = -1;
                                    $fehler .= "<div style='background-color:#ff0000; margin:10px;'>FEHLER! beim verifizieren der Lauf-zeit... (orig) ".$zeitins."!=".$zeitvins." (v)<hr><pre>".$lastline."</pre><hr>".$moreinfo."</div>";
                                }
                                if ($rang==0) {
                                    $rang = $rangv;
                                } else if ($rang>$rangv) {
                                    $rangv = -1;
                                    $fehler .= "<div style='background-color:#ff0000; margin:10px;'>FEHLER! beim verifizieren des Rangs... (orig) ".$rang."<".$rangv." (v)<hr><pre>".$lastline."</pre><hr>".$moreinfo."</div>";
                                }
                            } else {
                                $zeitvins = -1;
                                $rangv = -1;
                                $fehler .= "<div style='background-color:#ff0000; margin:10px;'>FEHLER! beim verifizieren...<hr><pre>".$lastline."</pre><hr>".$moreinfo."</div>";
                            }
                            $res = preg_match("/Zi\s*([0-9\.]+)\s*\(\s*([0-9]*)\s*\)/",$conts[$j],$matches);
                            if ($res) {
                                $zzeit = $matches[1];
                                $zrang = $matches[2];
                                $res = preg_match_all("/\s*([0-9]+)\ +([0-9\.]+)\ *\(\s*([0-9]+)\s*\)\ *(Zi|[\n\r])/",$conts[$j],$matches);
                                if ($res) {
                                    $lposten = $matches[1][count($matches[1])-1];
                                } else {
                                    $lposten = -1;
                                    $fehler .= "<div style='background-color:#ff0000; margin:10px;'>FEHLER! beim Parsen des Letzten Postens...<hr><pre>".$conts[$j]."</pre><hr>".$moreinfo."</div>";
                                }
                                $res = preg_match("/\s*([0-9\.]+)\s*$/",$conts[$j],$matches);
                                if ($res) {
                                    $zrueckstand = $matches[1];
                                    $zzeitins = zeitins($zzeit);
                                    $moreinfo .= "ZIELEINLAUF: ZEIT: <b>".$zzeit."</b> - RANG: ".$zrang." - R&Uuml;CKSTAND: ".$zrueckstand."</b><br>\n";
                                    $score = ($zzeitins!=0 && $linm!=0?$abgefedertezeitins/$linm/$zzeitins/$zzeitins:0);
                                    echo $moreinfo."<br>";
                                } else {
                                    $zrueckstand = -1;
                                    $zzeitins = -1;
                                    $score = -1;
                                    $fehler .= "<div style='background-color:#ff0000; margin:10px;'>FEHLER! beim Parsen des R&uuml;ckstandes im Zieleinlauf...<hr><pre>".$conts[$j]."</pre><hr>".$moreinfo."</div>";
                                }
                            } else {
                                $zzeit = -1;
                                $zrang = -1;
                                $fehler .= "<div style='background-color:#ff0000; margin:10px;'>FEHLER! beim Parsen des Zieleinlaufs...<hr><pre>".$conts[$j]."</pre><hr>".$moreinfo."</div>";
                            }
                            $result_r = mysql_query("SELECT * from solvresults WHERE person='".intval($person)."' AND lauf='".intval($lauf)."'");
                            $num_r = mysql_num_rows($result_r);
                            if ($num_r==0) {
                                mysql_query("INSERT solvresults SET person='".intval($person)."', lauf='".intval($lauf)."', letzterposten='".intval($lposten)."', laufzeit='".intval($zeitins)."', siegerlaufzeit='".intval($siegerzeitins)."', abgefedertelaufzeit='-1', zieleinlaufzeit='".intval($zzeitins)."', leistungskm='".intval($linm)."', score='-1'");
                            }
                        }
                    } else {
                        $fehler .= "<div style='background-color:#ff0000; margin:10px;'>FEHLER! beim Parsen des L&auml;ufers...<hr><pre>".$bs[$j]."</pre></div>";
                    }
                }
            }
            echo $fehler;
        }
        
        $result_r = mysql_query("SELECT * from solvresults WHERE lauf='".intval($lauf)."'");
        $num_r = mysql_num_rows($result_r);
        echo "<a href='?year=".$_GET["year"]."&event=".$_GET["event"]."&action=reevaluate'>Re-evaluate</a><table>";
        echo "<tr><td>name</td><td>letzterposten</td><td>t<sub>L</sub></td><td>t<sub>S</sub></td><td><b>t<sub>A</sub></b></td><td>t<sub>Z</sub></td><td>s</td><td><b>P</b></td><td><b>V</b></td></tr>";
        for ($k=0; $k<$num_r; $k++) {
            $row_r = mysql_fetch_array($result_r);
            $result_p = mysql_query("SELECT name from solvpeople WHERE id='".intval($row_r["person"])."'");
            $row_p = mysql_fetch_array($result_p);
            $laufzeit = $row_r["laufzeit"];
            $siegerlaufzeit = $row_r["siegerlaufzeit"];
            $abgefedertelaufzeit = floatval($row_r["abgefedertelaufzeit"]);
            $zieleinlaufzeit = $row_r["zieleinlaufzeit"];
            $leistungskm = $row_r["leistungskm"];
            $score = floatval($row_r["score"]);
            $cmpscore = -1;
            if ($laufzeit!=-1 && $siegerlaufzeit!=-1 && $zieleinlaufzeit!=-1 && $leistungskm!=-1) {
                if ($abgefedertelaufzeit==-1) {
                    $abgefedertelaufzeit = (atan(($laufzeit/$siegerlaufzeit-1)*2)/2+1)*$siegerlaufzeit;
                    mysql_query("UPDATE solvresults SET abgefedertelaufzeit='".str_replace(",",".",floatval($abgefedertelaufzeit))."' WHERE id='".intval($row_r["id"])."'");
                }
                if ($score==-1) {
                    $score = $abgefedertelaufzeit/$leistungskm/$zieleinlaufzeit/$zieleinlaufzeit;
                    mysql_query("UPDATE solvresults SET score='".str_replace(",",".",floatval($score))."' WHERE id='".intval($row_r["id"])."'");
                }
                $result_tmp = mysql_query("SELECT MAX(score) from solvresults WHERE lauf='".intval($lauf)."' AND letzterposten='".$row_r["letzterposten"]."'");
                $row_tmp = mysql_fetch_array($result_tmp);
                $cmpscore = $score*1000/floatval($row_tmp["MAX(score)"]);
            }
            echo "<tr><td>".$row_p["name"]."</td><td>".$row_r["letzterposten"]."</td><td>".$laufzeit."</td><td>".$siegerlaufzeit."</td><td><b>".round($abgefedertelaufzeit,2)."</b></td><td>".$zieleinlaufzeit."</td><td>".$leistungskm."</td><td><b>".$score."</b></td><td><b>".round($cmpscore,2)."</b></td></tr>";
        }
        echo "</table>";
    } else if (isset($_GET["year"])) {
        $url = "http://www.o-l.ch/cgi-bin/results?type=rang&year=".intval($_GET["year"])."&event=Auswahl";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $file = curl_exec($ch);
        curl_close($ch);
        $res = preg_match_all("/<input[^>]*name=event[^>]*value=\"([^\"]+)\"[^>]*>/i",$file,$matches);
        $events = $matches[1];
        $result = mysql_query("SELECT COUNT(*) from solvresults WHERE lauf='".intval($lauf)."'");
        $row = mysql_fetch_array($result);
        for ($i=0; $i<count($events); $i++) {
            echo "<a href='?year=".$_GET["year"]."&event=".$events[$i]."'>".$events[$i]." ".($row["COUNT(*)"]!="0"?"(".$row["COUNT(*)"].")":"")."</a><br>";
        }
    } else {
        for ($year=2000; $year<=date("Y"); $year++) {
            echo "<a href='?year=".$year."'>".$year."</a><br>";
        }
        echo "Remove Duplicates:<br><br>";
        $result = mysql_query("SELECT s1.name AS name, s1.id AS id1, s2.id AS id2, s1.jahrgang AS jahrgang1, s2.jahrgang AS jahrgang2, s1.wohnort AS wohnort1, s2.wohnort AS wohnort2 FROM solvresults sr1 JOIN solvpeople s1 ON (sr1.person=s1.id) JOIN solvpeople s2 ON (s2.name=s1.name) JOIN solvresults sr2 ON (sr2.person=s2.id) WHERE s1.id!=s2.id ORDER BY name ASC");
        $num = mysql_num_rows($result);
        for ($i=0; $i<$num; $i++) {
            $row = mysql_fetch_array($result);
            echo $row["name"]." - ".$row["id1"]."-<span style='color:rgb(255,0,0);'>".$row["id2"]."</span> - ".$row["jahrgang1"]."-<span style='color:rgb(255,0,0);'>".$row["jahrgang2"]."</span> - ".$row["wohnort1"]."-<span style='color:rgb(255,0,0);'>".$row["wohnort2"]."</span><br>";
            echo "UPDATE solvresults SET person='".$row["id1"]."' WHERE person='".$row["id2"]."'<br>";
        }
    }
} else {
    $wertung = array();
    $result_p = mysql_query("SELECT * from solvpeople");
    $num_p = mysql_num_rows($result_p);
    for ($i=0; $i<$num_p; $i++) {
        $row_p = mysql_fetch_array($result_p);
        $wertung[$row_p["id"]] = array();
    }
    $result_l = mysql_query("SELECT * from solvlaufe WHERE jahr='2013'");
    $num_l = mysql_num_rows($result_l);
    for ($i=0; $i<$num_l; $i++) {
        $row_l = mysql_fetch_array($result_l);
        $result_r = mysql_query("SELECT * from solvresults WHERE lauf='".$row_l["id"]."'");
        $num_r = mysql_num_rows($result_r);
        for ($j=0; $j<$num_r; $j++) {
            $row_r = mysql_fetch_array($result_r);
            $laufzeit = $row_r["laufzeit"];
            $siegerlaufzeit = $row_r["siegerlaufzeit"];
            $abgefedertelaufzeit = floatval($row_r["abgefedertelaufzeit"]);
            $zieleinlaufzeit = $row_r["zieleinlaufzeit"];
            $leistungskm = $row_r["leistungskm"];
            $score = floatval($row_r["score"]);
            $cmpscore = -1;
            if ($laufzeit!=-1 && $siegerlaufzeit!=-1 && $zieleinlaufzeit!=-1 && $leistungskm!=-1) {
                $result_tmp = mysql_query("SELECT MAX(score) from solvresults WHERE lauf='".$row_l["id"]."' AND letzterposten='".$row_r["letzterposten"]."'");
                $row_tmp = mysql_fetch_array($result_tmp);
                $cmpscore = $score*1000/floatval($row_tmp["MAX(score)"]);
            }
            array_push($wertung[$row_r["person"]],array($row_l["id"],$cmpscore,$laufzeit,$siegerlaufzeit,$abgefedertelaufzeit,$zieleinlaufzeit,$leistungskm,$score,intval($row_r["letzterposten"])));
        }
    }
    $people = array_keys($wertung);
    $html_details = "";
    $rangliste = array();
    for ($i=0; $i<count($people); $i++) {
        $result_p = mysql_query("SELECT * from solvpeople WHERE id='".intval($people[$i])."'");
        $row_p = mysql_fetch_array($result_p);
        $html_details .= "<tr><td colspan='2'><br><br><br><a name='details".$row_p["id"]."'></a><h3>".$row_p["name"]."</h3></td></tr>";
        $tmp = $wertung[$people[$i]];
        $cnt = 0;
        $sum = 0;
        $htmlsum = array();
        for ($j=0; $j<count($tmp); $j++) {
            $result_l = mysql_query("SELECT * from solvlaufe WHERE id='".intval($tmp[$j][0])."'");
            $row_l = mysql_fetch_array($result_l);
            $html_details .= "<tr><td><a href='javascript:toggle(\"math-".$i."-".$j."\")'>".$row_l["name"]." - Letzter Posten: ".$tmp[$j][8]."</a></td><td style='text-align:right;'>".number_format($tmp[$j][1],2,".","'")."</td></tr><tr id='math-".$i."-".$j."' style='display:none;'><td colspan='2'><p><table style='width:auto;'><tr><td style='vertical-align:middle; width:100px; text-align:right;'>t<sub>L</sub>&nbsp;=&nbsp;</td><td style='vertical-align:middle;'>".$tmp[$j][2]." s</td></tr></table></p><p><table style='width:auto;'><tr><td style='vertical-align:middle; width:100px; text-align:right;'>t<sub>S</sub>&nbsp;=&nbsp;</td><td style='vertical-align:middle;'>".$tmp[$j][3]." s</td></tr></table></p><p><table style='width:auto;'><tr><td style='vertical-align:middle; width:100px; text-align:right;'>t<sub>Z</sub>&nbsp;=&nbsp;</td><td style='vertical-align:middle;'>".$tmp[$j][5]." s</td></tr></table></p><p><table style='width:auto;'><tr><td style='vertical-align:middle; width:100px; text-align:right;'>s&nbsp;=&nbsp;</td><td style='vertical-align:middle;'>".$tmp[$j][6]." s</td></tr></table></p>
<p><table style='width:auto;'><tr><td rowspan='3' style='vertical-align:middle; width:100px; text-align:right;'>t<sub>A</sub>&nbsp;=&nbsp;</td><td rowspan='3' style='vertical-align:middle;'>(&nbsp;</td><td style='text-align:center;'>1</td><td rowspan='3' style='vertical-align:middle;'>*arctan((</td><td style='text-align:center;'>".$tmp[$j][2]."</td><td rowspan='3' style='vertical-align:middle;'>-1)*2)+1&nbsp;)&nbsp;*&nbsp;".$tmp[$j][3]."&nbsp;=&nbsp;".number_format($tmp[$j][4],2,".","'")."</td></tr><tr><td style='height:1px; background-color:#000000;'></td><td style='height:1px; background-color:#000000;'></td></tr><tr><td style='text-align:center;'>2</td><td style='text-align:center;'>".$tmp[$j][3]."</td></tr></table></p>
<p><table style='width:auto;'><tr><td rowspan='3' style='vertical-align:middle; width:100px; text-align:right;'>P&nbsp;=&nbsp;</td><td style='text-align:center;'>".number_format($tmp[$j][4],2,".","'")."</td><td rowspan='3' style='vertical-align:middle;'>&nbsp;=&nbsp;".number_format($tmp[$j][7],8,".","'")."</td></tr><tr><td style='height:1px; background-color:#000000;'></td></tr><tr><td style='text-align:center;'>".$tmp[$j][6]."&nbsp;*&nbsp;".$tmp[$j][5]."<sup>2</sup></td></tr></table></p></td></tr>";
            if ($tmp[$j][1]!=-1) {
                $cnt++;
                $sum += $tmp[$j][1];
                array_push($htmlsum,number_format($tmp[$j][1],2,".","'"));
            }
        }
        $endscore = ($cnt!=0)?$sum/$cnt*(atan(3*$cnt/4-sqrt(3))+2*3.14159265/6):-1;
        $html_details .= "<tr><td><b><a href='javascript:toggle(\"math-total-".$i."\")'>TOTAL</a></b></td><td style='text-align:right;'>
<b>".number_format($endscore,2,".","'")."</b></td></tr><tr id='math-total-".$i."' style='display:none;'><td colspan='2'>
<table style='width:auto;'><tr><td rowspan='3' style='vertical-align:middle; width:100px; text-align:right;'>TOTAL&nbsp;=&nbsp;</td><td style='text-align:center;'>".implode(" + ",$htmlsum)."</td><td rowspan='3' style='vertical-align:middle;'>&nbsp;*&nbsp;(&nbsp;arctan(</td><td>3*".$cnt."</td><td rowspan='3' style='vertical-align:middle;'>-&#8730;<span style='text-decoration:overline;'>3</span>)&nbsp;+&nbsp;</td><td>2&#960;</td><td rowspan='3' style='vertical-align:middle;'>&nbsp;)</td></tr><tr><td style='height:1px; background-color:#000000;'></td><td style='height:1px; background-color:#000000;'></td><td style='height:1px; background-color:#000000;'></td></tr><tr><td style='text-align:center;'>".$cnt."</td><td style='text-align:center;'>4</td><td style='text-align:center;'>6</td></tr></table>
<br>
<table style='width:auto;'><tr><td style='vertical-align:middle; width:100px; text-align:right;'>=&nbsp;</td><td style=''>".number_format($sum/$cnt,2,".","'")."&nbsp;*&nbsp;(".number_format(atan(3*$cnt/4-sqrt(3))+2*3.14159265/6,4,".","'").")</td></tr></table>
<br>
<table style='width:auto;'><tr><td style='vertical-align:middle; width:100px; text-align:right;'>=&nbsp;</td><td style='text-align:center;'>".number_format($endscore,2,".","'")."</td></tr></table>
</td></tr>";
        array_push($rangliste,array($people[$i],$endscore));
    }
    usort($rangliste,"ranglistesort");
    echo "<table>";
    for ($i=0; $i<count($rangliste); $i++) {
        $result_p = mysql_query("SELECT * from solvpeople WHERE id='".intval($rangliste[$i][0])."'");
        $row_p = mysql_fetch_array($result_p);
        echo "<tr><td>".($i+1)."</td><td><a href='#details".$row_p["id"]."'>".$row_p["name"]."</a></td><td style='text-align:right;'>".number_format($rangliste[$i][1],2,".","'")."</td></tr>";
    }
    echo "</table>";
    echo "<table>".$html_details."</table>";
}

function floatsort($a,$b) {
    return $a>$b?1:-1;
}

function ranglistesort($a,$b) {
    return $a[1]<$b[1]?1:-1;
}

function zeitins($zeit) {
    $zeit = trim($zeit);
    $res = preg_match("/(([0-9]+)[:\.])?([0-9]+)[:\.]([0-9]+)/",$zeit,$matches);
    if (!$res) return -1;
    $h = $matches[2];
    $m = $matches[3];
    $s = $matches[4];
    return $h*3600+$m*60+$s;
}

function roemisch($zahl) {
    $rz = "";
    while (1000<=$zahl) {
        $rz .= "M";
        $zahl -= 1000;
    }
    while (500<=$zahl) {
        $rz .= "D";
        $zahl -= 500;
    }
    while (100<=$zahl) {
        $rz .= "C";
        $zahl -= 100;
    }
    while (50<=$zahl) {
        $rz .= "L";
        $zahl -= 50;
    }
    while (10<=$zahl) {
        $rz .= "X";
        $zahl -= 10;
    }
    while (5<=$zahl) {
        $rz .= "V";
        $zahl -= 5;
    }
    while (0<$zahl) {
        $rz .= "I";
        $zahl -= 1;
    }
    $rz = str_replace("VIIII","IX",$rz);
    $rz = str_replace("IIII","IV",$rz);
    $rz = str_replace("LXXXX","XC",$rz);
    $rz = str_replace("XXXX","XL",$rz);
    $rz = str_replace("DCCCC","CM",$rz);
    $rz = str_replace("CCCC","CD",$rz);
    return $rz;
}

?>