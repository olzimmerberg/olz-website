<?php

// =============================================================================
// Kann gewisse Daten vom SOLV und von GO2OL lesen.
// TODO(simon): DEPRECATED?
// =============================================================================

use Olz\Utils\AbstractDateUtils;
use Olz\Utils\DbUtils;

function solvdataforyear($year) {
    $date_utils = AbstractDateUtils::fromEnv();
    $db = DbUtils::fromEnv()->getDb();
    if (!$year) {
        $year = $date_utils->getCurrentDateInFormat("Y");
    }
    $url = "https://o-l.ch/cgi-bin/fixtures?&year=".$year."&kind=&csv=1";
    $file = iconv('ISO-8859-1', 'UTF-8', load_url($url));
    $solv_termine = preg_split("/[\n\r]+/i", $file);
    for ($i = 0; $i < count($solv_termine); $i++) {
        $tmp = explode(";", $solv_termine[$i]);
        $tmp["uniqueid"] = intval($tmp[0]);
        $tmp["datum"] = strtotime($tmp[1]);
        $tmp["dauer"] = intval($tmp[2]);
        $tmp["art"] = $tmp[3];
        $tmp["nacht"] = ($tmp[4] == "night");
        $tmp["national"] = (intval($tmp[5]) == 1);
        $tmp["region"] = $tmp[6];
        $tmp["abk"] = $tmp[7];
        $tmp["name"] = $tmp[8];
        $tmp["link"] = $tmp[9];
        $tmp["verein"] = $tmp[10];
        $tmp["karte"] = $tmp[11];
        $tmp["wkz"] = $tmp[12];
        $tmp["coordx"] = intval($tmp[13]);
        $tmp["coordy"] = intval($tmp[14]);
        $tmp["meldeschluss"] = strtotime($tmp[15]);
        $tmp["modified"] = strtotime($tmp[17]);
        if ($tmp["datum"]) {
            $result = $db->query("SELECT solv_uid FROM solv_events WHERE solv_uid='".intval($tmp[0])."'", $db);
            $num = mysqli_num_rows($result);
            if ($num == 0) {
                $db->query("INSERT INTO solv_events (solv_uid) VALUES ('".intval($tmp[0])."')", $db);
            }
            $db->query("UPDATE solv_events SET date='".date("Y-m-d", strtotime($tmp[1]))."', duration='".intval($tmp[2])."', kind='".mysqli_real_escape_string($tmp[3])."', day_night='".mysqli_real_escape_string($tmp[4])."', national='".intval($tmp[5])."', region='".mysqli_real_escape_string($tmp[6])."', type='".mysqli_real_escape_string($tmp[7])."', event_name='".mysqli_real_escape_string($tmp[8])."', event_link='".mysqli_real_escape_string($tmp[9])."', club='".mysqli_real_escape_string($tmp[10])."', map='".mysqli_real_escape_string($tmp[11])."', location='".mysqli_real_escape_string($tmp[12])."', coord_x='".intval($tmp[13])."', coord_y='".intval($tmp[14])."', deadline='".date("Y-m-d", strtotime($tmp[15]))."', entryportal='".intval($tmp[16])."', last_modification='".date("Y-m-d", strtotime($tmp[17]))."' WHERE solv_uid='".intval($tmp[0])."'", $db);
            $solv_termine[$i] = $tmp;
        } else {
            array_splice($solv_termine, $i, 1);
            $i--;
        }
    }
    return $solv_termine;
}

function go2oldata() {
    $db = DbUtils::fromEnv()->getDb();

    $url = "http://www.go2ol.ch/index.asp";
    $file = iconv('ISO-8859-1', 'UTF-8', load_url($url));
    $go2ol_termine = [];
    $res = preg_match_all("/<td.*><a.*href=\"(?P<link>[^\"]+)\".*>(?P<name>.+)<\\/a>.*<\\/td>\\s*<td.*>\\s*<img.*src=\"(?P<post>[^\"]+)\".*>\\s*<label>\\s*<input name=\"solv_uid\" type=\"hidden\" id=\"solv_uid\" value=\"(?P<solv_uid>.+)\".*>\\s*<\\/label>\\s*<\\/td>\\s*<td.*>\\s*<div.*>(?P<verein>.+)<\\/div>\\s*<\\/td>\\s*<td.*>\\s*<div.*>(?P<datum>.+)<\\/div>\\s*<\\/td>\\s*<td.*><div.*>(?P<meldeschluss_ohne>.+)<\\/div>\\s*<\\/td>\\s*<td.*>\\s*<div.*>(?P<meldeschluss_mit>.+)<\\/div>\\s*<\\/td>/i", $file, $matches);
    // print_r($matches);
    for ($i = 0; $i < count($matches["link"]); $i++) {
        $res = preg_match("/(.*\\/|^)([a-zA-Z0-9]+)(\\/.*|$)/", $matches["link"][$i], $matchestmp);
        $go2olident = ($res ? $matchestmp[2] : "");
        $tmp = [];
        $tmp["ident"] = $go2olident;
        $tmp["link"] = $matches["link"][$i];
        $tmp["name"] = $matches["name"][$i];
        $tmp["post"] = $matches["post"][$i];
        $tmp["solv_uid"] = intval($matches["solv_uid"][$i]);
        $tmp["verein"] = $matches["verein"][$i];
        $tmp["datum"] = $matches["datum"][$i];
        $tmp["meldeschluss"] = [];
        $ohne = strtotime($matches["meldeschluss_ohne"][$i]);
        if ($ohne > 0) {
            $tmp["meldeschluss"][] = $ohne;
        }
        $mit = strtotime($matches["meldeschluss_mit"][$i]);
        if ($mit > 0) {
            $tmp["meldeschluss"][] = $mit;
        }
        $go2ol_termine[] = $tmp;
        if ($tmp["solv_uid"] > 0) {
            $result = $db->query("SELECT solv_uid FROM termine_go2ol WHERE solv_uid='".intval($tmp["solv_uid"])."'", $db);
            $num = mysqli_num_rows($result);
            if ($num == 0) {
                $db->query("INSERT INTO termine_go2ol (solv_uid) VALUES ('".intval($tmp["solv_uid"])."')", $db);
            }
            $db->query("UPDATE termine_go2ol SET link='".mysqli_real_escape_string($tmp["link"])."', ident='".mysqli_real_escape_string($tmp["ident"])."', name='".mysqli_real_escape_string($tmp["name"])."', post='".mysqli_real_escape_string($tmp["post"])."', verein='".mysqli_real_escape_string($tmp["verein"])."', datum='".mysqli_real_escape_string($tmp["datum"])."', meldeschluss1='".date("Y-m-d", $ohne)."', meldeschluss2='".date("Y-m-d", $mit)."' WHERE solv_uid='".intval($tmp["solv_uid"])."'", $db);
        }
    }
    return $go2ol_termine;
}

function load_url($url) {
    $date_utils = AbstractDateUtils::fromEnv();
    $res = preg_match("/^https?\\:\\/\\/([^\\/]+)/", $url, $matches);
    $filename = "temp/".md5($url)."-".$date_utils->getCurrentDateInFormat("Y-m-d")."-".$matches[1].".txt";
    if (is_file($filename)) {
        $file = file_get_contents($filename);
    } else {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $file = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($filename, "w+");
        fwrite($fp, $file);
        fclose($fp);
    }
    return $file;
}
