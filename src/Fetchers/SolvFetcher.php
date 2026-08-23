<?php

namespace Olz\Fetchers;

use Olz\Utils\WithUtilsTrait;

class SolvFetcher {
    use WithUtilsTrait;

    private string $base_url = "https://www.o-l.ch/";
    /** @var non-empty-string */
    protected static string $user_agent_string = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36';

    public function fetchEventsCsvForYear(int|string $year): ?string {
        $this->sleep(3);

        $path = "cgi-bin/fixtures";
        $query = "?=&year={$year}&kind=-1&csv=1";
        $url = "{$this->base_url}{$path}{$query}";

        $ch = $this->curlInit($url);
        $result = $this->httpUtils()->curlExec($ch);

        return iconv('ISO-8859-1', 'UTF-8', $result) ?: '';
    }

    public function fetchYearlyResultsJson(int|string $year): ?string {
        $this->sleep(3);

        $path = "cgi-bin/fixtures";
        $query = "?mode=results&year={$year}&json=1";
        $url = "{$this->base_url}{$path}{$query}";

        $ch = $this->curlInit($url);
        $result = $this->httpUtils()->curlExec($ch);

        return iconv('ISO-8859-1', 'UTF-8', $result) ?: '';
    }

    public function fetchEventResultsHtml(int|string $rank_id): ?string {
        $this->sleep(3);

        $path = "cgi-bin/results";
        $query = "?rl_id={$rank_id}&club=OL+Zimmerberg&zwizt=1";
        $url = "{$this->base_url}{$path}{$query}";

        $ch = $this->curlInit($url);
        $result = $this->httpUtils()->curlExec($ch);

        return html_entity_decode(iconv('ISO-8859-1', 'UTF-8', $result) ?: '');
    }

    /** @param non-empty-string $url */
    protected function curlInit(string $url): \CurlHandle {
        $ch = $this->httpUtils()->curlInit($url, [
            'userAgent' => self::$user_agent_string,
        ]);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.o-l.ch/cgi-bin/fixtures');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        return $ch;
    }

    protected function sleep(int $seconds): void {
        sleep($seconds);
    }
}
