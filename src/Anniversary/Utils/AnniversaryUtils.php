<?php

namespace Olz\Anniversary\Utils;

use Olz\Utils\WithUtilsTrait;

/**
 * @phpstan-type OlzElevationStats array{
 *   completion: float,
 *   diffMeters: float,
 *   diffDays: float,
 *   diffKind: 'ahead'|'behind',
 * }
 */
class AnniversaryUtils {
    use WithUtilsTrait;

    /** @return OlzElevationStats */
    public function getElevationStats() {
        $db = $this->dbUtils()->getDb();

        $is_before_2026 = intval($this->dateUtils()->getCurrentDateInFormat('Y')) < 2026;
        $goal_meters_per_day = 4478;
        $year_start_secs = 1735689600; // 2025-01-01 00:00:00
        $now_secs = $is_before_2026 ? $year_start_secs : strtotime($this->dateUtils()->getIsoNow());
        $goal_elevation = ($now_secs - $year_start_secs) * $goal_meters_per_day / 86400;
        $sql = <<<'ZZZZZZZZZZ'
                SELECT SUM(elevation_meters) as sum_elevation
                FROM anniversary_runs
                WHERE
                    run_at >= '2026-01-01'
                    AND run_at <= '2026-12-31'
                    AND is_counting = '1'
                    AND on_off = '1'
            ZZZZZZZZZZ;
        $res_sum_elevation = $db->query($sql);
        $this->generalUtils()->checkNotBool($res_sum_elevation, "Query error: {$sql}");
        $sum_elevation = floatval($res_sum_elevation->fetch_assoc()['sum_elevation'] ?? 0);

        $completion = $sum_elevation / ($goal_meters_per_day * 356);
        $diff_meters = $sum_elevation - $goal_elevation;
        $diff_days = $diff_meters / $goal_meters_per_day;
        $diff_kind = $diff_days >= 0 ? 'ahead' : 'behind';

        return [
            'completion' => $completion,
            'diffMeters' => $diff_meters,
            'diffDays' => $diff_days,
            'diffKind' => $diff_kind,
        ];
    }

    public function getPrettySource(string $source): string {
        $source_short = mb_split('-', $source)[0] ?? '?';
        if ($source_short === 'manuell') {
            return "✍️ manuell";
        }
        if ($source_short === 'strava') {
            $code_href = $this->envUtils()->getCodeHref();
            return "<img src='{$code_href}assets/icns/strava_16.svg' alt='s' class='noborder'> strava";
        }
        return "🤷 {$source_short}";
    }
}
