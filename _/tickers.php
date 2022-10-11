<?php

// =============================================================================
// Versuch, die verschiedenen Typen von Einträgen irgendwie zu vereinheitlichen.
// TODO(simon): Was davon ist in Gebrauch? Was soll geschehen?
// =============================================================================

use Olz\Utils\DbUtils;

require_once __DIR__.'/config/paths.php';
require_once __DIR__.'/config/date.php';

function termine_ticker($settings) {
    global $_DATE;
    $db = DbUtils::fromEnv()->getDb();

    $textlaenge_def = isset($settings["eintrag_laenge"]) ? intval($settings["eintrag_laenge"]) : 80;
    $listenlaenge = isset($settings["eintrag_anzahl"]) ? intval($settings["eintrag_anzahl"]) : 8;
    $sql_where = isset($settings["sql_where"]) ? $settings["sql_where"] : "";
    $titel = isset($settings["titel"]) ? $settings["titel"] : "Termine";
    $heute_highlight = isset($settings["heute_highlight"]) ? $settings["heute_highlight"] : true;
    // Konstanten
    $db_table = "termine";
    $heute = olz_current_date("Y-m-d");
    echo "<div class='layout'>";
    echo "<h4 class='tablebar'>".$titel."</h4>";
    // Tabelle auslesen
    $sql = "select * from {$db_table} WHERE ((datum >= '{$heute}') OR (datum_end >= '{$heute}')) AND (on_off = 1)".$sql_where." ORDER BY datum ASC LIMIT {$listenlaenge}";
    $result = $db->query($sql);

    // TEST uu/1.4.2011
    // Was, wenn ein mehrtägiger Event vor x Tagen begonnen hat? simon/23.5.2011
    $pulse = "";
    $wotag = olz_current_date("w");
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
                $datum_end = '(bis '.$_DATE->olzDate('WW t.m.', $datum_end).')';
            } elseif (($datum_end != '0000-00-00' and $datum_end !== null) and $diff_end > 0) {
                $datum_end = '(bis '.$_DATE->olzDate('WW', $datum_end).')';
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
                $datum_end = '-'.$_DATE->olzDate('WW (t.m.)', $datum_end);
            } elseif (($datum_end != '0000-00-00' and $datum_end !== null) and $diff_end > 0) {
                $datum_end = '-'.$_DATE->olzDate('WW', $datum_end);
            } else {
                $datum_end = '';
            }
            // $datum_end = ($datum_end!='0000-00-00' AND $datum_end!=$datum_tmp) ? '-'.$_DATE->olzDate('W',$datum_end) : '' ;
            $datum = $_DATE->olzDate('WW', $datum_tmp).$datum_end.":";
        } elseif ($diff < (14.95 - $wotag)) {
            $case_tmp = 3;
            $datum_end = (($datum_end != '0000-00-00' and $datum_end !== null) and $datum_end != $datum_tmp) ? '-'.$_DATE->olzDate('t.m.(W)', $datum_end) : '';
            $datum = $_DATE->olzDate('W, t.m.', $datum_tmp).$datum_end;
        } elseif ($flag == 1) {
            $case_tmp = 4;
            $datum = $_DATE->olzDate('t.m.', $datum_tmp);
        } else {
            $case_tmp = 5;
            $datum = $_DATE->olzDate('t.m.', $datum_tmp);
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
        // $datum_tmp = zeitintervall(strtotime($datum_tmp));
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

        echo "<p{$class_heute}><a href='termine.php#id".$id_tmp."' id='terminticker".$id_tmp."' onmouseover='mousein(\"terminticker".$id_tmp."\")' onmouseout='mouseout(\"terminticker".$id_tmp."\")'><span style='font-weight:bold;margin-right:6px;'>".$datum_tmp."</span> ".$titel.$mehr."</a></p>";
    }
    echo "</div>";
}

