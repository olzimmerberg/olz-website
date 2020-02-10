<?php

session_start();

require_once 'admin/olz_init.php';

$solv_events_fields = ["solv_uid" => "unique_id", "date" => "date", "duration" => "duration", "kind" => "kind", "day_night" => "day_night", "national" => "national", "region" => "region", "type" => "type", "name" => "event_name", "link" => "event_link", "club" => "club", "map" => "map", "location" => "location", "coord_x" => "coord_x", "coord_y" => "coord_y", "deadline" => "deadline", "entryportal" => "entryportal", "last_modification" => "last_modification"];
$solv_entryportals = [1 => "GO2OL", 2 => "picoTIMING", 3 => "anderes"];

function zeitins($zeit) {
    $zeit = trim($zeit);
    $res = preg_match("/(([0-9]+)[:\\.])?([0-9]+)[:\\.]([0-9]+)/", $zeit, $matches);
    if (!$res) {
        return -1;
    }
    $h = $matches[2];
    $m = $matches[3];
    $s = $matches[4];
    return $h * 3600 + $m * 60 + $s;
}

function automat_solv_view() {
    global $db, $solv_events_fields, $_GET, $_SESSION;
    if (isset($_GET['merge'], $_GET['into'])) {
        if (!isset($_SESSION['auth'])) {
            echo "<div style='color:red;'>Du bist nicht eingeloggt!</div>";
        } else {
            $merge = intval($_GET['merge']);
            $into = intval($_GET['into']);
            if ($merge > 0 && $into > 0) {
                $db->query("UPDATE solv_people SET same_as='".intval($into)."' WHERE same_as='".intval($merge)."'");
            }
        }
    }
    $res_p = $db->query("SELECT p1.name AS name1, p1.birthyear AS birthyear1, p1.residence AS residence1, p1.id AS id1, p2.name AS name2, p2.birthyear AS birthyear2, p2.residence AS residence2, p2.id AS id2 FROM solv_people p1 JOIN solv_people p2 ON (p1.same_as<p2.same_as)");
    $dists = [];
    for ($i = 0; $i < $res_p->num_rows; $i++) {
        $dist_name = levenshtein($row_p['name1'], $row_p['name2']);
        $dist_birthyear = levenshtein(str_pad($row_p['birthyear1'], 4, "0", STR_PAD_LEFT), str_pad($row_p['birthyear2'], 4, "0", STR_PAD_LEFT));
        $dist_residence = levenshtein($row_p['residence1'], $row_p['residence2']);
        if ($dist_residence > 2) {
            $dist_residence = 2;
        }
        $dist = ($dist_name + $dist_birthyear + $dist_residence);
        if ($dist < 10) {
            if (!isset($dists[$dist])) {
                $dists[$dist] = [];
            }
            $dists[$dist][] = [$row_p['name1'], $row_p['birthyear1'], $row_p['residence1'], $row_p['id1'], $row_p['name2'], $row_p['birthyear2'], $row_p['residence2'], $row_p['id2']];
        }
        $row_p = $res_p->fetch_assoc();
    }
    echo "<h2>&Auml;hnliche Namen</h2><table>";
    for ($dist = 1; $dist < 10; $dist++) {
        if (!isset($dists[$dist])) {
            continue;
        }
        for ($i = 0; $i < count($dists[$dist]); $i++) {
            echo "<tr><td>".($i == 0 ? $dist : "")."</td><td><a href='?merge=".$dists[$dist][$i][7]."&into=".$dists[$dist][$i][3]."' style='font-family:monospace; background-color:".($dists[$dist][$i][0] == $dists[$dist][$i][4] ? "rgb(210,255,210)" : "rgb(220,220,220)").";'>".$dists[$dist][$i][0]."</a> (".str_pad($dists[$dist][$i][1], 2, "0", STR_PAD_LEFT)."; ".$dists[$dist][$i][2].")</td><td><a href='?merge=".$dists[$dist][$i][3]."&into=".$dists[$dist][$i][7]."' style='font-family:monospace; background-color:".($dists[$dist][$i][0] == $dists[$dist][$i][4] ? "rgb(210,255,210)" : "rgb(220,220,220)").";'>".$dists[$dist][$i][4]."</a> (".str_pad($dists[$dist][$i][5], 2, "0", STR_PAD_LEFT)."; ".$dists[$dist][$i][6].")</td></tr>";
        }
    }
    echo "</table>";
}
function automat_solv_update($incr = true) {
    global $db, $solv_events_fields;
    $year = date('Y');
    if (!$incr) {
        $year = 2006;
    }
    $yearly_data = [];
    $rank_data = [];
    while (true) {
        $ch = curl_init("https://www.o-l.ch/cgi-bin/fixtures?&year=".$year."&kind=&csv=1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resp = utf8_encode(curl_exec($ch));
        curl_close($ch);
        $data = str_getcsv($resp, "\n");
        $header = false;
        for ($i = 0; $i < count($data); $i++) {
            $tmp = str_getcsv($data[$i], ";");
            if ($i == 0) {
                $header = $tmp;
            } else {
                $data[$i] = [];
                for ($j = 0; $j < count($header); $j++) {
                    $data[$i][$header[$j]] = $tmp[$j];
                }
            }
        }
        array_splice($data, 0, 1);

        $last_modification = [];
        $old_solv_uids = [];
        $res_e = $db->query("SELECT solv_uid, last_modification FROM solv_events WHERE YEAR(date)='".intval($year)."'");
        for ($i = 0; $i < $res_e->num_rows; $i++) {
            $row_e = $res_e->fetch_assoc();
            $last_modification[$row_e['solv_uid']] = $row_e['last_modification'];
            $old_solv_uids[$row_e['solv_uid']] = false;
        }

        $cnt_data = count($data);
        for ($i = 0; $i < $cnt_data; $i++) {
            $uid = $data[$i]['unique_id'];
            if (!isset($last_modification[$uid])) {
                $sql_key_tmp = [];
                $sql_val_tmp = [];
                foreach ($solv_events_fields as $db_key => $header_key) {
                    $sql_key_tmp[] = "`".DBEsc($db_key)."`";
                    $sql_val_tmp[] = "'".DBEsc($data[$i][$header_key])."'";
                }
                $db->query("INSERT INTO solv_events (".implode(", ", $sql_key_tmp).") VALUES (".implode(", ", $sql_val_tmp).")");
                echo "INSERTED ".$uid."<br>";
            } elseif ($last_modification[$uid] != $data[$i]['last_modification']) {
                $sql_tmp = [];
                foreach ($solv_events_fields as $db_key => $header_key) {
                    $sql_tmp[] = "`".DBEsc($db_key)."`='".DBEsc($data[$i][$header_key])."'";
                }
                $db->query("UPDATE solv_events SET ".implode(", ", $sql_tmp)." WHERE solv_uid='".intval($uid)."'");
                echo "UPDATED ".$uid."<br>";
            }
            $old_solv_uids[$uid] = true;
        }
        foreach ($old_solv_uids as $uid => $still_exists) {
            if (!$still_exists) {
                $db->query("DELETE FROM solv_events WHERE solv_uid='".intval($uid)."'");
                echo "DELETED ".$uid."<br>";
            }
        }

        $ch = curl_init("https://www.o-l.ch/cgi-bin/fixtures?mode=results&year=".$year."&json=1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resp = json_decode(utf8_encode(curl_exec($ch)), true);
        curl_close($ch);

        for ($i = 0; $i < count($resp["ResultLists"]); $i++) {
            $res = $resp["ResultLists"][$i];
            if (!isset($res['UniqueID']) || $res['UniqueID'] == 0 || isset($rank_data[$res['UniqueID']])) {
                continue;
            }
            if (!isset($rank_data[$res['UniqueID']])) {
                $rank_data[$res['UniqueID']] = [];
            }
            $rank_data[$res['UniqueID']][] = $res;
        }

        if (count($data) < 3) {
            break;
        }
        if (date('Y') + 3 < $year) {
            break;
        }
        $yearly_data[$year] = $data;
        $year++;
    }

    // Check for new start-list/results for last 3 month's events
    $res_e = $db->query("SELECT * FROM solv_events WHERE DATE(CURRENT_TIMESTAMP-INTERVAL 3 MONTH)<date AND date<=DATE(CURRENT_TIMESTAMP) AND rank_link IS NULL");
    for ($i = 0; $i < $res_e->num_rows; $i++) {
        $row_e = $res_e->fetch_assoc();
        $solv_uid = intval($row_e['solv_uid']);
        echo "<br>".$i." - ".json_encode($row_e)." - ".(isset($rank_data[$solv_uid]) ? json_encode($rank_data[$solv_uid]) : ":(")."<br>";
        if (!isset($rank_data[$solv_uid])) {
            $rank_data[$solv_uid] = [];
        }
        for ($result_index = 0; $result_index < count($rank_data[$solv_uid]); $result_index++) {
            if (!isset($rank_data[$solv_uid][$result_index]['ResultListID'])) {
                continue;
            }
            $rank_id = $rank_data[$solv_uid][$result_index]['ResultListID'];
            $db->query("UPDATE solv_events SET rank_link='results?rl_id=".intval($rank_id)."' WHERE solv_uid='".intval($row_e['solv_uid'])."'");
            $ch = curl_init("https://www.o-l.ch/cgi-bin/results?rl_id=".intval($rank_id)."&club=zimmerberg");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $resp = utf8_encode(curl_exec($ch));
            curl_close($ch);

            // PARSE
            $pres = [];
            $pre = "";
            $text = "";
            $num = strlen($resp);
            $last_pre = 0;
            for ($j = 0; $j < $num; $j++) {
                if (substr($resp, $j, 6) == "</pre>") {
                    $r = preg_match("/\\<a\\s+([^\\>]*\\s+)?href\\=\"results[^\"]*(\\&|\\?)kat\\=([^\"\\&\\=]+)[^\"]*\"(\\s+[^\\>]*)?\\>([^\\>]+)\\<\\/a\\>/", substr($resp, $last_pre, $j - strlen($pre) - $last_pre), $matches);
                    if (!$r) {
                        echo "ERROR: No Match for Class definition<br>";
                    }
                    if ($matches[3] != urlencode($matches[5])) {
                        echo "ERROR: Inconsistent Match for Class definition<br>";
                    }
                    if ($r) {
                        $last_pre = $j + 6;
                        $inpre = false;
                        $pres[$matches[5]] = $pre;
                    }
                }
                if ($inpre) {
                    $pre .= $resp[$j];
                }
                if (substr($resp, $j, 5) == "<pre>") {
                    $inpre = true;
                    $pre = "";
                    $j += 4;
                }
            }

            $keys = array_keys($pres);
            $num = count($keys);
            for ($j = 0; $j < $num; $j++) {
                $kat = [];
                $pre = $pres[$keys[$j]];
                $r = preg_match("/^\\s*\\(\\s*([0-9\\.]+)\\s*km\\s*,\\s*([0-9]+)\\s*m\\s*,\\s*([0-9]+)\\s*Po\\.\\s*\\)\\s*([0-9]+)\\s*Teilnehmer/", $pre, $matches);
                if ($r) {
                    $kat["strecke"] = floatval($matches[1]) * 1000;
                    $kat["hoehe"] = intval($matches[2]);
                    $kat["numposten"] = intval($matches[3]);
                    $kat["numteilnehmer"] = intval($matches[4]);
                } else {
                    echo "ERROR: preg_match fail";
                }
                echo json_encode($kat)."<br>";
                $pre = substr($pre, strlen($matches[0]));
                echo htmlspecialchars($pre)."<br>";
                while (true) {
                    $r = preg_match("/^\\s*(\\s*([0-9]+)\\.)?\\s*(((?![\\s]{2,})[^0-9])+)\\s+([0-9]{2}|)\\s+([^\\s0-9]((?![\\s]{2,})[\\S ])+)\\s+(([^\\s0-9]((?![\\s]{2,})[\\S ])+)\\s+)?([0-9\\:]+|[A-Za-z\\.\\/ ]+)\\s*/", $pre, $matches);
                    $pre = substr($pre, strlen($matches[0]));
                    if (!$r) {
                        break;
                    }
                    $cur_rang = intval($matches[2]);
                    $cur_name = $matches[3];
                    $cur_birthyear = $matches[5];
                    $cur_residence = $matches[6];
                    $cur_club = $matches[9];
                    $cur_zeit = zeitins($matches[11]);
                    echo $cur_rand." - ".$cur_name." - ".$cur_birthyear." - ".$cur_residence." - ".$cur_club." - ".$cur_zeit."<br>";
                    $member = preg_match("/zimmerb/i", $cur_residence.$cur_club);
                    if ($member) {
                        $res_p = $db->query("SELECT id FROM solv_people WHERE name LIKE '".DBEsc($cur_name)."' AND residence LIKE '".DBEsc($cur_residence)."' AND birthyear='".intval($cur_birthyear)."'");
                        if ($res_p->num_rows == 0) {
                            $db->query("INSERT INTO solv_people (same_as, name, birthyear, residence, member) VALUES (NULL, '".DBEsc($cur_name)."', '".intval($cur_birthyear)."', '".DBEsc($cur_residence)."', '1')");
                            $person = $db->insert_id;
                            $db->query("UPDATE solv_people SET same_as='".intval($person)."' WHERE id='".intval($person)."' AND same_as IS NULL");
                        } else {
                            $row_p = $res_p->fetch_assoc();
                            $person = $row_p['id'];
                        }
                        $res_r = $db->query("SELECT id FROM solv_results WHERE person='".intval($person)."' AND event='".intval($row_e['solv_uid'])."'");
                        if ($res_r->num_rows == 0) {
                            $db->query("INSERT INTO solv_results (person, event, class, result) VALUES ('".intval($person)."', '".intval($row_e['solv_uid'])."', '".DBEsc($keys[$j])."', '".$cur_zeit."')");
                        }
                    }
                }
            }
        }
    }
}

$modules = [
    'all' => [
        'update' => function () {
            $incremental = true;
            automat_solv_update($incremental);
        },
    ],
    'solv' => [
        'view' => function () {
            automat_solv_view();
        },
        'update' => function () {
            $incremental = true;
            if ($_GET['incremental'] == 'no') {
                $incremental = false;
            }
            automat_solv_update($incremental);
        },
    ],
];
$path_info = $_SERVER['PATH_INFO'];

do {
    $res = preg_match("/^\\/?(".implode('|', array_keys($modules)).")/", $path_info, $matches);
    if (!$res) {
        break;
    }
    $modules = $modules[$matches[1]];
    $path_info = substr($path_info, strlen($matches[0]));
} while (is_array($modules));
if (is_callable($modules)) {
    $modules();
} else {
    $keys = array_keys($modules);
    echo "<h2>Diese Seite existiert nicht</h2>";
    if ($keys && is_array($keys) && count($keys) > 0) {
        echo "Meinten Sie:<br>";
        for ($i = 0; $i < count($keys); $i++) {
            echo "<a href='./".$keys[$i]."/'>".$keys[$i]."</a><br>";
        }
    }
}
