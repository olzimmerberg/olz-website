<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Terminen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineUpcomingTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineUpcomingTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.7;
    }

    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();
        $today = $this->dateUtils()->getIsoToday();

        $out = "<h2>Bevorstehende Termine</h2>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<ZZZZZZZZZZ
            SELECT t.id, t.datum as date, t.titel as title
            FROM termine t
            WHERE t.on_off = '1' AND t.datum >= '{$today}'
            ORDER BY t.datum ASC
            LIMIT 5
        ZZZZZZZZZZ);
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $date = date('d.m.', strtotime($row['date']));
            $title = $row['title'];
            $out .= "<li><a href='{$code_href}termine.php?id={$id}' class='linkint'><b>{$date}</b>: {$title}</a></li>";
        }
        $out .= "</ul>";

        return $out;
    }
}
