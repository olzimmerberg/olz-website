<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit relevanten Termine-Links an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineListsTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Termine\Utils\TermineFilterUtils;

class OlzTermineListsTile extends AbstractOlzTile {
    private $termine_utils;
    private $db;
    private $this_year;

    public function getRelevance(?User $user): float {
        return 0.8;
    }

    public function getHtml($args = []): string {
        $this->termine_utils = TermineFilterUtils::fromEnv()->loadTypeOptions();
        $this->db = $this->dbUtils()->getDb();
        $this->this_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));

        $out = "<h2>Termine</h2>";

        $out .= "<ul class='links'>";
        $out .= $this->renderAllUpcomingList();
        $out .= $this->renderProgramList();
        $out .= $this->renderWeekendsList();
        $out .= $this->renderUpcomingTrainingsList();
        $out .= "</ul>";

        return $out;
    }

    protected function renderAllUpcomingList() {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_all_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $filter = $this->termine_utils->getDefaultFilter();
        $filter['typ'] = 'alle';
        $filter['datum'] = 'bevorstehend';
        $enc_json_filter = urlencode(json_encode($filter));
        return <<<ZZZZZZZZZZ
        <li><a href='{$code_href}termine?filter={$enc_json_filter}'>
            {$icon_img} <b>Nächste Termine</b>
        </a></li>
        ZZZZZZZZZZ;
    }

    protected function renderProgramList() {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_programm_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $this_year = $this->this_year;
        $next_year = $this->this_year + 1;
        $imminent_filter = [
            ...$this->termine_utils->getDefaultFilter(),
            'typ' => 'programm',
            'datum' => 'bevorstehend',
        ];
        $this_year_filter = [
            ...$this->termine_utils->getDefaultFilter(),
            'typ' => 'programm',
            'datum' => strval($this_year),
        ];
        $next_year_filter = [
            ...$this->termine_utils->getDefaultFilter(),
            'typ' => 'programm',
            'datum' => strval($next_year),
        ];
        $num_imminent = $this->getNumberOfEntries($imminent_filter);
        $num_next_year = $this->getNumberOfEntries($next_year_filter);
        $out = '';
        if ($num_imminent > 0) {
            $num_this_year = $this->getNumberOfEntries($this_year_filter);
            $enc_json_filter = urlencode(json_encode($this_year_filter));
            $out .= <<<ZZZZZZZZZZ
            <li><a href='{$code_href}termine?filter={$enc_json_filter}'>
                {$icon_img} <b>Jahresprogramm {$this_year}</b><span class='secondary'>({$num_this_year})</span>
            </a></li>
            ZZZZZZZZZZ;
        }
        if ($num_next_year > 0) {
            $enc_json_filter = urlencode(json_encode($next_year_filter));
            $out .= <<<ZZZZZZZZZZ
            <li><a href='{$code_href}termine?filter={$enc_json_filter}'>
                {$icon_img} <b>Jahresprogramm {$next_year}</b><span class='secondary'>({$num_next_year})</span>
            </a></li>
            ZZZZZZZZZZ;
        }
        return $out;
    }

    protected function renderWeekendsList() {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_weekend_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $imminent_filter = [
            ...$this->termine_utils->getDefaultFilter(),
            'typ' => 'weekend',
            'datum' => 'bevorstehend',
        ];
        $num_imminent = $this->getNumberOfEntries($imminent_filter);
        $enc_json_filter = urlencode(json_encode($imminent_filter));
        return <<<ZZZZZZZZZZ
        <li><a href='{$code_href}termine?filter={$enc_json_filter}'>
            {$icon_img} <b>Bevorstehende Weekends</b><span class='secondary'>({$num_imminent})</span>
        </a></li>
        ZZZZZZZZZZ;
    }

    protected function renderUpcomingTrainingsList() {
        $code_href = $this->envUtils()->getCodeHref();
        $icon = "{$code_href}assets/icns/termine_type_training_20.svg";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        $imminent_filter = [
            ...$this->termine_utils->getDefaultFilter(),
            'typ' => 'training',
            'datum' => 'bevorstehend',
        ];
        $enc_json_filter = urlencode(json_encode($imminent_filter));
        return <<<ZZZZZZZZZZ
        <li><a href='{$code_href}termine?filter={$enc_json_filter}'>
            {$icon_img} <b>Nächste Trainings</b>
        </a></li>
        ZZZZZZZZZZ;
    }

    protected function getNumberOfEntries($filter) {
        $date_filter = $this->termine_utils->getSqlDateRangeFilter($filter);
        $type_filter = $this->termine_utils->getSqlTypeFilter($filter);
        $filter_sql = "({$date_filter}) AND ({$type_filter})";
        $res = $this->db->query("SELECT t.id FROM termine t WHERE {$filter_sql}");
        return $res->num_rows;
    }
}
