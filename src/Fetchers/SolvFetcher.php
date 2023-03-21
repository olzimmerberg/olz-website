<?php

namespace Olz\Fetchers;

class SolvFetcher {
    private $base_url = "https://www.o-l.ch/";
    private $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36';

    public function fetchEventsCsvForYear($year) {
        $path = "/cgi-bin/fixtures";
        $query = "?=&year={$year}&kind=-1&csv=1";
        $url = "{$this->base_url}{$path}{$query}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = $this->curlExec($ch);

        return iconv('ISO-8859-1', 'UTF-8', $result);
    }

    public function fetchYearlyResultsJson($year) {
        $path = "/cgi-bin/fixtures";
        $query = "?mode=results&year={$year}&json=1";
        $url = "{$this->base_url}{$path}{$query}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = $this->curlExec($ch);

        return iconv('ISO-8859-1', 'UTF-8', $result);
    }

    public function fetchEventResultsHtml($rank_id) {
        $path = "/cgi-bin/results";
        $query = "?rl_id={$rank_id}&club=OL+Zimmerberg&zwizt=1";
        $url = "{$this->base_url}{$path}{$query}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = $this->curlExec($ch);

        return html_entity_decode(iconv('ISO-8859-1', 'UTF-8', $result));
    }

    protected function curlExec($ch) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.o-l.ch/cgi-bin/fixtures');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($errno) {
            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            throw new Exception("Error fetching {$url}: {$error} ({$errno})");
        }
        return $result;
    }
}
