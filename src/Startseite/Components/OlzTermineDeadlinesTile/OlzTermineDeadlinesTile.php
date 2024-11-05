<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Meldeschlüssen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineDeadlinesTile;

use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineDeadlinesTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.75;
    }

    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $db = $this->dbUtils()->getDb();
        $date_utils = $this->dateUtils();
        $code_href = $this->envUtils()->getCodeHref();
        $today = $date_utils->getIsoToday();
        $now = $date_utils->getIsoNow();
        $plus_three_days = \DateInterval::createFromDateString("+3 days");
        $plus_ten_days = \DateInterval::createFromDateString("+10 days");
        $plus_four_weeks = \DateInterval::createFromDateString("+4 weeks");
        $in_three_days = (new \DateTime($today))->add($plus_three_days)->format('Y-m-d');
        $in_ten_days = (new \DateTime($today))->add($plus_ten_days)->format('Y-m-d');
        $in_four_weeks = (new \DateTime($today))->add($plus_four_weeks)->format('Y-m-d');

        $out = "<h2>Meldeschlüsse</h2>";

        $res = $db->query(<<<ZZZZZZZZZZ
            (
                SELECT
                    se.deadline as deadline,
                    t.start_date as date,
                    t.title as title,
                    t.id as id
                FROM termine t JOIN solv_events se ON (t.solv_uid = se.solv_uid)
                WHERE 
                    se.deadline IS NOT NULL
                    AND se.deadline >= '{$today}'
                    AND se.deadline <= '{$in_four_weeks}'
            ) UNION ALL (
                SELECT
                    DATE(t.deadline) as deadline,
                    t.start_date as date,
                    t.title as title,
                    t.id as id
                FROM termine t
                WHERE
                    t.deadline IS NOT NULL
                    AND t.deadline >= '{$now}'
                    AND t.deadline <= '{$in_four_weeks}'
            )
            ORDER BY deadline ASC
            LIMIT 7
            ZZZZZZZZZZ);
        if ($res->num_rows === 0) {
            $out .= "<br /><center><i>Keine Meldeschlüsse in den nächsten vier Wochen</i></center>";
            return $out;
        }
        $out .= "<ul class='links'>";
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $deadline = date('d.m.', strtotime($row['deadline']));
            $date = date('d.m.', strtotime($row['date']));
            $title = $row['title'];
            $icon_color = 'green';
            if ($row['deadline'] <= $in_three_days) {
                $icon_color = 'red';
            } elseif ($row['deadline'] <= $in_ten_days) {
                $icon_color = 'orange';
            }
            $icon_basename = "termine_type_meldeschluss_{$icon_color}_20.svg";
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
            $out .= "<li><a href='{$code_href}termine/{$id}'>
              {$icon_img} <b>{$deadline}</b>: Für {$title} vom {$date}
            </a></li>";
        }
        $out .= "</ul>";
        return $out;
    }
}
