<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit relevanten Termine-Links an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineListsTile;

use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineListsTile extends AbstractOlzTile {
    private ?\mysqli $db = null;
    private ?int $this_year = null;

    public function getRelevance(?User $user): float {
        return 0.8;
    }

    public function getHtml(mixed $args): string {
        $this->termineUtils()->loadTypeOptions();
        $this->db = $this->dbUtils()->getDb();
        $this->this_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));

        $out = "<h3>Termine</h3>";

        $out .= "<ul class='links'>";
        $out .= $this->renderAllUpcomingList();
        $out .= $this->renderProgramList();
        $out .= $this->renderWeekendsList();
        $out .= $this->renderTrophyList();
        $out .= $this->renderUpcomingTrainingsList();
        $out .= "</ul>";

        return $out;
    }

    protected function renderAllUpcomingList(): string {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_all_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $filter = $this->termineUtils()->getDefaultFilter();
        $filter['typ'] = 'alle';
        $filter['datum'] = 'bevorstehend';
        $serialized_filter = $this->termineUtils()->serialize($filter);
        return <<<ZZZZZZZZZZ
            <li><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                {$icon_img} <b>Nächste Termine</b>
            </a></li>
            ZZZZZZZZZZ;
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
                <li><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                    {$icon_img} <b>Jahresprogramm {$this_year}</b><span class='secondary'>({$num_this_year})</span>
                </a></li>
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
                    <li><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                        {$icon_img} <b>Jahresprogramm {$next_year}</b><span class='secondary'>({$num_next_year})</span>
                    </a></li>
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
            <li><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                {$icon_img} <b>Bevorstehende Weekends</b><span class='secondary'>({$num_imminent})</span>
            </a></li>
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
            <li><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                {$icon_img} <b>OLZ Trophy {$this_year}</b><span class='secondary'>({$num_this_year})</span>
            </a></li>
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
            <li><a href='{$code_href}termine?filter={$serialized_filter}&von=startseite'>
                {$icon_img} <b>Nächste Trainings</b>
            </a></li>
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
