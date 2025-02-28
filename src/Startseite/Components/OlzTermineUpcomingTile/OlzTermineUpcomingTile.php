<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Terminen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineUpcomingTile;

use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineUpcomingTile extends AbstractOlzTile {
    /** @var array<string, string> */
    protected static $iconBasenameByType = [
        'programm' => 'termine_type_programm_20.svg',
        'weekend' => 'termine_type_weekend_20.svg',
        'ol' => 'termine_type_ol_20.svg',
        'training' => 'termine_type_training_20.svg',
        'club' => 'termine_type_club_20.svg',
        'meldeschluss' => 'termine_type_meldeschluss_20.svg',
    ];

    public function getRelevance(?User $user): float {
        return 0.7;
    }

    public function getHtml(mixed $args): string {
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();
        $today = $this->dateUtils()->getIsoToday();

        $out = "<h3>Bevorstehende Termine</h3>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<ZZZZZZZZZZ
            SELECT
                t.id,
                t.start_date as date,
                t.title as title,
                (
                    SELECT GROUP_CONCAT(l.ident ORDER BY l.position ASC SEPARATOR ' ')
                    FROM 
                        termin_label_map tl
                        JOIN termin_labels l ON (l.id = tl.label_id)
                    WHERE tl.termin_id = t.id
                    GROUP BY t.id
                ) as type
            FROM termine t
            WHERE t.on_off = '1' AND t.start_date >= '{$today}'
            ORDER BY t.start_date ASC
            LIMIT 7
            ZZZZZZZZZZ);
        // @phpstan-ignore-next-line
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            // @phpstan-ignore-next-line
            $date = date('d.m.', strtotime($row['date']) ?: 0);
            $title = $row['title'];
            // @phpstan-ignore-next-line
            $types = explode(' ', $row['type']);
            $icon_basename = array_reduce($types, function ($carry, $item) {
                if ($carry) {
                    return $carry;
                }
                return self::$iconBasenameByType[$item] ?? '';
            }, '');
            $icon_basename = $icon_basename ? $icon_basename : 'termine_type_null_20.svg';
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
            $out .= "<li><a href='{$code_href}termine/{$id}'>
                {$icon_img} <b>{$date}</b>: {$title}
            </a></li>";
        }
        $out .= "</ul>";

        return $out;
    }
}
