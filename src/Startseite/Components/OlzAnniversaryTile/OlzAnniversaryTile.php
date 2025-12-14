<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit manuell eingegebenem Inhalt an.
// =============================================================================

namespace Olz\Startseite\Components\OlzAnniversaryTile;

use Olz\Anniversary\Components\OlzAnniversaryRocket\OlzAnniversaryRocket;
use Olz\Components\OlzZielsprint\OlzZielsprint;
use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzAnniversaryTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        if (!$this->authUtils()->hasPermission('anniversary', $user)) {
            return 0.0;
        }
        return ((bool) $user) ? 0.91 : 0.01;
    }

    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();
        $db = $this->dbUtils()->getDb();

        $goal_meters_per_day = 4478;
        $year = $this->dateUtils()->getCurrentDateInFormat('Y');
        $sql = <<<ZZZZZZZZZZ
                SELECT SUM(elevation_meters) as sum_elevation
                FROM anniversary_runs
                WHERE
                    run_at >= '{$year}-01-01'
                    AND run_at <= '{$year}-12-31'
            ZZZZZZZZZZ;
        $res_sum_elevation = $db->query($sql);
        $this->generalUtils()->checkNotBool($res_sum_elevation, "Query error: {$sql}");
        $sum_elevation = floatval($res_sum_elevation->fetch_assoc()['sum_elevation'] ?? 0);

        $value = $sum_elevation / ($goal_meters_per_day * 356);
        $done_hei = intval($value * 160);
        $rocket_hei = intval($value * 160) - 30;
        $rocket = OlzAnniversaryRocket::render();
        return <<<ZZZZZZZZZZ
            <a href='{$code_href}2026' class='anniversary-container'>
                <h3 class='anniversary-h3'>🥳 20 Jahre OL Zimmerberg</h3>
                <div class='all-bar'></div>
                <div class='done-bar' style='height: {$done_hei}px;'></div>
                <div class='rocket test-flaky' style='bottom: {$rocket_hei}px;'>{$rocket}</div>
                <div class='elevation'>{$this->getElevationHtml()}</div>
                <div class='zielsprint'>{$this->getZielsprintHtml()}</div>
            </a>
            ZZZZZZZZZZ;
    }

    public function getElevationHtml(): string {
        return "<div class='title'>🏃 Höhenmeter-Challenge ⛰️</div>";
    }

    public function getZielsprintHtml(): string {
        $component = new OlzZielsprint();
        $ranking = $component->getRanking();
        $out = "<div class='title'>🏁 Zielsprint-Challenge 🏃</div><table>";
        $medals = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
        for ($rang = 1; $rang <= 3; $rang++) {
            $entry = $ranking[$rang - 1] ?? [];
            $person_name = $entry['person_name'] ?? '?';
            $points = $entry['points'] ?? '0';
            $out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>{$medals[$rang]}</td>
                    <td>{$person_name}</td>
                    <td>{$points}</td>
                </tr>
                ZZZZZZZZZZ;
        }
        $out .= "</table>";
        return $out;
    }
}
