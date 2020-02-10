<?php

function solvdataforyear($year) {
    if (!$year) {
        $year = date("Y");
    }

    $url = "http://o-l.ch/cgi-bin/fixtures?&year=".$year."&kind=&csv=1";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file = curl_exec($ch);
    curl_close($ch);
    $solv_termine = preg_split("/[\n\r]+/i", $file);
    for ($i = 0; $i < count($solv_termine); $i++) {
        $tmp = explode(";", $solv_termine[$i]);
        $tmp["datum"] = strtotime($tmp[1]);
        $tmp["name"] = $tmp[8];
        if ($tmp["datum"]) {
            $solv_termine[$i] = $tmp;
        } else {
            array_splice($solv_termine, $i, 1);
            $i--;
        }
    }
    //echo "<pre>".nl2br(var_export($solv_termine, true))."</pre>";

    return $solv_termine;
}

function solvdataforuid($uid) {
    // URL, die durchsucht werden soll
    $url = "http://www.o-l.ch/cgi-bin/fixtures?mode=show&unique_id=".$uid;
    $file = geturlcontent($url);
    $solv_lauf = ["datum" => "", "meldeschluss" => "", "region" => "", "abk" => "", "links" => "", "name" => "", "verein" => "", "karte" => "", "map" => "", "rangliste" => "", "startliste" => ""];
    $solv_lauf["links"] = [];

    if (trim($file) == "") {
        mail("simon.hatt@olzimmerberg.ch", "Parse Error", "SOLV-Lauf-Daten konnten nicht abgefragt werden.\nDie URL \"".$url."\" ist möglicherweise fehlerhaft.", "From: OL Zimmerberg <system@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64");
    } else {
        $numi = strlen($file);
        $keys = ["Datum:" => "datum", "Region:" => "region", "Club:" => "verein", "Karte:" => "karte", "Meldeschluss:" => "meldeschluss", "Koordinaten:" => "map"];
        $checkpoint = "start";
        $key = "";
        $linktmp = [];
        for ($i = 0; $i < $numi; $i++) {
            if ($checkpoint == "trgrey" && check($file, $i, "</tr")) {
                $checkpoint = "tablegrey";
            }
            if ($checkpoint == "tdgrey" && check($file, $i, "</td")) {
                $checkpoint = "trgrey";
            }
            if ($checkpoint == "tablecont" && check($file, $i, "</table")) {
                $checkpoint = "tdgrey";
            }
            if (($checkpoint == "trcont" || $checkpoint == "valtrcont" || $checkpoint == "keytrcont") && check($file, $i, "</tr")) {
                $checkpoint = "tablecont";
            }
            if ($checkpoint == "valtdcont" && check($file, $i, "</td")) {
                $checkpoint = "trcont";
            }
            if ($checkpoint == "valtrcont" && check($file, $i, "<td")) {
                $val = get($file, $i, ">", "</td");
                //echo "Key: ".$key." - Val: ".$val."<br>\n";
                if ($keys[$key]) {
                    $solv_lauf[$keys[$key]] = $val;
                //echo $keys[$key]." => ".$val."<br>";
                } elseif (!is_bool(strpos($key, "Rangliste"))) {
                    $solv_lauf["rangliste"] = $key;
                } elseif (!is_bool(strpos($key, "Startliste"))) {
                    $solv_lauf["startliste"] = $key;
                }
                $checkpoint = "valtdcont";
            }
            if ($checkpoint == "keytdcont" && check($file, $i, "</td")) {
                $checkpoint = "valtrcont";
            }
            if ($checkpoint == "keytrcont" && check($file, $i, "<td")) {
                $key = get($file, $i, ">", "</td");
                //echo "Key: ".$key."<br>";
                $checkpoint = "keytdcont";
            }
            if ($checkpoint == "tablecont" && check($file, $i, "<tr")) {
                $checkpoint = "keytrcont";
            }
            if ($checkpoint == "tdgrey" && check($file, $i, "<table")) {
                $checkpoint = "tablecont";
            }
            if ($checkpoint == "linktext" && check($file, $i, "</p")) {
                $checkpoint = "tdgrey";
            }
            if ($checkpoint == "linkhref" && check($file, $i, ">")) {
                $text_tmp = get($file, $i, ">", "<");
                //echo "Text: ".$text_tmp."<br>\n";
                $linktmp["text"] = control_text(trim(str_replace("&raquo;", "", $text_tmp)));
                array_push($solv_lauf["links"], $linktmp);
                $checkpoint = "linktext";
            }
            if ($checkpoint == "alink" && check($file, $i, "href=\"")) {
                $link_tmp = control_link(get($file, $i, "href=\"", "\""));
                //echo "Link: ".$link_tmp."<br>\n";
                $linktmp = ["href" => $link_tmp, "text" => ""];
                $checkpoint = "linkhref";
            }
            if ($checkpoint == "plink" && check($file, $i, "<a")) {
                $checkpoint = "alink";
            }
            if ($checkpoint == "tdgrey" && check($file, $i, "<p")) {
                $checkpoint = "plink";
            }
            if ($checkpoint == "trgrey" && check($file, $i, "<td")) {
                $checkpoint = "tdgrey";
            }
            if ($checkpoint == "tablegrey" && check($file, $i, "<tr")) {
                $checkpoint = "trgrey";
            }
            if ($checkpoint == "name" && check($file, $i, "<table")) {
                $checkpoint = "tablegrey";
            }
            if ($checkpoint == "start" && check($file, $i, "<h2")) {
                $name_tmp = get($file, $i, ">", "<");
                $zupos = strrpos($name_tmp, ")");
                $aufpos = strrpos($name_tmp, "(");
                if (strlen($name_tmp) - $zupos < 3 && $zupos - $aufpos < 8) {
                    $abk_tmp = substr($name_tmp, $aufpos + 1, $zupos - $aufpos - 1);
                    $name_tmp = substr($name_tmp, 0, $aufpos);
                }
                //echo "Name: ".$name_tmp." - Abk: ".$abk_tmp."<br>\n";
                $solv_lauf["name"] = control_text($name_tmp);
                $solv_lauf["abk"] = control_text($abk_tmp);
                $checkpoint = "name";
            }
        }
    }
    if ($solv_lauf["datum"]) {
        $solv_lauf["datum"] = control_date(parse_date($solv_lauf["datum"]));
    }
    if ($solv_lauf["meldeschluss"]) {
        $solv_lauf["meldeschluss"] = control_date(parse_date($solv_lauf["meldeschluss"]));
    }
    if ($solv_lauf["map"]) {
        $solv_lauf["map"] = parse_map(control_error($solv_lauf["map"]));
    }
    if ($solv_lauf["rangliste"]) {
        $solv_lauf["rangliste"] = control_link(parse_linkurl($solv_lauf["rangliste"]));
    }
    if ($solv_lauf["startliste"]) {
        $solv_lauf["startliste"] = control_link(parse_linkurl($solv_lauf["startliste"]));
    }
    return $solv_lauf;
}

