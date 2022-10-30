<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Meldeschlüssen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineDeadlinesTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Utils\AbstractDateUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

class OlzTermineDeadlinesTile extends AbstractOlzTile {
    public static function getRelevance(?User $user): float {
        return 0.7;
    }

    public static function render(): string {
        $db = DbUtils::fromEnv()->getDb();
        $date_utils = AbstractDateUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();
        $today = $date_utils->getIsoToday();
        $now = $date_utils->getIsoNow();

        $out = "<h2>Meldeschlüsse</h2>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<ZZZZZZZZZZ
        (
            SELECT
                se.deadline as deadline,
                t.datum as date,
                t.titel as title,
                t.id as id
            FROM termine t JOIN solv_events se ON (t.solv_uid = se.solv_uid)
            WHERE se.deadline IS NOT NULL AND se.deadline >= '{$today}'
        ) UNION ALL (
            SELECT
                DATE(t.deadline) as deadline,
                t.datum as date,
                t.titel as title,
                t.id as id
            FROM termine t
            WHERE t.deadline IS NOT NULL AND t.deadline >= '{$now}'
        )
        ORDER BY deadline ASC
        LIMIT 5
        ZZZZZZZZZZ);
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $deadline = date('d.m.', strtotime($row['deadline']));
            $date = date('d.m.', strtotime($row['date']));
            $title = $row['title'];
            $out .= "<li><a href='{$code_href}termine.php?id={$id}' class='linkint'><b>{$deadline}</b>: Meldeschluss für {$title} vom {$date}</a></li>";
        }
        $out .= "</ul>";
        return $out;
    }
}