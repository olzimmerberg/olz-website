<?php

// =============================================================================
// Die Kopfzeile der Website.
// =============================================================================

function olz_header_bar($args = []): string {
    global $_CONFIG, $db, $zugriff, $button_name;
    $out = '';

    require_once __DIR__.'/../../../config/database.php';
    require_once __DIR__.'/../../../config/server.php';
    require_once __DIR__.'/../../../admin/olz_functions.php';
    require_once __DIR__.'/../../../image_tools.php';
    require_once __DIR__.'/../../../file_tools.php';

    $out .= "<div id='header-bar' class='header-bar menu-closed'>";

    $out .= "<div class='above-header'>";
    $out .= "<div class='account-menu-container'>";

    require_once __DIR__.'/../../auth/olz_account_menu/olz_account_menu.php';
    $out .= olz_account_menu();

    $out .= "</div>";
    $out .= "</div>";

    $out .= "<div class='below-header'>";
    $out .= "<div id='menu-container' class='menu-container'>";

    require_once __DIR__."/../olz_menu/olz_menu.php";
    $out .= olz_menu();

    $out .= "</div>"; // menu-container
    $out .= "</div>"; // below-header

    $out .= "<div id='menu-switch' onclick='toggleMenu()' />";
    $out .= "<img src='icns/menu_hamburger.svg' alt='' class='menu-hamburger noborder' />";
    $out .= "<img src='icns/menu_close.svg' alt='' class='menu-close noborder' />";
    $out .= "</div>";

    $out .= "<div class='header-content-container'>";
    $out .= "<div class='header-content-scroller'>";
    $out .= "<div class='header-content'>";

    // TODO: Remove switch as soon as Safari properly supports SVGs.
    if (preg_match('/Safari/i', $_SERVER['HTTP_USER_AGENT'])) {
        $out .= "<img src='icns/olzschatten.png' alt='' class='noborder' id='olz-logo' />";
    } else {
        $out .= "<img src='icns/olz_logo.svg' alt='' class='noborder' id='olz-logo' />";
    }
    $out .= "<div style='flex-grow:1;'></div>";

    // OLZ Statistik Trainings/Wettkämpfe 2014
    $header_spalten = 2;

    $db_table = "aktuell";
    $zugriff = ((($_SESSION['auth'] ?? null) == 'all') or (in_array($db_table, explode(' ', $_SESSION['auth'])))) ? true : false;
    $button_name = 'button'.$db_table;
    if (isset($_GET[$button_name])) {
        $_POST[$button_name] = $_GET[$button_name];
    }
    if (isset($_POST[$button_name])) {
        $_SESSION['edit']['db_table'] = $db_table;
    }

    $sql = "SELECT * FROM {$db_table} WHERE (on_off != '0') AND (typ LIKE '%box%') ORDER BY typ ASC";
    $result = $db->query($sql);

    $ganze = [];
    while ($row = mysqli_fetch_array($result)) {
        $wichtig = substr($row["typ"], 3 + strpos(strtolower($row["typ"]), "box"));
        if ($wichtig == "" || !in_array($wichtig, [0, 1, 2])) {
            $wichtig = 2;
        }

        // Dateicode einfügen
        $textlang = $row["textlang"];
        preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $textlang, $matches);
        //preg_match_all("/<datei([0-9]+)[^>]*(\s+file=(\"|\')([^\"\']+)(\"|\'))[^>]*>/i", $textlang, $matches_file);

        for ($i = 0; $i < count($matches[0]); $i++) {
            $tmptext = $matches[4][$i];
            $tmpfile = $matches_file[4][$i];
            //if($_SESSION['auth']=='all') $out .= $i."***2".$matches_file[4][$i]."<br>";
            if (mb_strlen($tmptext) < 1) {
                $tmptext = "Datei ".$matches[1][$i];
            }
            $tmp_html = olz_file($db_table, $row["id"], intval($matches[1][$i]), $tmptext);
            $textlang = str_replace($matches[0][$i], $tmp_html, $textlang);
        }

        $tmp = ["id" => $row["id"], "wichtig" => $wichtig, "titel" => $row["titel"], "textlang" => $textlang];
        array_push($ganze, $tmp);
    }

    $html_first_row = "";
    for ($i = 0; $i < $header_spalten; $i++) {
        $html_first_row = htmlbox($ganze[0], 1, $zugriff, $button_name).$html_first_row;
        array_splice($ganze, 0, 1);
    }
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%training%')";
    $result = $db->query($sql);
    $training = mysqli_fetch_array($result);
    $sql = "SELECT SUM(teilnehmer),count(*) FROM termine WHERE datum>'2013-12-31' AND teilnehmer>0 AND (typ LIKE '%ol%')";
    $result = $db->query($sql);
    $ol = mysqli_fetch_array($result);

    $statistik_text = "<div style='position:absolute; top:0px; right:0px;'><div class='box_ganz'><div style='border:none;'>
    <h3>Statistik 2014:</h3>
    <p><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[1]."</span> Trainings mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$training[0]."</span> TeilnehmerInnen
    <br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[1]."</span> Wettkämpfe mit<br><span style='font-size:140%;font-weight:bold;vertical-align:baseline;'>".$ol[0]."</span> TeilnehmerInnen</p>
    </div></div></div>";

    $out .= $html_first_row;

    // OLZ JOM-Counter 2020
    // --------------------
    // Chris Seitz - 15
    // Daniel Rohr - 12
    // Dominik Badertscher - 16
    // Giulia Borner - 45
    // Jan Hug - 17
    // Jonas Junker - 22
    // Julia Jakob - 489
    // Liliane Suter - 239
    // Lilly Gross - 42
    // Marc Bitterli - 9
    // Marc Breitenmoser - 8
    // Anik Bachmann - 68
    // Michael Laager - 64
    // Miriam Isenring - 69
    // Moritz Oetiker - 275
    // Philipp Tschannen - 249
    // Priska Badertscher - 51
    // Roger Fluri - 23
    // Simon Hatt - 20
    // Tiziana Rigamonti - 650
    $jom_solv_uids_2019 = [9610, 9543, 9781, 9636, 9542, 9541, 9380, 9390, 9950, 9815, 9821];
    $jom_solv_uids_2020 = [10086, 10228, 9901, 10049, 10197, 10201, 10239, 10253, 9915, 10247, 10317];
    $sql_kids = "
    SELECT
        sp1.name AS name,
        COUNT(*) AS cnt,
        GROUP_CONCAT(se.name SEPARATOR '\n') AS events
    FROM solv_results sr
        LEFT JOIN solv_people sp ON (sr.person=sp.id)
        LEFT JOIN solv_people sp1 ON (sp.same_as=sp1.id OR (sp.same_as IS NULL AND sp.id=sp1.id))
        LEFT JOIN solv_events se ON (sr.event=se.solv_uid)
    WHERE
        sr.event IN ('%%PLACEHOLDER_FOR_SOLV_UIDS%%')
        AND sr.class IN ('H10', 'H12', 'H14', 'H16', 'H18', 'D10', 'D12', 'D14', 'D16', 'D18')
    GROUP BY sp1.id
    ORDER BY cnt DESC"; // cnt DESC, se.date ASC
    $sql_j_und_s = "
    SELECT
        sp1.name AS name,
        COUNT(*) AS cnt,
        GROUP_CONCAT(se.name SEPARATOR '\n') AS events
    FROM solv_results sr
        LEFT JOIN solv_people sp ON (sr.person=sp.id)
        LEFT JOIN solv_people sp1 ON (sp.same_as=sp1.id OR (sp.same_as IS NULL AND sp.id=sp1.id))
        LEFT JOIN solv_events se ON (sr.event=se.solv_uid)
    WHERE
        sr.event IN ('%%PLACEHOLDER_FOR_SOLV_UIDS%%')
        AND sp1.id IN ('15', '12', '16', '45', '17', '22', '489', '239', '42', '9', '8', '68', '64', '69', '275', '249', '51', '23', '20', '650')
    GROUP BY sp1.id
    ORDER BY cnt DESC"; // cnt DESC, se.date ASC

    $htmlout_before = "<div style='position:absolute; top:0px; right:252px; z-index:1000; display:none;' id='%%PLACEHOLDER_FOR_ID%%'><div class='box_ganz'><div style='margin-top:8px; border:0px; overflow-y:scroll;'><div style='padding:5px;'><table>";
    $htmlout_before .= "<tr><th>Name</th><th style='text-align:right;'>Starts</th></tr>";
    $htmlout_after = "</table></div></div></div></div>";

    $sql_kids_2019 = str_replace(
        '%%PLACEHOLDER_FOR_SOLV_UIDS%%',
        implode("', '", $jom_solv_uids_2019),
        $sql_kids,
    );
    $result_kids_2019 = $db->query($sql_kids_2019);
    $starts_kids_2019 = 0;
    $htmlout_kids_2019 = str_replace('%%PLACEHOLDER_FOR_ID%%', 'ranking-kids-2019', $htmlout_before);
    while ($row = $result_kids_2019->fetch_assoc()) {
        $starts_kids_2019 += intval($row['cnt']);
        $htmlout_kids_2019 .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
    }
    $htmlout_kids_2019 .= $htmlout_after;

    $sql_j_und_s_2019 = str_replace(
        '%%PLACEHOLDER_FOR_SOLV_UIDS%%',
        implode("', '", $jom_solv_uids_2019),
        $sql_j_und_s,
    );
    $result_j_und_s_2019 = $db->query($sql_j_und_s_2019);
    $starts_j_und_s_2019 = 0;
    $htmlout_j_und_s_2019 = str_replace('%%PLACEHOLDER_FOR_ID%%', 'ranking-junds-2019', $htmlout_before);
    while ($row = $result_j_und_s_2019->fetch_assoc()) {
        $starts_j_und_s_2019 += intval($row['cnt']);
        $htmlout_j_und_s_2019 .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
    }
    $htmlout_j_und_s_2019 .= $htmlout_after;

    $sql_kids_2020 = str_replace(
        '%%PLACEHOLDER_FOR_SOLV_UIDS%%',
        implode("', '", $jom_solv_uids_2020),
        $sql_kids,
    );
    $result_kids_2020 = $db->query($sql_kids_2020);
    $starts_kids_2020 = 0;
    $htmlout_kids_2020 = str_replace('%%PLACEHOLDER_FOR_ID%%', 'ranking-kids-2020', $htmlout_before);
    while ($row = $result_kids_2020->fetch_assoc()) {
        $starts_kids_2020 += intval($row['cnt']);
        $htmlout_kids_2020 .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
    }
    $htmlout_kids_2020 .= $htmlout_after;

    $sql_j_und_s_2020 = str_replace(
        '%%PLACEHOLDER_FOR_SOLV_UIDS%%',
        implode("', '", $jom_solv_uids_2020),
        $sql_j_und_s,
    );
    $result_j_und_s_2020 = $db->query($sql_j_und_s_2020);
    $starts_j_und_s_2020 = 0;
    $htmlout_j_und_s_2020 = str_replace('%%PLACEHOLDER_FOR_ID%%', 'ranking-junds-2020', $htmlout_before);
    while ($row = $result_j_und_s_2020->fetch_assoc()) {
        $starts_j_und_s_2020 += intval($row['cnt']);
        $htmlout_j_und_s_2020 .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
    }
    $htmlout_j_und_s_2020 .= $htmlout_after;

    $percent_j_und_s = $starts_j_und_s_2020 * 100 / ($starts_j_und_s_2019 + 0.00000001);
    $percent_kids = $starts_kids_2020 * 100 / ($starts_kids_2019 + 0.00000001);
    $are_kids_winners = ($percent_kids > $percent_j_und_s);

    $color_kids = $are_kids_winners ? 'rgb(0,100,0)' : 'rgb(180,0,0)';
    $color_j_und_s = $are_kids_winners ? 'rgb(180,0,0)' : 'rgb(0,100,0)';

    $out .= "<div class='header-box'><div style='width:80px;' class='box_ganz'><div style='display: flow-root; width:80px; border:0px;'>";
    $out .= "<h2 style='font-size:12px; border: 0; padding-left: 0; text-align:center; margin-top: 0;'>JOM-Jugend</h2>";
    $out .= "<div style='margin-top:-4px; font-size:18px; text-align:center; color:".$color_kids.";' title='Wie viele Jugndliche im Vergleich zu 2019'>".round($percent_kids, 2)."%</div>";
    $out .= "<div style='margin-top:-2px; font-size:12px; text-align:center;' title='Anzahl Starts von Jugndlichen 2020 / 2019'>";
    $out .= "<a href='javascript:' onclick='headerToggle(&quot;ranking-kids-2020&quot;); return false;' style='color:".$color_kids.";'>{$starts_kids_2020}</a> / ";
    $out .= "<a href='javascript:' onclick='headerToggle(&quot;ranking-kids-2019&quot;); return false;' style='color:".$color_kids.";'>{$starts_kids_2019}</a>";
    $out .= "</div>";
    $out .= "<h2 style='margin-top:2px; font-size:12px; border:0px; padding-left: 0; text-align:center;'>J&amp;S-Leiter</h2>";
    $out .= "<div style='margin-top:-4px; font-size:18px; text-align:center; color:".$color_j_und_s.";' title='Wie viele J&S-Leiter im Vergleich zu 2019'>".round($percent_j_und_s, 2)."%</div>";
    $out .= "<div style='margin-top:-2px; font-size:12px; text-align:center;' title='Anzahl Starts von OLZ-J&S-Leitern 2020 / 2019'>";
    $out .= "<a href='javascript:' onclick='headerToggle(&quot;ranking-junds-2020&quot;); return false;' style='color:".$color_j_und_s.";'>{$starts_j_und_s_2020}</a> / ";
    $out .= "<a href='javascript:' onclick='headerToggle(&quot;ranking-junds-2019&quot;); return false;' style='color:".$color_j_und_s.";'>{$starts_j_und_s_2019}</a>";
    $out .= "</div>";
    $out .= "</div></div></div>";
    $out .= $htmlout_kids_2019;
    $out .= $htmlout_kids_2020;
    $out .= $htmlout_j_und_s_2019;
    $out .= $htmlout_j_und_s_2020;

    /*
    // OLZ JOM Team Challenge 2015
    $out .= "<div style='position:absolute; top:0px; right:0px;'><div class='box_ganz'><div style='border-left:5px solid rgb(255,250,0);'><h2>Nachwuchs Challenge</h2><form name='Formularh' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>";
    $out .= get_olz_text(9, false);
    $out .= "</form></div></div></div>";
    */
    //$out .= $statistik_text;

    /*function htmlboxhalbe($entry) {
        global $zugriff,$colors;
        $edit_admin = ($zugriff)?"<a href='aktuell.php?id=".$entry["id"]."&amp;buttonaktuell=start' class='linkedit'>&nbsp;</a>":"";

        if (!$entry){
            return "<td rowspan='2' style='vertical-align:middle;'><div class='box_halb'></div></td>";
            }
        else{
        $titel = ($entry["titel"].$entry["textkurz"]!="") ? $edit_admin.$entry["titel"].$entry["textkurz"] : "" ;
        return "<td rowspan='2' style='vertical-align:middle;'><div class='box_halb' style='border-color:#".$colors[$entry["wichtig"]].";'><h3>".$titel."</h3><p>".olz_br($entry["textlang"])."</p></div></td>";}
    }*/

    // OLZ Trophy 2017
    $out .= "<div class='header-box'><a href='trophy.php'><img src='{$_CONFIG->getDataHref()}img/trophy.png' alt='trophy' style='position:relative; top:5px;' class='noborder' /></a></div>";

    $out .= "</div>"; // header-content
    $out .= "</div>"; // header-content-scroller
    $out .= "</div>"; // header-content-container
    $out .= "</div>"; // header-bar

    return $out;
}

function htmlbox($entry, $typ, $zugriff, $button_name): string {
    $colors = ["dd0000", "00cc00", "005500"]; // Farbe Randbalken

    $edit_admin = ($zugriff) ? "<a href='aktuell.php?id=".$entry["id"]."&amp;".$button_name."=start' class='linkedit'>&nbsp;</a>" : "";
    if (!$entry) {
        return "<div class='box_ganz'>&nbsp;</div>";
    }
    $titel = ($entry["titel"] != "") ? $edit_admin.$entry["titel"] : ""; // Wieso???
    return "<div class='header-box box_ganz'><div style='display: flow-root; border-color:#".$colors[$entry["wichtig"]].";'><h3 style='margin-top: 0;'>".$titel."</h3><div style='padding:0px 5px;' class='box_content'>".olz_br($entry["textlang"])."</div></div></div>";
}