function go2oldata() {
    $url = "http://www.go2ol.ch/index.asp";
    $file = geturlcontent($url);
    $go2ol = [];
    $res = preg_match_all("/<td.*><a.*href=\"(?P<link>[^\"]+)\".*>(?P<name>.+)<\\/a>.*<\\/td>\\s*<td.*>\\s*<img.*src=\"(?P<post>[^\"]+)\".*>\\s*<label>\\s*<input name=\"solv_uid\" type=\"hidden\" id=\"solv_uid\" value=\"(?P<solv_uid>.+)\".*>\\s*<\\/label>\\s*<\\/td>\\s*<td.*>\\s*<div.*>(?P<verein>.+)<\\/div>\\s*<\\/td>\\s*<td.*>\\s*<div.*>(?P<datum>.+)<\\/div>\\s*<\\/td>\\s*<td.*><div.*>(?P<meldeschluss_ohne>.+)<\\/div>\\s*<\\/td>\\s*<td.*>\\s*<div.*>(?P<meldeschluss_mit>.+)<\\/div>\\s*<\\/td>/i", $file, $matches);
    //print_r($matches);
    for ($i = 0; $i < count($matches["link"]); $i++) {
        $tmp = [];
        $tmp["link"] = $matches["link"][$i];
        $tmp["name"] = $matches["name"][$i];
        $tmp["post"] = $matches["post"][$i];
        $tmp["solv_uid"] = $matches["solv_uid"][$i];
        $tmp["verein"] = $matches["verein"][$i];
        $tmp["datum"] = $matches["datum"][$i];
        $tmp["meldeschluss"] = [];
        $ohne = strtotime($matches["meldeschluss_ohne"][$i]);
        $mit = strtotime($matches["meldeschluss_mit"][$i]);
        if ($ohne == $mit || $mit == 0) {
            $tmp["meldeschluss"][0] = $ohne;
        } elseif ($ohne == 0) {
            $tmp["meldeschluss"][0] = $mit;
        } else {
            $tmp["meldeschluss"][0] = $ohne;
            $tmp["meldeschluss"][1] = $mit;
        }
        array_push($go2ol, $tmp);
    }
    return $go2ol;
}

