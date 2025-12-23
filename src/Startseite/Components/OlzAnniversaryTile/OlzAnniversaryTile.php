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
        $is_before_2026 = intval($this->dateUtils()->getCurrentDateInFormat('Y')) < 2026;

        $code_href = $this->envUtils()->getCodeHref();
        $db = $this->dbUtils()->getDb();

        $goal_meters_per_day = 4478;
        $year_start_secs = 1735689600; // 2025-01-01 00:00:00
        $now_secs = $is_before_2026 ? $year_start_secs : strtotime($this->dateUtils()->getIsoNow());
        $goal_elevation = ($now_secs - $year_start_secs) * $goal_meters_per_day / 86400;
        $sql = <<<'ZZZZZZZZZZ'
                SELECT SUM(elevation_meters) as sum_elevation
                FROM anniversary_runs
                WHERE
                    run_at >= '2025-01-01'
                    AND run_at <= '2025-12-31'
            ZZZZZZZZZZ;
        $res_sum_elevation = $db->query($sql);
        $this->generalUtils()->checkNotBool($res_sum_elevation, "Query error: {$sql}");
        $sum_elevation = floatval($res_sum_elevation->fetch_assoc()['sum_elevation'] ?? 0);
        $diff_meters = $sum_elevation - $goal_elevation;
        $diff_days = $diff_meters / $goal_meters_per_day;
        $speed_kind = $diff_days >= 0 ? 'ahead' : 'behind';
        $speed_hei = log10(abs($diff_days) + 1) * 40;

        $value = $sum_elevation / ($goal_meters_per_day * 356);
        $done_hei = intval($value * 160);
        $rocket_hei = intval($value * 160) - 30;
        $rocket = OlzAnniversaryRocket::render();
        return <<<ZZZZZZZZZZ
            <a href='{$code_href}2026' class='anniversary-container'>
                <h3 class='anniversary-h3'>🥳 20 Jahre OL Zimmerberg</h3>
                <div class='done-range'></div>
                <div class='done-bar' style='height: {$done_hei}px;'></div>
                <div class='rocket test-flaky' style='bottom: {$rocket_hei}px;'>{$rocket}</div>
                <div class='speed-range'></div>
                <div class='speed-bar {$speed_kind}' style='height: {$speed_hei}px;'></div>
                <div class='elevation'>{$this->getElevationHtml($value, $diff_meters, $diff_days)}</div>
                <div class='zielsprint'>{$this->getZielsprintHtml($is_before_2026)}</div>
            </a>
            ZZZZZZZZZZ;
    }

    public function getElevationHtml(float $done_part, float $diff_meters, float $diff_days): string {
        $pretty_done = number_format($done_part * 100, 1, ".", "'")."%";
        $diff_kind = $diff_meters >= 0 ? 'ahead' : 'behind';
        $diff_verb = $diff_meters >= 0 ? 'sind' : 'liegen';
        $diff_particle = $diff_meters >= 0 ? 'voraus' : 'zurück';
        $pretty_diff_meters = number_format($diff_meters, 0, ".", "'")."m";
        $pretty_diff_days = number_format($diff_days, 1, ".", "'")." Tage";
        return <<<ZZZZZZZZZZ
            <div class='title'>🏃 Höhenmeter-Challenge ⛰️</div>
            <div>
                Ziel zu <span class='done-text'>{$pretty_done}</span> erreicht.
                Wir {$diff_verb} <span class='diff-meters {$diff_kind}'>{$pretty_diff_meters}</span> bzw. <span class='diff-days {$diff_kind}'>{$pretty_diff_days}</span> {$diff_particle}.</div>
            ZZZZZZZZZZ;
    }

    public function getZielsprintHtml(bool $is_before_2026): string {
        $component = new OlzZielsprint();
        $ranking = $component->getRanking();
        $out = "<div class='title'>🏁 Zielsprint-Challenge 🏃</div><table>";
        $medals = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
        for ($rang = 1; $rang <= 3; $rang++) {
            $entry = $ranking[$rang - 1] ?? [];
            $person_name = $is_before_2026 ? '?' : $entry['person_name'] ?? '?';
            $points = $is_before_2026 ? '0' : $entry['points'] ?? '0';
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
