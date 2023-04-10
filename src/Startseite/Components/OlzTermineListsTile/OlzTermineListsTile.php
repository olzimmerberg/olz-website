<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit relevanten Termine-Links an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineListsTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Termine\Utils\TermineFilterUtils;

class OlzTermineListsTile extends AbstractOlzTile {
    private static $termine_utils;
    private static $db;
    private static $this_year;

    public function getRelevance(?User $user): float {
        return 0.8;
    }

    public function getHtml($args = []): string {
        self::$termine_utils = TermineFilterUtils::fromEnv();
        self::$db = $this->dbUtils()->getDb();
        self::$this_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));

        $out = "<h2>Termine</h2>";

        $out .= "<ul class='links'>";
        $out .= self::renderAllUpcomingList();
        $out .= self::renderProgramList();
        $out .= self::renderWeekendsList();
        $out .= self::renderUpcomingTrainingsList();
        $out .= "</ul>";

        return $out;
    }

    protected function renderAllUpcomingList() {
        $code_href = $this->envUtils()->getCodeHref();
        $filter = self::$termine_utils->getDefaultFilter();
        $filter['typ'] = 'alle';
        $filter['datum'] = 'bevorstehend';
        $enc_json_filter = urlencode(json_encode($filter));
        return "<li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>Nächste Termine</a></li>";
    }

    protected function renderProgramList() {
        $code_href = $this->envUtils()->getCodeHref();
        $filter = self::$termine_utils->getDefaultFilter();
        $filter['typ'] = 'programm';
        $filter['datum'] = 'bevorstehend';
        $num_imminent = self::getNumberOfEntries($filter);
        $filter['datum'] = strval(self::$this_year + 1);
        $num_next_year = self::getNumberOfEntries($filter);
        if ($num_imminent > 0 || $num_next_year === 0) {
            $filter['datum'] = strval(self::$this_year);
        }
        $enc_json_filter = urlencode(json_encode($filter));
        $year = $filter['datum'];
        return "<li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>Jahresprogramm {$year}</a></li>";
    }

    protected function renderWeekendsList() {
        $code_href = $this->envUtils()->getCodeHref();
        $filter = self::$termine_utils->getDefaultFilter();
        $filter['typ'] = 'weekend';
        $filter['datum'] = 'bevorstehend';
        $num_imminent = self::getNumberOfEntries($filter);
        $filter['datum'] = strval(self::$this_year + 1);
        $num_next_year = self::getNumberOfEntries($filter);
        if ($num_imminent > 0 || $num_next_year === 0) {
            $filter['datum'] = strval(self::$this_year);
        }
        $enc_json_filter = urlencode(json_encode($filter));
        $year = $filter['datum'];
        return "<li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>Weekends {$year}</a></li>";
    }

    protected function renderUpcomingTrainingsList() {
        $code_href = $this->envUtils()->getCodeHref();
        $filter = self::$termine_utils->getDefaultFilter();
        $filter['typ'] = 'training';
        $filter['datum'] = 'bevorstehend';
        $enc_json_filter = urlencode(json_encode($filter));
        return "<li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>Nächste Trainings</a></li>";
    }

    protected function getNumberOfEntries($filter) {
        $filter_sql = self::$termine_utils->getSqlFromFilter($filter);
        $res = self::$db->query("SELECT t.id FROM termine t WHERE {$filter_sql}");
        return $res->num_rows;
    }
}
