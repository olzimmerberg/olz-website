<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich geänderten Terminen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineUpdatesTile;

use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineUpdatesTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.5;
    }

    public function getHtml(mixed $args): string {
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();

        $out = "<h3>Aktualisierte Termine</h3>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<'ZZZZZZZZZZ'
                SELECT t.id, t.start_date as date, t.title as title, t.last_modified_at
                FROM termine t
                WHERE t.on_off = '1' AND t.newsletter = '1'
                ORDER BY t.last_modified_at DESC
                LIMIT 5
            ZZZZZZZZZZ);
        // @phpstan-ignore-next-line
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            // @phpstan-ignore-next-line
            $modified = date('d.m.', strtotime($row['last_modified_at']) ?: 0);
            // @phpstan-ignore-next-line
            $date = date('d.m.', strtotime($row['date']) ?: 0);
            $title = $row['title'];
            $out .= "<li><a href='{$code_href}termine/{$id}' class='linkint'><b>{$modified}</b>: Änderung an {$title} vom {$date}</a></li>";
        }
        $out .= "</ul>";

        return $out;
    }
}
