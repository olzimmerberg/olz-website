<?php

namespace Olz\Components\OlzZielsprint;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;

/** @extends OlzComponent<array<string, mixed>> */
class OlzZielsprint extends OlzComponent {
    public function getHtml(mixed $args): string {
        $out = '';

        $db = $this->dbUtils()->getDb();

        $out .= "<h2>OLZ-Zielsprint-Challenge 2026</h2>";

        // $out .= "<div style='color:rgb(180,0,0); font-weight:bold; text-align:center; font-size:14px;'>In Bearbeitung</div>";
        $out .= OlzEditableText::render(['snippet_id' => 9]);

        $sql = "
            SELECT solv_uid, name, date
            FROM solv_events
            WHERE
                date>'2025-01-01'
                AND date<'2025-01-01'
                AND kind='foot'
            ORDER BY date ASC";
        $res_events = $db->query($sql);
        $points_by_person = [];
        // @phpstan-ignore-next-line
        while ($row_event = $res_events->fetch_assoc()) {
            // $out .= "<h3>".json_encode($row_event)."</h3>";
            $event_id = intval($row_event['solv_uid']);
            $sql = "
                SELECT person, finish_split
                FROM solv_results
                WHERE
                    event='{$event_id}'
                    AND finish_split > '0'
                ORDER BY finish_split ASC";
            $res_results = $db->query($sql);
            $last_finish_split = null;
            $last_actual_points = null;
            // @phpstan-ignore-next-line
            for ($points = $res_results->num_rows; $points > 0; $points--) {
                // @phpstan-ignore-next-line
                $row_results = $res_results->fetch_assoc();
                // @phpstan-ignore-next-line
                $person_id = intval($row_results['person']);
                // @phpstan-ignore-next-line
                $finish_split = $row_results['finish_split'];
                $actual_points = ($last_finish_split === $finish_split)
                    ? $last_actual_points
                    : $points;
                $person_points = $points_by_person[$person_id]
                    ?? ['points' => 0, 'calculation' => []];
                $points_by_person[$person_id]['points'] = $person_points['points'] + $actual_points;
                $points_by_person[$person_id]['calculation'][] = [
                    'event_name' => $row_event['name'],
                    'points' => $actual_points,
                    'finish_split' => $finish_split,
                    // @phpstan-ignore-next-line
                    'max_points' => $res_results->num_rows,
                ];
                $last_finish_split = $finish_split;
                $last_actual_points = $actual_points;
                // $out .= "<div>".json_encode($row_results)."</div>";
            }
        }
        $ranking = [];
        foreach ($points_by_person as $person_id => $points) {
            $ranking[] = [
                'person_id' => $person_id,
                'points' => $points['points'],
                'calculation' => $points['calculation'],
            ];
        }

        // @phpstan-ignore-next-line
        usort($ranking, function ($a, $b) {
            return $b['points'] - $a['points'];
        });

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
            $person_id = intval($ranking_entry['person_id']);
            $points = intval($ranking_entry['points']);
            $actual_rank = ($last_points === $points)
                ? $last_actual_rank
                : $rank;
            $sql = "
                SELECT name
                FROM solv_people
                WHERE id='{$person_id}'";
            $res_person = $db->query($sql);
            // @phpstan-ignore-next-line
            $row_person = $res_person->fetch_assoc();
            // @phpstan-ignore-next-line
            $person_name = $row_person['name'];
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
}
