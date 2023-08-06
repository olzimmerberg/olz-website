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
        $this->termine_utils = TermineFilterUtils::fromEnv();
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
        $filter = $this->termine_utils->getDefaultFilter();
        $filter['typ'] = 'alle';
        $filter['datum'] = 'bevorstehend';
        $enc_json_filter = urlencode(json_encode($filter));
        return <<<ZZZZZZZZZZ
        <li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>
            <b>Nächste Termine</b>
        </a></li>
        ZZZZZZZZZZ;
    }

    protected function renderProgramList() {
        $code_href = $this->envUtils()->getCodeHref();
        $filter = $this->termine_utils->getDefaultFilter();
        $filter['typ'] = 'programm';
        $filter['datum'] = 'bevorstehend';
        $num_imminent = $this->getNumberOfEntries($filter);
        $filter['datum'] = strval($this->this_year + 1);
        $num_next_year = $this->getNumberOfEntries($filter);
        if ($num_imminent > 0 || $num_next_year === 0) {
            $filter['datum'] = strval($this->this_year);
        }
        $enc_json_filter = urlencode(json_encode($filter));
        $year = $filter['datum'];
        return <<<ZZZZZZZZZZ
        <li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>
            <b>Jahresprogramm {$year}</b>
        </a></li>
        ZZZZZZZZZZ;
    }

    protected function renderWeekendsList() {
        $code_href = $this->envUtils()->getCodeHref();
        $filter = $this->termine_utils->getDefaultFilter();
        $filter['typ'] = 'weekend';
        $filter['datum'] = 'bevorstehend';
        $num_imminent = $this->getNumberOfEntries($filter);
        $filter['datum'] = strval($this->this_year + 1);
        $num_next_year = $this->getNumberOfEntries($filter);
        if ($num_imminent > 0 || $num_next_year === 0) {
            $filter['datum'] = strval($this->this_year);
        }
        $enc_json_filter = urlencode(json_encode($filter));
        $year = $filter['datum'];
        return <<<ZZZZZZZZZZ
        <li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>
            <b>Weekends {$year}</b>
        </a></li>
        ZZZZZZZZZZ;
    }

    protected function renderUpcomingTrainingsList() {
        $code_href = $this->envUtils()->getCodeHref();
        $filter = $this->termine_utils->getDefaultFilter();
        $filter['typ'] = 'training';
        $filter['datum'] = 'bevorstehend';
        $enc_json_filter = urlencode(json_encode($filter));
        return <<<ZZZZZZZZZZ
        <li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>
            <b>Nächste Trainings</b>
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