// ---

function control_error($result) {
    $unallowed = ["</td", "</tr", "</table"];
    for ($i = 0; $i < count($unallowed); $i++) {
        if (!is_bool(strpos($result, $unallowed[$i]))) {
            mail("simon.hatt@olzimmerberg.ch", "Parse Error", "Control_Error()\nUnerlaubte Zeichen in geparstem Text.\n\"".$unallowed[$i]."\" wurde in \"".$result."\" gefunden.", "From: OL Zimmerberg <system@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64");
            return "";
        }
    }
    return $result;
}

function control_text($result) {
    $result = control_error($result);
    if (!is_bool(strpos($result, "<"))) {
        mail("simon.hatt@olzimmerberg.ch", "Parse Error", "Control_Text()\nUnerlaubte Zeichen in geparstem Text.\n\"<\" wurde in \"".$result."\" gefunden.", "From: OL Zimmerberg <system@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64");
        return "";
    }
    if (!is_bool(strpos($result, ">"))) {
        mail("simon.hatt@olzimmerberg.ch", "Parse Error", "Control_Text()\nUnerlaubte Zeichen in geparstem Text.\n\">\" wurde in \"".$result."\" gefunden.", "From: OL Zimmerberg <system@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64");
        return "";
    }
    return $result;
}

function control_link($result) {
    $result = control_text($result);
    if (is_bool(strpos($result, "/")) && is_bool(strpos($result, ".")) && is_bool(strpos($result, "?")) && is_bool(strpos($result, "&"))) {
        mail("simon.hatt@olzimmerberg.ch", "Parse Error", "Control_Link()\nKein Link, da weder \"/\" noch \".\" in \"".$result."\" gefunden.", "From: OL Zimmerberg <system@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64");
        return "";
    }
    return $result;
}

function control_date($result) {
    $result = control_text($result);
    if (!is_numeric($result)) {
        //mail("simon.hatt@olzimmerberg.ch","Parse Error","Control_Date()\nKein Datum (UNIX-Timestamp), da \"".$result."\" nicht numerisch ist.","From: OL Zimmerberg <system@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64");
        return "";
    }
    return $result;
}

// ---

function parse_date($str) {
    $numberfilter = "";
    for ($i = 0; $i < strlen($str); $i++) {
        if (is_numeric($str[$i]) || $str[$i] == "." || $str[$i] == " ") {
            $numberfilter .= $str[$i];
        }
    }
    return strtotime($numberfilter);
}

