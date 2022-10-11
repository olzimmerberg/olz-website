<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit relevanten Termine-Links an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineListsTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\AbstractDateUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

class OlzTermineListsTile extends AbstractOlzTile {
    private static $env_utils;
    private static $termine_utils;
    private static $db;
    private static $this_year;

    public static function getRelevance(?User $user): float {
        return 0.8;
    }

    public static function render(): string {
        self::$env_utils = EnvUtils::fromEnv();
        self::$termine_utils = TermineFilterUtils::fromEnv();
        $date_utils = AbstractDateUtils::fromEnv();
        self::$db = DbUtils::fromEnv()->getDb();
        self::$this_year = intval($date_utils->getCurrentDateInFormat('Y'));

        $out = "<h2>Termine</h2>";

        $out .= "<ul class='links'>";
        $out .= self::renderAllUpcomingList();
        $out .= self::renderProgramList();
        $out .= self::renderWeekendsList();
        $out .= self::renderUpcomingTrainingsList();
        $out .= "</ul>";

        return $out;
    }

    protected static function renderAllUpcomingList() {
        $code_href = self::$env_utils->getCodeHref();
        $filter = self::$termine_utils->getDefaultFilter();
        $filter['typ'] = 'alle';
        $filter['datum'] = 'bevorstehend';
        $enc_json_filter = urlencode(json_encode($filter));
        return "<li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>Nächste Termine</a></li>";
    }

    protected static function renderProgramList() {
        $code_href = self::$env_utils->getCodeHref();
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

    protected static function renderWeekendsList() {
        $code_href = self::$env_utils->getCodeHref();
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

    protected static function renderUpcomingTrainingsList() {
        $code_href = self::$env_utils->getCodeHref();
        $filter = self::$termine_utils->getDefaultFilter();
        $filter['typ'] = 'training';
        $filter['datum'] = 'bevorstehend';
        $enc_json_filter = urlencode(json_encode($filter));
        return "<li><a href='{$code_href}termine.php?filter={$enc_json_filter}' class='linkint'>Nächste Trainings</a></li>";
    }

    protected static function getNumberOfEntries($filter) {
        $filter_sql = self::$termine_utils->getSqlFromFilter($filter);
        $res = self::$db->query("SELECT t.id FROM termine t WHERE {$filter_sql}");
        return $res->num_rows;
    }
}
