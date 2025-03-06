<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Terminen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineUpcomingTile;

use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineUpcomingTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.7;
    }

    public function getHtml(mixed $args): string {
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();
        $code_path = $this->envUtils()->getCodePath();
        $today = $this->dateUtils()->getIsoToday();
        $termin_label_repo = $this->entityManager()->getRepository(TerminLabel::class);

        $out = "<h3>Bevorstehende Termine</h3>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<ZZZZZZZZZZ
            SELECT
                t.id,
                t.start_date as date,
                t.title as title,
                (
                    SELECT l.id
                    FROM 
                        termin_label_map tl
                        JOIN termin_labels l ON (l.id = tl.label_id)
                    WHERE tl.termin_id = t.id
                    ORDER BY l.position ASC
                    LIMIT 1
                ) as label_id
            FROM termine t
            WHERE t.on_off = '1' AND t.start_date >= '{$today}'
            ORDER BY t.start_date ASC
            LIMIT 7
            ZZZZZZZZZZ);
        // @phpstan-ignore-next-line
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            // @phpstan-ignore-next-line
            $date = $this->dateUtils()->compactDate($row['date']);
            $title = $row['title'];
            $label_id = $row['label_id'];
            $label = $termin_label_repo->findOneBy(['id' => $label_id]);
            $label_ident = $label?->getIdent();
            $fallback_path = "{$code_path}assets/icns/termine_type_{$label_ident}_20.svg";
            $fallback_href = is_file($fallback_path)
                ? "{$code_href}assets/icns/termine_type_{$label_ident}_20.svg" : null;
            $icon_href = $label?->getIcon() ? $label->getFileHref($label->getIcon()) : $fallback_href;
            $icon_img = $icon_href ? "<img src='{$icon_href}' alt='' class='link-icon'>" : '';
            $out .= <<<ZZZZZZZZZZ
                    <li class='flex'>
                        {$icon_img}
                        <a href='{$code_href}termine/{$id}?von=startseite'>
                            <b>{$date}</b>: {$title}
                        </a>
                    </li>
                ZZZZZZZZZZ;
        }
        $out .= "</ul>";

        return $out;
    }
}
