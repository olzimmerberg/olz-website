<?php

namespace Olz\Components\OlzZielsprint;

use Olz\Components\Common\OlzComponent;

/** @extends OlzComponent<array<string, mixed>> */
class OlzZielsprint extends OlzComponent {
    public function getHtml(mixed $args): string {
        $out = '';

        $ranking = $this->getRanking();

        $out .= "<table>";
        $out .= "<tr>";
        $out .= "<th style='border-bottom: 1px solid black; text-align: right;'>Rang&nbsp;</th>";
        $out .= "<th style='border-bottom: 1px solid black;'>Name</th>";
        $out .= "<th style='border-bottom: 1px solid black; text-align: right;'>Punkte</th>";
        $out .= "</tr>";
        $last_points = null;
        $last_actual_rank = 1;
        for ($index = 0; $index < count($ranking); $index++) {
            $rank = $index + 1;
            $ranking_entry = $ranking[$index];
            $person_name = $ranking_entry['person_name'];
            $points = intval($ranking_entry['points']);
            $actual_rank = ($last_points === $points)
                ? $last_actual_rank
                : $rank;

            $calculation = "{$person_name}\\n---\\n";
            foreach ($ranking_entry['calculation'] as $event_calculation) {
                $event_name = $event_calculation['event_name'];
                $event_points = $event_calculation['points'];
                $event_max_points = $event_calculation['max_points'];
                $finish_split = $event_calculation['finish_split'];
                $finish_minutes = floor(intval($finish_split) / 60);
                $finish_seconds = str_pad(strval(intval($finish_split) % 60), 2, '0');
                $pretty_finish_split = "{$finish_minutes}:{$finish_seconds}";
                $calculation .= "{$event_name}: {$event_points} / {$event_max_points} ({$pretty_finish_split})\\n";
            }
            $bgcolor = ($index % 2 === 0) ? 'rgba(0,0,0,0.1)' : 'rgba(0,0,0,0)';
            $out .= "<tr style='background-color:{$bgcolor}; cursor:pointer;' onclick='alert(&quot;{$calculation}&quot;)'>";
            $out .= "<td style='text-align: right;'>{$actual_rank}.&nbsp;</td>";
            $out .= "<td>{$person_name}</td>";
            $out .= "<td style='text-align: right;'>{$points}</td>";
            $out .= "</tr>";
            $last_points = $points;
            $last_actual_rank = $actual_rank;
        }
        $out .= "</table>";

        return $out;
    }

    /**
     * @return array<array{
     *   person_id: int,
     *   person_name: string,
     *   points: int,
     *   calculation: array<array{
     *     event_name: string,
     *     points: int,
     *     finish_split: int,
     *     max_points: int,
     *   }>
     * }>
     */
    public function getRanking(): array {
        $year = 2026;
        $db = $this->dbUtils()->getDb();

        $sql = "
            SELECT solv_uid, name, date
            FROM solv_events
            WHERE
                date>='{$year}-01-01'
                AND date<='{$year}-12-31'
                AND kind='foot'
            ORDER BY date ASC";
        $res_events = $db->query($sql);
        $this->generalUtils()->checkNotBool($res_events, "Query error: {$sql}");
        $points_by_person = [];
        while ($row_event = $res_events->fetch_assoc()) {
            $event_id = intval($row_event['solv_uid']);
            $sql = "SELECT DISTINCT last_control_code FROM solv_results WHERE event='{$event_id}'";
            $res_last_control = $db->query($sql);
            $this->generalUtils()->checkNotBool($res_last_control, "Query error: {$sql}");
            while ($row_last_control = $res_last_control->fetch_assoc()) {
                $last_control_code = intval($row_last_control['last_control_code']);
                // $out .= "<h3>".json_encode($row_event)."</h3>";
                $sql = "
                    SELECT person, finish_split
                    FROM solv_results
                    WHERE
                        event='{$event_id}'
                        AND finish_split > '0'
                        AND last_control_code = '{$last_control_code}'
                    ORDER BY finish_split ASC";
                $res_results = $db->query($sql);
                $this->generalUtils()->checkNotBool($res_results, "Query error: {$sql}");
                $num_participants = intval($res_results->num_rows);
                $last_finish_split = null;
                $last_actual_points = null;
                for ($points = $num_participants; $points > 0; $points--) {
                    $row_results = $res_results->fetch_assoc();
                    $person_id = intval($row_results['person'] ?? 0);
                    $finish_split = intval($row_results['finish_split'] ?? PHP_INT_MAX);
                    $actual_points = ($last_finish_split === $finish_split)
                        ? $last_actual_points
                        : $points;
                    $person_points = $points_by_person[$person_id]
                        ?? ['points' => 0, 'calculation' => []];
                    $points_by_person[$person_id]['points'] = $person_points['points'] + $actual_points;
                    $points_by_person[$person_id]['calculation'][] = [
                        'event_name' => "{$row_event['name']} ({$last_control_code})",
                        'points' => $actual_points,
                        'finish_split' => $finish_split,
                        'max_points' => $num_participants,
                    ];
                    $last_finish_split = $finish_split;
                    $last_actual_points = $actual_points;
                    // $out .= "<div>".json_encode($row_results)."</div>";
                }
            }
        }
        $ranking = [];
        foreach ($points_by_person as $person_id => $points) {
            $sql = "
                SELECT name
                FROM solv_people
                WHERE id='{$person_id}'";
            $res_person = $db->query($sql);
            $this->generalUtils()->checkNotBool($res_person, "Query error: {$sql}");
            $row_person = $res_person->fetch_assoc();
            $person_name = strval($row_person['name'] ?? '?');
            $ranking[] = [
                'person_id' => $person_id,
                'person_name' => $person_name,
                'points' => $points['points'],
                'calculation' => $points['calculation'],
            ];
        }

        usort($ranking, fn ($a, $b) => $b['points'] - $a['points']);

        return $ranking;
    }
}
