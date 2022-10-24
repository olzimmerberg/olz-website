<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich geänderten Terminen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineUpdatesTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

class OlzTermineUpdatesTile extends AbstractOlzTile {
    public static function getRelevance(?User $user): float {
        return 0.5;
    }

    public static function render(): string {
        $db = DbUtils::fromEnv()->getDb();
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();

        $out = "<h2>Aktualisierte Termine</h2>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<'ZZZZZZZZZZ'
            SELECT t.id, t.datum as date, t.titel as title, t.modified
            FROM termine t
            WHERE t.on_off = '1'
            ORDER BY modified DESC
            LIMIT 5
        ZZZZZZZZZZ);
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $modified = date('d.m.', strtotime($row['modified']));
            $date = date('d.m.', strtotime($row['date']));
            $title = $row['title'];
            $out .= "<li><a href='{$code_href}termine.php?id={$id}' class='linkint'><b>{$modified}</b>: {$title} vom {$date}</a></li>";
        }
        $out .= "</ul>";

        return $out;
    }
}
