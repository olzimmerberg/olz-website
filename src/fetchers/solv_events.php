<?php

function fetch_solv_events_csv_for_year($year) {
    $url = "https://www.o-l.ch/cgi-bin/fixtures?&year={$year}&kind=&csv=1";
    return utf8_encode(file_get_contents($url));
}