function zeitintervall($datum) {
    global $wochentage_lang;
    require_once __DIR__.'/config/date.php';
    $today = strtotime(olz_current_date("Y-m-d"));
    $towday = date("w", $today);
    if ($towday == 0) {
        $towday = 7;
    }
    $tage = round(($datum - $today) / 86400, 0);
    $wday = date("w", $datum);
    if ($wday == 0) {
        $wday = 7;
    }
    if ($tage == -1) {
        return "Gestern";
    }
    if ($tage == 0) {
        return "Heute";
    }
    if ($tage == 1) {
        return "Morgen";
    }
    if ($tage > -7 && $tage < 0) {
        return "Letzten ".$wochentage_lang[$wday];
    }
    if ($tage < (15 - $wday)) {
        return $wochentage_lang[$wday]; // (($towday<$wday)?"Diesen ":"Nächsten ")
    }
    return $_DATE->olzDate("tt.mm.", $datum);
}

/*


function aktuell_ticker($textlaenge_def=80,$nowrap=false,$offset=0) {
    global $conn_id;
    echo "<!-- AKTUELL TICKER -->
<h2><a href='index.php?page=2' style=' color:#003508;'>Aktuell ...</a></h2>";

    //Konstanten
    $db_table = "aktuell";
    $heute = date("Y-m-d");

    //Tabelle auslesen
    $sql = "select * from $db_table WHERE (datum <= '$heute') AND (typ LIKE '%aktuell%') AND (on_off = 1) ORDER BY datum DESC LIMIT $offset,1";
    $result = $db->query ($sql);

    $row = mysqli_fetch_array($result);
    $datum_tmp = strtotime($row['datum']);
    $titel = $row['titel'];
    $text = $row['text'];
    $id_tmp = $row['id'];
    $datum_tmp = $_DATE->olzDate("t. MM", $datum_tmp);
    $mehr = "";
    if ($textlaenge_def*5<strlen($datum_tmp)+strlen($titel)+strlen($text)) {
        $text = mb_substr($text,0,($textlaenge_def*5-strlen($datum_tmp)-strlen($titel)));
        $text = mb_substr($text,0,mb_strrpos($text," "));
        $mehr = " ...";
    }

    echo "<p><h2><a href='index.php?page=2&amp;id=".$id_tmp."' style='color:#003508; padding-left:7px;'><b>".$datum_tmp.":</b> ".$titel."</a></h2>".$text.$mehr."</p>";
}

function random_fan($textlaenge_def=60,$nowrap=false) {
    echo "<noscript>";
    galerie_ticker($textlaenge_def,$nowrap,0);
    echo "</noscript>
<table onmouseover='stop();' onmouseout='resume();' style='height:1px;'>
<tr><td id='fan3cell'><a href='' style='position:absolute; color:rgb(0,30,0);' id='fan3text'></a></td></tr>
<tr><td id='fan2cell'><a href='' style='position:absolute; color:rgb(0,30,0);' id='fan2text'></a></td></tr>
<tr><td id='fan1cell'><a href='' style='position:absolute; color:rgb(0,30,0);' id='fan1text'></a></td></tr>
<tr><td id='fan0cell'><a href='' style='position:absolute; color:rgb(0,30,0);' id='fan0text'></a></td></tr>
</table>
<script type='text/javascript'>
var width = window.innerWidth;
var fontfactani = 1;
if (width<1200) {
fontfactani = ((width-800)/400+1)/2;
}
var fontfact = 0;
var step = 0;
var aktuell0 = new Array(".aktuell_fan(0).");
var aktuell1 = new Array(".aktuell_fan(1).");
var termine0 = new Array(".termine_fan(0).");
var termine1 = new Array(".termine_fan(1).");
var blog0 = new Array(".blog_fan(0).");
var blog1 = new Array(".blog_fan(1).");
var forum0 = new Array(".forum_fan(0).");
var forum1 = new Array(".forum_fan(1).");
var galerie0 = new Array(".galerie_fan(0).");
var galerie1 = new Array(". galerie_fan(1).");
//var values = new Array(aktuell0,aktuell1,termine0,termine1,blog0,blog1,forum0,forum1,galerie0,galerie1);
var values = new Array(aktuell0,aktuell1,galerie0,galerie1);
document.getElementById(\"fan3text\").href = values[0][0];
document.getElementById(\"fan3text\").innerHTML = values[0][1];
var llshown = 1;
document.getElementById(\"fan2text\").href = values[llshown][0];
document.getElementById(\"fan2text\").innerHTML = values[llshown][1];
var lshown = 2;
document.getElementById(\"fan1text\").href = values[lshown][0];
document.getElementById(\"fan1text\").innerHTML = values[lshown][1];
var shown = 3;
document.getElementById(\"fan0text\").href = values[shown][0];
document.getElementById(\"fan0text\").innerHTML = values[shown][1];
var num = values.length;
var timeout;
var interval = window.setInterval(\"wechsel()\",5000);
animation();
function wechsel() {
var width = window.innerWidth;
if (width<1200) {
fontfactani = ((width-800)/400+1)/2;
} else {
fontfactani = 1;
}
var newshown = Math.floor(Math.random()*num);
while (newshown == shown || newshown == lshown || newshown == llshown) {
newshown = Math.floor(Math.random()*num);
}
llshown = lshown;
lshown = shown;
shown = newshown;
document.getElementById(\"fan3text\").href = document.getElementById(\"fan2text\").href;
document.getElementById(\"fan2text\").href = document.getElementById(\"fan1text\").href;
document.getElementById(\"fan1text\").href = document.getElementById(\"fan0text\").href;
document.getElementById(\"fan0text\").href = values[newshown][0];
step=0;
animation();
document.getElementById(\"fan3text\").innerHTML = document.getElementById(\"fan2text\").innerHTML;
document.getElementById(\"fan2text\").innerHTML = document.getElementById(\"fan1text\").innerHTML;
document.getElementById(\"fan1text\").innerHTML = document.getElementById(\"fan0text\").innerHTML;
document.getElementById(\"fan0text\").innerHTML = values[newshown][1];
}
function animation() {
step += (-4*Math.pow(step-0.5,2)+1.2)*0.1;
var factcalc = fontfact;
if (fontfact!= fontfactani) {
factcalc = (step*fontfactani+(1-step)*fontfact)/(step+(1-step));
}
document.getElementById(\"fan0cell\").style.height = anihei(step+0)+\"px\";
document.getElementById(\"fan1cell\").style.height = anihei(step+1)+\"px\";
document.getElementById(\"fan2cell\").style.height = anihei(step+2)+\"px\";
document.getElementById(\"fan3cell\").style.height = anihei(step+3)+\"px\";
document.getElementById(\"fan0cell\").style.fontSize = anihei(step+0)/1.8*factcalc+\"px\";
document.getElementById(\"fan1cell\").style.fontSize = anihei(step+1)/1.8*factcalc+\"px\";
document.getElementById(\"fan2cell\").style.fontSize = anihei(step+2)/1.8*factcalc+\"px\";
document.getElementById(\"fan3cell\").style.fontSize = anihei(step+3)/1.8*factcalc+\"px\";
document.getElementById(\"fan0cell\").style.paddingLeft = anihei(step+0)/2+\"px\";
document.getElementById(\"fan1cell\").style.paddingLeft = anihei(step+1)/2+\"px\";
document.getElementById(\"fan2cell\").style.paddingLeft = anihei(step+2)/2+\"px\";
document.getElementById(\"fan3cell\").style.paddingLeft = anihei(step+3)/2+\"px\";
document.getElementById(\"fan0text\").style.color = \"rgb(0,\"+(anicol(step+0))+\",0)\";
document.getElementById(\"fan1text\").style.color = \"rgb(0,\"+(anicol(step+1))+\",0)\";
document.getElementById(\"fan2text\").style.color = \"rgb(0,\"+(anicol(step+2))+\",0)\";
document.getElementById(\"fan3text\").style.color = \"rgb(0,\"+(anicol(step+3))+\",0)\";
if (step<1) {
window.setTimeout(\"animation()\",75);
} else {
document.getElementById(\"fan3text\").innerHTML = \"\";
fontfact = fontfactani;
}
}

function anicol(step) {
return Math.floor(160-(Math.sin(step*3.14/4)*1.5+(4-step)/4)*40);
}
function anihei(step) {
return Math.sin(step*3.14/4)*40;
}

function stop() {
if (interval) {
window.clearInterval(interval);
}
if (timeout) {
window.clearTimeout(timeout);
}
}
function resume() {
if (0.9<step) {
    timeout = window.setTimeout(\"wechsel()\",100);
}
interval = window.setInterval(\"wechsel()\",5000);
}
</script>";
}

function forum_fan($index) {
    global $conn_id;
    //Konstanten
    $db_table = "forum";
    $textlaenge_def = 60;

    //Tabelle daten auslesen
    $sql = "SELECT * from $db_table WHERE (on_off = '1') AND (email > '') AND (name > '') AND (eintrag > '') ORDER BY datum DESC, zeit DESC LIMIT $index,1";
    $result = $db->query($sql);

    $row = mysqli_fetch_array($result);
    $name = $row['name'];
    $eintrag = $row['eintrag'];
    $datum = strtotime($row['datum']);
    $id_tmp = $row['id'];
    $datum = date("j.n.",$datum);
    $eintrag = strip_tags(str_replace(array("\n","\r"),array(" "," "),$eintrag));
    if ($textlaenge_def<strlen($datum_tmp.$name.$eintrag)) {
        $eintrag = mb_substr($eintrag,0,($textlaenge_def-strlen($datum_tmp.$name)));
        $eintrag = mb_substr($eintrag,0,mb_strrpos($eintrag," "));
        $mehr = " ...";
    }
    return "\"?page=5#id".$id_tmp."\",\"<b>".$name.": </b> ".$eintrag.$mehr." <span style='font-size:0.6em;'>(".$datum.")</span>\"";
}

function blog_fan($index) {
    global $conn_id;
    //Konstanten
    $db_table = "blog";
    $textlaenge_def = 60;

    //Tabelle daten auslesen
    $sql = "SELECT * from $db_table WHERE (datum <= '".date("Y-m-d")."') AND (on_off = 1) ORDER BY datum DESC LIMIT $index,1";
    $result = $db->query($sql);

    $row = mysqli_fetch_array($result);
    $datum = strtotime($row['datum']);
    $autor = $row['autor'];
    $titel = ucfirst($row['titel']);
    $text = str_replace(array("<BILD1>","<BILD2>","<DL1>","<DL2>"),array("","","",""),$row['text']);
    $id_tmp = $row['id'];
    $datum = date("j.n.",$datum);
    $mehr = "";
    $titel = strip_tags(str_replace(array("\n","\r"),array(" "," "),$titel));
    $text = strip_tags(str_replace(array("\n","\r"),array(" "," "),$text));
    if ($textlaenge_def<strlen($datum.$autor.$titel.$text)) {
        if ($textlaenge_def<strlen($datum.$autor.$titel)) {
            $titel = mb_substr($titel,0,($textlaenge_def-strlen($datum.$autor)+7));
            $titel = mb_substr($titel,0,mb_strrpos($titel," "));
        }
        $text = mb_substr($text,0,($textlaenge_def-strlen($datum.$autor.$titel)+7));
        $text = mb_substr($text,0,mb_strrpos($text," "));
        $mehr = " ...";
        $eintrag = $titel." <span style='font-size:0.8em;'><i>".$text."</i></span>";
    } else {
        $eintrag = "";
    }
    return "\"?page=5#id".$id_tmp."\",\"<b>".$autor.": </b> ".$eintrag.$mehr." <span style='font-size:0.6em;'>(".$datum.")</span>\"";
}

function aktuell_fan($index) {
    global $conn_id;
    //Konstanten
    $db_table = "aktuell";
    $textlaenge_def = 60;

    //Tabelle daten auslesen
    $sql = "SELECT * from $db_table WHERE (datum <= '".date("Y-m-d")."') AND (typ LIKE '%aktuell%') AND (on_off = 1) ORDER BY datum DESC LIMIT $index,1";
    $result = $db->query($sql);

    $row = mysqli_fetch_array($result);
    $datum = strtotime($row['datum']);
    $titel = ucfirst($row['titel']);
    $text = ucfirst($row['text']);
    $id_tmp = $row['id'];
    $datum = date("j.n.",$datum);
    $mehr = "";
    $titel = strip_tags(str_replace(array("\n","\r"),array(" "," "),$titel));
    $text = strip_tags(str_replace(array("\n","\r"),array(" "," "),$text));
    if ($textlaenge_def<strlen($datum.$titel.$text)) {
        $text = mb_substr($text,0,($textlaenge_def-strlen($datum.$titel)+7));
        $text = mb_substr($text,0,mb_strrpos($text," "));
        $mehr = " ...";
        $eintrag = $text;
    } else {
        $eintrag = "";
    }
    return "\"?page=5#id".$id_tmp."\",\"<b>".$titel.": </b> ".$eintrag.$mehr." <span style='font-size:0.6em;'>(".$datum.")</span>\"";
}

function termine_fan($index) {
    global $conn_id;
    //Konstanten
    $db_table = "termine";
    $textlaenge_def = 60;
    $heute = date("Y-m-d");

    //Tabelle daten auslesen
    $sql = "SELECT * from $db_table WHERE ((datum >= '$heute') OR (datum_end >= '$heute')) AND (on_off = 1) ORDER BY datum ASC LIMIT $index,1";
    $result = $db->query($sql);

    $row = mysqli_fetch_array($result);
    $datum = strtotime($row['datum']);
    $titel = str_replace("<br>",", ",$row['titel']);
    $text = str_replace("<br>",", ",$row['text']);
    $id = $row['id'];
    $datum = zeitintervall($datum);
    if ($titel == "") $titel = $text;
    $mehr = "";
    $titel = strip_tags(str_replace(array("\n","\r"),array(" "," "),$titel));
    $text = strip_tags(str_replace(array("\n","\r"),array(" "," "),$text));
    if ($textlaenge_def<strlen($datum.$titel.$text)) {
        if ($textlaenge_def<strlen($datum.$titel)) {
            $titel = mb_substr($titel,0,($textlaenge_def-strlen($datum)+7));
            $titel = mb_substr($titel,0,mb_strrpos($titel," "));
        }
        $text = mb_substr($text,0,($textlaenge_def-strlen($datum.$titel)+7));
        $text = mb_substr($text,0,mb_strrpos($text," "));
        $mehr = " ...";
        $eintrag = $titel." <span style='font-size:0.8em;'><i>".$text."</i></span>";
    } else {
        $eintrag = "";
    }
    return "\"?page=5#id".$id."\",\"<b>".$datum.": </b> ".$eintrag.$mehr."\"";
}

function galerie_fan($index) {
    global $conn_id,$data_path;
    //Konstanten
    $db_table = "galerie";
    $textlaenge_def = 60;
    $pfad_galerie = $data_path."galerie/";
    $heute = date("Y-m-d");

    //Tabelle daten auslesen
    $sql = "SELECT * from $db_table WHERE (datum <= '$heute') AND (on_off = '1') ORDER BY datum DESC LIMIT $index,1";
    $result = $db->query($sql);

    $row = mysqli_fetch_array($result);
    $datum = strtotime($row['datum']);
    $titel = str_replace("<br>",", ",$row['titel']);
    $text = str_replace("<br>",", ",$row['groesse']);
    $id = $row['id'];
    $datum = zeitintervall($datum);
    if ($titel == "") $titel = $text;
    $mehr = "";
    $titel = strip_tags(str_replace(array("\n","\r"),array(" "," "),$titel));
    $text = strip_tags(str_replace(array("\n","\r"),array(" "," "),$text));
    if ($textlaenge_def<strlen($datum.$titel.$text)) {
        if ($textlaenge_def<strlen($datum.$titel)) {
            $titel = mb_substr($titel,0,($textlaenge_def-strlen($datum)+7));
            $titel = mb_substr($titel,0,mb_strrpos($titel," "));
        }
        $text = mb_substr($text,0,($textlaenge_def-strlen($datum.$titel)+7));
        $text = mb_substr($text,0,mb_strrpos($text," "));
        $mehr = " ...";
        $eintrag = $titel.$mehr." <span style='font-size:0.6em;'><i>".$text." Fotos</i></span>";
    } else {
        $eintrag = $titel.$mehr." <span style='font-size:0.6em;'><i>".$text." Fotos</i></span>";
    }
    return "\"?page=5#id".$id."\",\"<b>".$datum.": </b> ".$eintrag."\"";
}


// TICKER

function random_ticker($textlaenge_def=60,$nowrap=false) {
    echo "<table style='width:100%; height:100%;'><tr><td onmouseover='stop();' onmouseout='resume();' style='width:90%; vertical-align:middle;'>";
    echo "<div id='random0' style='display:block;'>";
    aktuell_ticker($textlaenge_def,$nowrap,0);
    echo "</div>";
    echo "<div id='random1' style='display:none;'>";
    aktuell_ticker($textlaenge_def,$nowrap,1);
    echo "</div>";
    echo "<div id='random2' style='display:none;'>";
    termine_ticker($textlaenge_def,$nowrap,4);
    echo "</div>";
    echo "<div id='random3' style='display:none;'>";
    forum_ticker($textlaenge_def,$nowrap,4);
    echo "</div>";
    echo "<div id='random4' style='display:none;'>";
    blog_ticker($textlaenge_def,$nowrap,0);
    echo "</div>";
    echo "<div id='random5' style='display:none;'>";
    blog_ticker($textlaenge_def,$nowrap,1);
    echo "</div>";
    echo "<div id='random6' style='display:none;'>";
    galerie_ticker($textlaenge_def,$nowrap,0);
    echo "</div>";
    echo "<div id='random7' style='display:none;'>";
    galerie_ticker($textlaenge_def,$nowrap,1);
    echo "</div>";
    echo "</td><td style='vertical-align:middle; width:20px; text-align:right;'><a href='javascript:wechsel();'>&gt;</a></td></tr></table>";
    echo "<script type='text/javascript'>
var llshown = 0;
var lshown = 0;
var shown = 0;
var num = 8;
var interval = window.setInterval(\"wechsel()\",10000);
var timer;
wechsel();
function stop() {
window.clearInterval(interval);
window.clearTimeout(timer);
}
function resume() {
window.clearInterval(interval);
timer = window.setTimeout(\"wechsel()\",10);
interval = window.setInterval(\"wechsel()\",10000);
}
function wechsel() {
var newshown = Math.floor(Math.random()*num);
while (newshown == shown || newshown == lshown || newshown == llshown) {
newshown = Math.floor(Math.random()*num);
}
document.getElementById(\"random\"+shown).style.display = \"none\";
document.getElementById(\"random\"+newshown).style.display = \"block\";
llshown = lshown;
lshown = shown;
shown = newshown;
}
</script>";
}

function forum_ticker($textlaenge_def=60,$nowrap=false,$listenlaenge=4) {
    global $conn_id;
    $html_tmp = "";
    if ($nowrap) {
        $html_tmp = " style='white-space:nowrap;'";
    }
    echo "<!-- FORUM TICKER -->
<h2><a href='index.php?page=5' style=' color:#003508;'>Forum ...</a></h2>
<ul class='layout'".$html_tmp.">";

    //Konstanten
    $db_table = "forum";

    //Tabelle daten auslesen
    $sql = "select * from $db_table WHERE (on_off = '1') AND (email > '') AND (name > '') AND (eintrag > '') ORDER BY datum DESC, zeit DESC LIMIT $listenlaenge";
    $result = $db->query($sql);

    while ($row = mysqli_fetch_array($result))
    {$name = $row['name'];
        $eintrag = $row['eintrag'];
        $datum_tmp = strtotime($row['datum']);
        $id_tmp = $row['id'];
        $datum_tmp = $_DATE->olzDate("t. MM", $datum_tmp);
        $eintrag = mb_substr($eintrag,0,($textlaenge_def - strlen($datum_tmp.$name)));
        $eintrag = mb_substr($eintrag,0,mb_strrpos($eintrag," "));
        if ($zugriff) $edit_admin = "<a href='index.php?page=5&amp;id=$id_tmp&amp;button$db_table=start' class='linkedit'>&nbsp;</a>";
        else $edit_admin = "";

        echo "<li>".$edit_admin."<a href='index.php?page=5#id".$id_tmp."' style='color:#003508; padding-left:7px;'><b>".$datum_tmp.": (".$name.")</b> ".$eintrag." ...</a></li>";
    }
    echo "</ul>";
}

function blog_ticker($textlaenge_def=80,$nowrap=false,$offset=0) {
    global $conn_id;
    echo "<!-- BLOG TICKER -->
<h2><a href='index.php?page=7' style=' color:#003508;'>Blog ...</a></h2>";

    //Konstanten
    $db_table = "blog";
    $heute = date("Y-m-d");

    //Tabelle auslesen
    $sql = "select * from $db_table WHERE (datum <= '$heute') AND (on_off = 1) ORDER BY datum DESC LIMIT $offset,1";
    $result = $db->query ($sql);

    $row = mysqli_fetch_array($result);
    $datum_tmp = strtotime($row['datum']);
    $titel = $row['titel'];
    $text = $row['text'];
    $id_tmp = $row['id'];
    $datum_tmp = $_DATE->olzDate("t. MM", $datum_tmp);
    $mehr = "";
    if ($textlaenge_def*5<strlen($datum_tmp)+strlen($titel)+strlen($text)) {
        $text = mb_substr($text,0,($textlaenge_def*5-strlen($datum_tmp)-strlen($titel)));
        $text = mb_substr($text,0,mb_strrpos($text," "));
        $mehr = " ...";
    }

    echo "<p><h2><a href='index.php?page=7&amp;id=".$id_tmp."#id".$id_tmp."' style='color:#003508; padding-left:7px;'><b>".$datum_tmp.":</b> ".$titel."</a></h2>".$text.$mehr."</p>";
}


function galerie_ticker($textlaenge_def=80,$nowrap=false,$offset=0) {
    global $conn_id,$data_path;
    echo "<!-- GALERIE TICKER -->
<h2><a href='index.php?page=4' style=' color:#003508;'>Galerie ...</a></h2>";

    //Konstanten
    $db_table = "galerie";
    $pfad_galerie = $data_path."galerie/";
    $heute = date("Y-m-d");

    //Tabelle auslesen
    $sql = "select * from $db_table WHERE (datum <= '$heute') AND (on_off = 1) ORDER BY datum DESC LIMIT $offset,1";
    $result = $db->query ($sql);

    $row = mysqli_fetch_array($result);
    $datum_tmp = strtotime($row['datum']);
    $titel = $row['titel'];
    $autor = $row['autor'];
    $groesse = $row['groesse'];
    $id_tmp = $row['id'];
    $foto_datum = date("Ymd",$datum_tmp);
    $indexes = array();
    for ($i=0; $i<((3<$groesse)?3:$groesse); $i++) {
        $rand_pic = mt_rand(1, $groesse);
        while(!is_bool(array_search($rand_pic,$indexes))) {
            $rand_pic = mt_rand(1, $groesse);
        }
        array_push($indexes,$rand_pic);
    }
    for ($i=0; $i<count($indexes); $i++) {
        $indexes[$i] = str_pad($indexes[$i] ,3, '0', STR_PAD_LEFT);
    }
    $datum_tmp = $_DATE->olzDate("t. MM", $datum_tmp);

    echo "<p><h2><a href='index.php?page=4' style='color:#003508; padding-left:7px;'><b>".$datum_tmp.":</b> ".$titel." (".$autor.")</a></h2>";
    for ($i=0; $i<count($indexes); $i++) {
        echo "<img src='".$pfad_galerie."foto".$foto_datum."/thumb/".$foto_datum."_th_".$indexes[$i].".jpg' style='height:55px; padding:5px;' alt='zufallsbild'>";
    }
    echo "</p>";
}
*/
