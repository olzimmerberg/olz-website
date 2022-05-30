<?php

class SolvFetcher {
    private $base_url = "https://www.o-l.ch/";

    public function fetchEventsCsvForYear($year) {
        $path = "/cgi-bin/fixtures";
        $query = "?&year={$year}&kind=&csv=1";
        $url = "{$this->base_url}{$path}{$query}";
        return utf8_encode(file_get_contents($url));
    }

    public function fetchYearlyResultsJson($year) {
        $path = "/cgi-bin/fixtures";
        $query = "?mode=results&year={$year}&json=1";
        $url = "{$this->base_url}{$path}{$query}";
        return utf8_encode(file_get_contents($url));
    }

    public function fetchEventResultsHtml($rank_id) {
        $path = "/cgi-bin/results";
        $query = "?rl_id={$rank_id}&club=OL+Zimmerberg&zwizt=1";
        $url = "{$this->base_url}{$path}{$query}";
        return html_entity_decode(utf8_encode(file_get_contents($url)));
    }
}
