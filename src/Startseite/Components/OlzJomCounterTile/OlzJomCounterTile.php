<?php

namespace Olz\Startseite\Components\OlzJomCounterTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzJomCounterTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.5;
    }

    public function getHtml(array $args = []): string {
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

        $db = $this->dbUtils()->getDb();

        $out = "<h2>JOM-Counter</h2>";

        $jom_solv_uids = [
            2019 => [9610, 9543, 9781, 9636, 9542, 9541, 9380, 9390, 9950, 9815, 9821],
            2020 => [10086, 10228, 9901, 10049, 10197, 10201, 10239, 10253, 9915, 10247, 10317],
            2022 => [10805, 11383, 10988, 11281, 11279, 11280, 11405, 11380, 11426, 11012, 11557],
            2023 => [11763, 11805, 11455, 11456, 11799, 11797, 11803, 11462, 11804, 11824, 11801],
        ];

        $previous_year = 2022;
        $current_year = 2023;

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

        $htmlout_before = "<div style='position:absolute; top:-200px; width:300px; height:200px; z-index:1000; display:none; border:1px solid #000; background:#fefefe; overflow-y:scroll;' id='%%PLACEHOLDER_FOR_ID%%'><div style='padding:5px;'><table>";
        $htmlout_before .= "<tr><th>Name</th><th style='text-align:right;'>Starts</th></tr>";
        $htmlout_after = "</table></div></div>";

        $sql_kids_previous = str_replace(
            '%%PLACEHOLDER_FOR_SOLV_UIDS%%',
            implode("', '", $jom_solv_uids[$previous_year]),
            $sql_kids,
        );
        $result_kids_previous = $db->query($sql_kids_previous);
        $starts_kids_previous = 0;
        $htmlout_kids_previous = str_replace('%%PLACEHOLDER_FOR_ID%%', 'ranking-kids-previous', $htmlout_before);
        while ($row = $result_kids_previous->fetch_assoc()) {
            $starts_kids_previous += intval($row['cnt']);
            $htmlout_kids_previous .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
        }
        $htmlout_kids_previous .= $htmlout_after;

        $sql_j_und_s_previous = str_replace(
            '%%PLACEHOLDER_FOR_SOLV_UIDS%%',
            implode("', '", $jom_solv_uids[$previous_year]),
            $sql_j_und_s,
        );
        $result_j_und_s_previous = $db->query($sql_j_und_s_previous);
        $starts_j_und_s_previous = 0;
        $htmlout_j_und_s_previous = str_replace('%%PLACEHOLDER_FOR_ID%%', 'ranking-junds-previous', $htmlout_before);
        while ($row = $result_j_und_s_previous->fetch_assoc()) {
            $starts_j_und_s_previous += intval($row['cnt']);
            $htmlout_j_und_s_previous .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
        }
        $htmlout_j_und_s_previous .= $htmlout_after;

        $sql_kids_current = str_replace(
            '%%PLACEHOLDER_FOR_SOLV_UIDS%%',
            implode("', '", $jom_solv_uids[$current_year]),
            $sql_kids,
        );
        $result_kids_current = $db->query($sql_kids_current);
        $starts_kids_current = 0;
        $htmlout_kids_current = str_replace('%%PLACEHOLDER_FOR_ID%%', 'ranking-kids-current', $htmlout_before);
        while ($row = $result_kids_current->fetch_assoc()) {
            $starts_kids_current += intval($row['cnt']);
            $htmlout_kids_current .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
        }
        $htmlout_kids_current .= $htmlout_after;

        $sql_j_und_s_current = str_replace(
            '%%PLACEHOLDER_FOR_SOLV_UIDS%%',
            implode("', '", $jom_solv_uids[$current_year]),
            $sql_j_und_s,
        );
        $result_j_und_s_current = $db->query($sql_j_und_s_current);
        $starts_j_und_s_current = 0;
        $htmlout_j_und_s_current = str_replace('%%PLACEHOLDER_FOR_ID%%', 'ranking-junds-current', $htmlout_before);
        while ($row = $result_j_und_s_current->fetch_assoc()) {
            $starts_j_und_s_current += intval($row['cnt']);
            $htmlout_j_und_s_current .= "<tr><td style='white-space:nowrap; overflow-x:hidden;'>".$row['name']."</td><td style='text-align:right; cursor:pointer;' title='".str_replace("'", "&#39;", $row['events'])."' onclick='alert(this.getAttribute(&quot;title&quot;))'>".$row['cnt']."</td></tr>";
        }
        $htmlout_j_und_s_current .= $htmlout_after;

        $percent_j_und_s = $starts_j_und_s_current * 100 / ($starts_j_und_s_previous + 0.00000001);
        $percent_kids = $starts_kids_current * 100 / ($starts_kids_previous + 0.00000001);
        $are_kids_winners = ($percent_kids > $percent_j_und_s);

        $color_kids = $are_kids_winners ? 'rgb(0,100,0)' : 'rgb(180,0,0)';
        $color_j_und_s = $are_kids_winners ? 'rgb(180,0,0)' : 'rgb(0,100,0)';

        $out .= "<div style='position:relative;'>";
        $out .= "<h2 style='font-size:12px; border: 0; padding-left: 0; text-align:center; margin-top: 0;'>JOM-Jugend</h2>";
        $out .= "<div style='margin-top:-4px; font-size:18px; text-align:center; color:".$color_kids.";' title='Wie viele Jugndliche im Vergleich zu {$previous_year}'>".round($percent_kids, 2)."%</div>";
        $out .= "<div style='margin-top:-2px; font-size:12px; text-align:center;' title='Anzahl Starts von Jugndlichen {$current_year} / {$previous_year}'>";
        $out .= "<a href='javascript:' onclick='olz.olzJomCounterToggle(&quot;ranking-kids-current&quot;); return false;' style='color:".$color_kids.";'>{$starts_kids_current}</a> / ";
        $out .= "<a href='javascript:' onclick='olz.olzJomCounterToggle(&quot;ranking-kids-previous&quot;); return false;' style='color:".$color_kids.";'>{$starts_kids_previous}</a>";
        $out .= "</div>";
        $out .= "<h2 style='margin-top:2px; font-size:12px; border:0px; padding-left: 0; text-align:center;'>J&amp;S-Leiter</h2>";
        $out .= "<div style='margin-top:-4px; font-size:18px; text-align:center; color:".$color_j_und_s.";' title='Wie viele J&S-Leiter im Vergleich zu {$previous_year}'>".round($percent_j_und_s, 2)."%</div>";
        $out .= "<div style='margin-top:-2px; font-size:12px; text-align:center;' title='Anzahl Starts von OLZ-J&S-Leitern {$current_year} / {$previous_year}'>";
        $out .= "<a href='javascript:' onclick='olz.olzJomCounterToggle(&quot;ranking-junds-current&quot;); return false;' style='color:".$color_j_und_s.";'>{$starts_j_und_s_current}</a> / ";
        $out .= "<a href='javascript:' onclick='olz.olzJomCounterToggle(&quot;ranking-junds-previous&quot;); return false;' style='color:".$color_j_und_s.";'>{$starts_j_und_s_previous}</a>";
        $out .= "</div>";
        $out .= $htmlout_kids_previous;
        $out .= $htmlout_kids_current;
        $out .= $htmlout_j_und_s_previous;
        $out .= $htmlout_j_und_s_current;
        $out .= "</div>";
        return $out;
    }
}
