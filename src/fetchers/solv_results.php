<?php

function fetch_solv_yearly_results_json($year) {
    $url = "https://www.o-l.ch/cgi-bin/fixtures?mode=results&year={$year}&json=1";
    return utf8_encode(file_get_contents($url));
}

function fetch_solv_event_results_html($rank_id) {
    $url = "https://www.o-l.ch/cgi-bin/results?rl_id={$rank_id}&club=OL+Zimmerberg&zwizt=1";
    return html_entity_decode(utf8_encode(file_get_contents($url)));
}
