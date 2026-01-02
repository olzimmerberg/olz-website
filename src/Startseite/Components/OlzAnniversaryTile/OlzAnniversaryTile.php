<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit manuell eingegebenem Inhalt an.
// =============================================================================

namespace Olz\Startseite\Components\OlzAnniversaryTile;

use Olz\Anniversary\Components\OlzAnniversaryRocket\OlzAnniversaryRocket;
use Olz\Anniversary\Utils\AnniversaryUtils;
use Olz\Components\OlzZielsprint\OlzZielsprint;
use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

/**
 * @phpstan-import-type OlzElevationStats from AnniversaryUtils
 */
class OlzAnniversaryTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return ((bool) $user) ? 0.91 : 0.01;
    }

    public function getHtml(mixed $args): string {
        $is_before_2026 = intval($this->dateUtils()->getCurrentDateInFormat('Y')) < 2026;
        $code_href = $this->envUtils()->getCodeHref();

        $stats = $this->anniversaryUtils()->getElevationStats();

        $diff_hei = log10(abs($stats['diffDays']) + 1) * 40;
        $done_hei = \intval($stats['completion'] * 160);
        $rocket_hei = \intval($stats['completion'] * 160) - 30;
        $rocket = OlzAnniversaryRocket::render();
        return <<<ZZZZZZZZZZ
            <a href='{$code_href}2026' class='anniversary-container'>
                <h3 class='anniversary-h3'>🥳 20 Jahre OL Zimmerberg</h3>
                <div class='done-range'></div>
                <div class='done-bar' style='height: {$done_hei}px;'></div>
                <div class='rocket test-flaky' style='bottom: {$rocket_hei}px;'>{$rocket}</div>
                <div class='diff-range'></div>
                <div class='diff-bar {$stats['diffKind']}' style='height: {$diff_hei}px;'></div>
                <div class='diff-marker'></div>
                <div class='elevation'>{$this->getElevationHtml($stats)}</div>
                <div class='zielsprint'>{$this->getZielsprintHtml($is_before_2026)}</div>
            </a>
            ZZZZZZZZZZ;
    }

    /**
     * @param OlzElevationStats $stats
     */
    public function getElevationHtml(array $stats): string {
        $pretty_sum_meters = number_format($stats['sumMeters'], 0, ".", "'")."m";
        $pretty_done = number_format($stats['completion'] * 100, 1, ".", "'")."%";
        $diff_verb = $stats['diffMeters'] >= 0 ? 'sind' : 'liegen';
        $diff_particle = $stats['diffMeters'] >= 0 ? 'voraus' : 'zurück';
        $pretty_diff_meters = number_format(abs($stats['diffMeters']), 0, ".", "'")."m";
        $pretty_diff_days = number_format(abs($stats['diffDays']), 1, ".", "'")." Tage";
        return <<<ZZZZZZZZZZ
            <div class='title'>🏃 Höhenmeter-Challenge ⛰️</div>
            <div>
                Ziel mit
                <span class='done-text'>
                    {$pretty_sum_meters}
                </span>
                zu
                <span class='done-text'>
                    {$pretty_done}
                </span>
                erreicht.

                Wir {$diff_verb}
                <span class='diff-meters {$stats['diffKind']}'>
                    {$pretty_diff_meters}
                </span>
                bzw.
                <span class='diff-days {$stats['diffKind']}'>
                    {$pretty_diff_days}
                </span>
                {$diff_particle}.
            </div>
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
