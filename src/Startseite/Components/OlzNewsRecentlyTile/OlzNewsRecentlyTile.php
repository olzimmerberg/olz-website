<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich veröffentlichten News an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsRecentlyTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzNewsRecentlyTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.7;
    }

    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();

        $out = "<h2>Kürzlich veröffentlicht</h2>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<'ZZZZZZZZZZ'
            SELECT a.id, a.datum as date, a.titel as title
            FROM aktuell a
            WHERE a.on_off = '1'
            ORDER BY datum DESC, zeit DESC
            LIMIT 5
        ZZZZZZZZZZ);
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $date = date('d.m.', strtotime($row['date']));
            $title = $row['title'];
            $out .= "<li><a href='{$code_href}aktuell.php?id={$id}' class='linkint'><b>{$date}</b>: {$title}</a></li>";
        }
        $out .= "</ul>";

        return $out;
    }
}