function parse_linkurl($str) {
    $pos = strpos($str, "http://");
    if ($pos == false) {
        $pos = strpos($str, "href=\"");
        $pos += 6;
    }
    $url_tmp = "";
    while ($str[$pos] != " " && $str[$pos] != "\"" && $str[$pos] != ">" && $pos < strlen($str)) {
        $url_tmp .= $str[$pos];
        $pos++;
    }
    return $url_tmp;
}

function parse_map($str) {
    $coords = [];
    $coordtmp = "";
    for ($i = 0; $i < strlen($str); $i++) {
        if (is_numeric($str[$i])) {
            while (is_numeric($str[$i])) {
                $coordtmp .= $str[$i];
                $i++;
            }
            array_push($coords, $coordtmp);
            $coordtmp = "";
        }
    }
    $return = [];
    for ($i = 0; $i < count($coords); $i++) {
        if ($coords[$i] > 400000 and $coords[$i] < 900000) {
            $return["x"] = $coords[$i];
        } elseif ($coords[$i] > 50000 and $coords[$i] < 400000) {
            $return["y"] = $coords[$i];
        }
    }
    return $return;
}

function parse_uid($str) {
    $uid_tmp = "";
    $pos = strpos($str, "unique_id=");
    if (!is_bool($pos)) {
        $pos += 10;
    }
    $uids = [];
    for ($i = $pos; $i < strlen($str); $i++) {
        if (is_numeric($str[$i])) {
            $uid_tmp .= $str[$i];
        } else {
            if ($uid_tmp != "") {
                array_push($uids, $uid_tmp);
            }
        }
    }
    if ($uid_tmp != "") {
        array_push($uids, $uid_tmp);
    }
    if (count($uids) == 1) {
        return $uids[0];
    }
    for ($i = 0; $i < count($uids); $i++) {
        if ($uids[$i] > 999 && $uids[$i] < 100000) {
            return $uids[$i];
        }
    }
}

function parse_go2olcode($str) {
    return get($str, $pos, "/", "/index");
}

function parse_links($str) {
    $links = [];
    $linktype = "";
    $linkhref = "";
    $linkname = "";
    $linksense = "";
    $status = "text";
    //echo $str."<br>---<br>";
    for ($i = 0; $i < strlen($str); $i++) {
        if ($status == "div" && check($str, $i, "</div")) {
            array_push($links, ["type" => $linktype, "href" => $linkhref, "text" => $linkname]);
            $linktype = "";
            $linkhref = "";
            $linkname = "";
            $status = "text";
        }
        if ($status == "aname" && check($str, $i, "</a")) {
            $status = "div";
        }
        if ($status == "atag" && check($str, $i, ">")) {
            $linkname = get($str, $i, ">", "<");
            //echo $linkname."<br>";
            $status = "aname";
        }
        if ($status == "ahref" && check($str, $i, "='")) {
            $linkhref = get($str, $i, "='", "'");
            //echo $linkhref."<br>";
            $status = "atag";
        }
        if ($status == "ahref" && check($str, $i, "=\"")) {
            $linkhref = get($str, $i, "=\"", "\"");
            //echo $linkhref."<br>";
            $status = "atag";
        }
        if ($status == "atag" && check($str, $i, "href")) {
            $status = "ahref";
        }
        if ($status == "classgot" && check($str, $i, "<a")) {
            $status = "atag";
        }
        if ($status == "class" && check($str, $i, "link")) {
            if (substr($str, $i, 7) == "linkint") {
                $linktype = "linkint";
            }
            if (substr($str, $i, 7) == "linkext") {
                $linktype = "linkext";
            }
            if (substr($str, $i, 7) == "linkpdf") {
                $linktype = "linkpdf";
            }
            if (substr($str, $i, 8) == "linkmail") {
                $linktype = "linkmail";
            }
            if (substr($str, $i, 7) == "linkmap") {
                $linktype = "linkmap";
            }
            if (substr($str, $i, 7) == "linkoev") {
                $linktype = "linkoev";
            }
            if (substr($str, $i, 7) == "linkimg") {
                $linktype = "linkimg";
            }
            if (substr($str, $i, 9) == "linkmovie") {
                $linktype = "linkmovie";
            }
            //echo $linktype."<br>";
            $status = "classgot";
        }
        if ($status == "div" && check($str, $i, "class")) {
            $status = "class";
        }
        if ($status == "text" && check($str, $i, "<div")) {
            $status = "div";
        }
    }
    return $links;
}

