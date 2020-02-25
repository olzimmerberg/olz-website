<?php

echo "<h2>In Bearbeitung</h2>";

$sql = "SELECT solv_uid, name FROM solv_events"; //  WHERE...
$res_events = $db->query($sql);
$points_by_person = [];
while ($row_event = $res_events->fetch_assoc()) {
    echo "<h3>".json_encode($row_event)."</h3>";
    $event_id = intval($row_event['solv_uid']);
    $sql = "SELECT person, finish_split FROM solv_results WHERE event='{$event_id}' ORDER BY finish_split DESC";
    $res_results = $db->query($sql);
    for ($points = 1; $points <= $res_results->num_rows; $points++) {
        $row_results = $res_results->fetch_assoc();
        $person_id = intval($row_results['person']);
        $person_points = isset($points_by_person[$person_id]) ? $points_by_person[$person_id] : 0;
        $points_by_person[$person_id] = $person_points + $points;
        echo "<div>".json_encode($row_results)."</div>";
    }
}
$ranking = [];
foreach ($points_by_person as $person_id => $points) {
    $ranking[] = ['person_id' => $person_id, 'points' => $points];
}
function ranking_compare($a, $b) {
    return $a['points'] - $b['points'];
}
usort($ranking, 'ranking_compare');

echo "<h2>Rangliste</h2>";
foreach ($ranking as $rank) {
    $person_id = intval($rank['person_id']);
    $points = intval($rank['points']);
    $sql = "SELECT name FROM solv_people WHERE id='{$person_id}'";
    $res_person = $db->query($sql);
    $row_person = $res_person->fetch_assoc();
    $person_name = $row_person['name'];
    echo "<div>{$person_name}: {$points}</div>";
}
