<?php

namespace Olz\Components\Page\OlzHeaderJomCounter;

class OlzHeaderJomCounter {
    public static function render($args = []) {
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

        global $db;
        require_once __DIR__.'/../../../../_/config/database.php';
        $out = '';

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

        $htmlout_before = "<div style='position:absolute; top:0px; right:252px; z-index:1000; display:none;' id='%%PLACEHOLDER_FOR_ID%%'><div class='box-ganz'><div style='margin-top:8px; border:0px; overflow-y:scroll;'><div style='padding:5px;'><table>";
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

        $out .= "<div class='header-box'><div style='width:80px;' class='box-ganz'><div style='display: flow-root; width:80px; border:0px;'>";
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
        return $out;
    }
}
