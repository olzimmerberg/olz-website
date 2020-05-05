<?php

// =============================================================================
// Zeigt Informationen zur Zielsprint-Challenge 2020 an.
// =============================================================================

echo "<h2>OLZ-Zielsprint-Challenge 2020</h2>";

//echo "<div style='color:rgb(180,0,0); font-weight:bold; text-align:center; font-size:14px;'>In Bearbeitung</div>";
olz_text_insert(9);

$sql = "SELECT solv_uid, name, date FROM solv_events WHERE date>'2020-03-13' AND date<'2021-01-01' AND kind='foot' ORDER BY date ASC";
$res_events = $db->query($sql);
$points_by_person = [];
while ($row_event = $res_events->fetch_assoc()) {
    // echo "<h3>".json_encode($row_event)."</h3>";
    $event_id = intval($row_event['solv_uid']);
    $sql = "SELECT person, finish_split FROM solv_results WHERE event='{$event_id}' ORDER BY finish_split DESC";
    $res_results = $db->query($sql);
    for ($points = 1; $points <= $res_results->num_rows; $points++) {
        $row_results = $res_results->fetch_assoc();
        $person_id = intval($row_results['person']);
        $person_points = isset($points_by_person[$person_id]) ? $points_by_person[$person_id] : 0;
        $points_by_person[$person_id] = $person_points + $points;
        // echo "<div>".json_encode($row_results)."</div>";
    }
}
$ranking = [];
foreach ($points_by_person as $person_id => $points) {
    $ranking[] = ['person_id' => $person_id, 'points' => $points];
}
function ranking_compare($a, $b) {
    return $b['points'] - $a['points'];
}
usort($ranking, 'ranking_compare');

echo "<table>";
echo "<tr>";
echo "<th style='border-bottom: 1px solid black; text-align: right;'>Rang&nbsp;</th>";
echo "<th style='border-bottom: 1px solid black;'>Name</th>";
echo "<th style='border-bottom: 1px solid black; text-align: right;'>Punkte</th>";
echo "</tr>";
for ($index = 0; $index < count($ranking); $index++) {
    $rank = $index + 1;
    $ranking_entry = $ranking[$index];
    $person_id = intval($ranking_entry['person_id']);
    $points = intval($ranking_entry['points']);
    $sql = "SELECT name FROM solv_people WHERE id='{$person_id}'";
    $res_person = $db->query($sql);
    $row_person = $res_person->fetch_assoc();
    $person_name = $row_person['name'];
    $bgcolor = ($index % 2 === 0) ? 'rgba(0,0,0,0.1)' : 'rgba(0,0,0,0)';
    echo "<tr style='background-color:{$bgcolor};'>";
    echo "<td style='text-align: right;'>{$rank}.&nbsp;</td>";
    echo "<td>{$person_name}</td>";
    echo "<td style='text-align: right;'>{$points}</td>";
    echo "</tr>";
}
echo "</table>";
