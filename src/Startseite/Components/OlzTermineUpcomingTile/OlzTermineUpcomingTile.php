<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Terminen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineUpcomingTile;

use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineUpcomingTile extends AbstractOlzTile {
    private ?\mysqli $db = null;
    private ?int $this_year = null;

    public function getRelevance(?User $user): float {
        return 0.7;
    }

    public function getHtml(mixed $args): string {
        $this->termineUtils()->loadTypeOptions();
        $this->db = $this->dbUtils()->getDb();
        $this->this_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $code_href = $this->envUtils()->getCodeHref();
        $code_path = $this->envUtils()->getCodePath();
        $today = $this->dateUtils()->getIsoToday();
        $termin_label_repo = $this->entityManager()->getRepository(TerminLabel::class);

        $termine_url = $this->termineUtils()->getUrl(['typ' => 'alle', 'datum' => 'bevorstehend']);

        $out = <<<ZZZZZZZZZZ
            <h3>
                <a href='{$termine_url}&von=startseite' class='header-link'>
                    <img
                        src='{$code_href}assets/icns/termine_type_all_20.svg'
                        alt='Termine'
                        class='header-link-icon text-icon'
                    >
                    Termine
                </a>
            </h3>
            ZZZZZZZZZZ;

        $out .= "<div class='filters'>".implode(' ', [
            $this->renderProgramList(),
            $this->renderWeekendsList(),
            $this->renderTrophyList(),
            $this->renderUpcomingTrainingsList(),
        ])."</div>";

        $out .= "<ul class='links'>";
        $res = $this->db->query(<<<ZZZZZZZZZZ
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
            $icon_img = $icon_href ? "<img src='{$icon_href}' alt='' class='link-icon text-icon'>" : '';
            $out .= <<<ZZZZZZZZZZ
                    <li class='flex'>
                        <a href='{$code_href}termine/{$id}?von=startseite'>
                            {$icon_img}
                            <b>{$date}</b>: {$title}
                        </a>
                    </li>
                ZZZZZZZZZZ;
        }
        $out .= "</ul>";

        return $out;
    }

    protected function renderProgramList(): string {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_programm_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $this_year = $this->this_year;
        $next_year = $this->this_year + 1;
        $imminent_filter = [
            ...$this->termineUtils()->getDefaultFilter(),
            'typ' => 'programm',
            'datum' => 'bevorstehend',
        ];
        $this_year_filter = [
            ...$this->termineUtils()->getDefaultFilter(),
            'typ' => 'programm',
            'datum' => strval($this_year),
        ];
        $num_imminent = $this->getNumberOfEntries($imminent_filter);
        $out = '';
        if ($num_imminent > 0) {
            $num_this_year = $this->getNumberOfEntries($this_year_filter);
            $serialized_filter = $this->termineUtils()->serialize($this_year_filter);
            $out .= <<<ZZZZZZZZZZ
                <div class='filter'><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                    {$icon_img} Programm {$this_year}<span class='secondary'>({$num_this_year})</span>
                </a></div>
                ZZZZZZZZZZ;
        }
        $current_month = intval($this->dateUtils()->getCurrentDateInFormat('m'));
        if ($current_month > 8) {
            $next_year_filter = [
                ...$this->termineUtils()->getDefaultFilter(),
                'typ' => 'programm',
                'datum' => strval($next_year),
            ];
            $num_next_year = $this->getNumberOfEntries($next_year_filter);

            if ($num_next_year > 0) {
                $serialized_filter = $this->termineUtils()->serialize($next_year_filter);
                $out .= <<<ZZZZZZZZZZ
                    <div class='filter'><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                        {$icon_img} Programm {$next_year}<span class='secondary'>({$num_next_year})</span>
                    </a></div>
                    ZZZZZZZZZZ;
            }
        }
        return $out;
    }

    protected function renderWeekendsList(): string {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_weekend_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $imminent_filter = [
            ...$this->termineUtils()->getDefaultFilter(),
            'typ' => 'weekend',
            'datum' => 'bevorstehend',
        ];
        $num_imminent = $this->getNumberOfEntries($imminent_filter);
        $serialized_filter = $this->termineUtils()->serialize($imminent_filter);
        return <<<ZZZZZZZZZZ
            <div class='filter'><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                {$icon_img} Weekends<span class='secondary'>({$num_imminent})</span>
            </a></div>
            ZZZZZZZZZZ;
    }

    protected function renderTrophyList(): string {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_trophy_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $this_year = $this->this_year;
        $this_year_filter = [
            ...$this->termineUtils()->getDefaultFilter(),
            'typ' => 'trophy',
            'datum' => strval($this_year),
        ];
        $num_this_year = $this->getNumberOfEntries($this_year_filter);
        $serialized_filter = $this->termineUtils()->serialize($this_year_filter);
        return <<<ZZZZZZZZZZ
            <div class='filter'><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                {$icon_img} OLZ Trophy<span class='secondary'>({$num_this_year})</span>
            </a></div>
            ZZZZZZZZZZ;
    }

    protected function renderUpcomingTrainingsList(): string {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_training_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $imminent_filter = [
            ...$this->termineUtils()->getDefaultFilter(),
            'typ' => 'training',
            'datum' => 'bevorstehend',
        ];
        $serialized_filter = $this->termineUtils()->serialize($imminent_filter);
        return <<<ZZZZZZZZZZ
            <div class='filter'><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                {$icon_img} Trainings
            </a></div>
            ZZZZZZZZZZ;
    }

    /** @param array{typ?: string, datum?: string, archiv?: string} $filter */
    protected function getNumberOfEntries(array $filter): int {
        $date_filter = $this->termineUtils()->getSqlDateRangeFilter($filter, 'c');
        $type_filter = $this->termineUtils()->getSqlTypeFilter($filter, 'c');
        $filter_sql = "({$date_filter}) AND ({$type_filter})";
        $sql = <<<ZZZZZZZZZZ
            SELECT * 
            FROM (
                SELECT
                    t.id AS id,
                    t.start_date AS start_date,
                    t.end_date AS end_date,
                    (
                        SELECT GROUP_CONCAT(l.ident ORDER BY l.position ASC SEPARATOR ' ')
                        FROM
                            termin_label_map tl
                            JOIN termin_labels l ON (l.id = tl.label_id)
                        WHERE tl.termin_id = t.id
                        GROUP BY t.id
                    ) as typ
                FROM termine t
            ) AS c
            WHERE {$filter_sql}
            ZZZZZZZZZZ;
        $res = $this->db?->query($sql);
        // @phpstan-ignore-next-line
        return $res->num_rows;
    }
}