// ---

function arraytostr($array, $level = 0) {
    $str = "(\n";
    if (is_array($array)) {
        $keys = array_keys($array);
        for ($i = 0; $i < count($keys); $i++) {
            for ($j = 0; $j < $level; $j++) {
                $str .= "\t";
            }
            $str .= "[".$keys[$i]."] => ".(is_array($array[$keys[$i]]) ? arraytostr($array[$keys[$i]], $level + 1) : $array[$keys[$i]])."\n";
        }
    }
    $str .= ")";
    return $str;
}

function check($file, $position, $value) {
    if (substr($file, $position, strlen($value)) == $value) {
        return true;
    }
    return false;
}

function get($file, $position, $value_bef, $value_aft) {
    $str = "";
    $numi = strlen($file);
    for ($i = $position; $i < $numi; $i++) {
        if ($record && (substr($file, $i, strlen($value_aft)) == $value_aft)) {
            return $str;
        }
        if ($record) {
            $str .= $file[$i];
        }
        if (!$record && (substr($file, $i, strlen($value_bef)) == $value_bef)) {
            $i += strlen($value_bef) - 1;
            $record = true;
        }
    }
}

function geturlcontent($url) {
    $allowedcharsets = ["ISO-8859-1", "ISO-8859-2", "ISO-8859-3", "WINDOWS-1250", "WINDOWS-1252", "UTF-8"];
    // ---
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file = curl_exec($ch);
    curl_close($ch);
    $meta = 0;
    $metacontent = "";
    for ($i = 0; $i < 1111; $i++) {
        if ($meta != 0 && check($file, $i, ">")) {
            if ($metacontent != "" && $meta == 3) {
                break;
            }
            $meta = 0;
        }
        if ($meta == 1 && check($file, $i, "http-equiv")) {
            $meta = 2;
        }
        if ($meta == 2 && check($file, $i, "Content-Type")) {
            $meta = 3;
        }
        if ($meta != 0 && check($file, $i, "content=")) {
            $str = 0;
            for ($i = $i + 8; $i < 1111; $i++) {
                if ($file[$i] == "\"") {
                    $str = ($str == 0) ? 1 : 0;
                }
                if ($str == 0 && ($file[$i] == " " || $file[$i] == ">")) {
                    break;
                }
                $metacontent .= $file[$i];
            }
        }
        if (check($file, $i, "<meta")) {
            $meta = 1;
            $metacontent = "";
        }
    }
    $charset = "";
    for ($i = 0; $i < strlen($metacontent); $i++) {
        if (check($metacontent, $i, "charset=")) {
            for ($i = $i + 8; $i < strlen($metacontent); $i++) {
                if ($metacontent[$i] == " " || $metacontent[$i] == ";" || $metacontent[$i] == "," || $metacontent[$i] == "\"") {
                    break;
                }
                $charset .= $metacontent[$i];
            }
        }
    }
    if (is_bool(array_search(strtoupper(trim($charset)), $allowedcharsets)) && $charset != "") {
        mail("simon.hatt@olzimmerberg.ch", "Parse Error", "Die URL \"".$url."\" konnte nicht richtig dekodiert werden.\nDie Zeichenkodierung \"".$charset."\" ist unbekannt.", "From: OL Zimmerberg <system@olzimmerberg.ch>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8 \nContent-Transfer-Encoding: base64");
        $charset = "UTF-8";
    }
    if (strlen($charset) > 0) {
        $file = iconv($charset, "UTF-8", $file);
    }
    return $file;
}
