<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Terminen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineUpcomingTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineUpcomingTile extends AbstractOlzTile {
    protected static $iconBasenameByType = [
        'programm' => 'termine_type_programm_20.svg',
        'weekend' => 'termine_type_weekend_20.svg',
        'ol' => 'termine_type_ol_20.svg',
        'training' => 'termine_type_training_20.svg',
        'club' => 'termine_type_club_20.svg',
    ];

    public function getRelevance(?User $user): float {
        return 0.7;
    }

    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $today = $this->dateUtils()->getIsoToday();

        $out = "<h2>Bevorstehende Termine</h2>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<ZZZZZZZZZZ
            SELECT t.id, t.datum as date, t.titel as title, t.typ as type
            FROM termine t
            WHERE t.on_off = '1' AND t.datum >= '{$today}'
            ORDER BY t.datum ASC
            LIMIT 7
        ZZZZZZZZZZ);
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $date = date('d.m.', strtotime($row['date']));
            $title = $row['title'];
            $types = explode(' ', $row['type']);
            $icon_basename = array_reduce($types, function ($carry, $item) {
                if ($carry) {
                    return $carry;
                }
                return self::$iconBasenameByType[$item] ?? '';
            }, '');
            $icon_basename = $icon_basename ? $icon_basename : 'termine_type_null_20.svg';
            $icon = "{$data_href}assets/icns/{$icon_basename}";
            $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
            $out .= "<li><a href='{$code_href}termine.php?id={$id}'>
                {$icon_img} <b>{$date}</b>: {$title}
            </a></li>";
        }
        $out .= "</ul>";

        return $out;
    }
}
