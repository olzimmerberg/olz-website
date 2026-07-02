<?php

namespace Olz\Parsers;

use Olz\Utils\WithUtilsTrait;

class StravaActivityParser {
    use WithUtilsTrait;

    /**
     * @return array{
     *   name: ?string,
     *   sportType: ?string,
     *   runAt: ?\DateTime,
     *   distanceMeters: ?int,
     *   elevationMeters: ?int,
     * } */
    public function parse_strava_activity_html(string $html_content): array {
        $content = str_replace("\n", " ", $html_content);
        $res = preg_match(
            "/<span class=\"title\">\\s*<a[^>]*>([^<]+)<\\/a>\\s*[–-]+\\s*([^<]+)<\\/span>/",
            $content,
            $matches
        );
        $name = $res ? trim($matches[1]) : null;
        $sport_type = $res ? trim($matches[2]) : null;

        $res = preg_match(
            "/<div class=\"details\"> <time>([^<]+)<\\/time>/",
            $content,
            $matches
        );
        $date_string = $res ? trim($matches[1]) : null;
        $date = $date_string ? ($this->parseDateDe($date_string) ?? $this->parseDateEn($date_string)) : null;

        $res = preg_match(
            "/<strong>([0-9\\.\\,]+)<abbr[^>]*>\\s*km<\\/abbr><\\/strong>\\s*<div class=\"label\">(?:Distanz|Distance)<\\/div>/",
            $content,
            $matches
        );
        $distance = $res ? intval(floatval(str_replace(',', '.', trim($matches[1]))) * 1000) : null;

        $res = preg_match(
            "/<div[^>]*>(?:Höhenmeter|Elevation)<\\/div>\\s*<div[^>]*>\\s*<strong>([0-9\\.\\,]+)<abbr[^>]*>\\s*m<\\/abbr><\\/strong>/",
            $content,
            $matches
        );
        $elevation = $res ? intval(str_replace(',', '.', trim($matches[1]))) : null;

        return [
            'name' => $name,
            'sportType' => $sport_type,
            'runAt' => $date,
            'distanceMeters' => $distance,
            'elevationMeters' => $elevation,
        ];
    }

    protected function parseDateEn(string $date_string): ?\DateTime {
        $pattern = '/([0-9]+):([0-9]+) on \w+, ([0-9]+) (\w+) ([0-9]+)/';
        $res = preg_match($pattern, $date_string, $matches);
        if (!$res) {
            return null;
        }
        $hour = $matches[1];
        $minute = $matches[2];
        $day = $matches[3];
        $pretty_month = strtolower($matches[4]);
        $month_map = [
            'january' => '01',
            'february' => '02',
            'march' => '03',
            'april' => '04',
            'may' => '05',
            'june' => '06',
            'july' => '07',
            'august' => '08',
            'september' => '09',
            'october' => '10',
            'november' => '11',
            'december' => '12',
        ];
        $month = $month_map[$pretty_month] ?? null;
        $year = $matches[5];
        try {
            return new \DateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:00");
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function parseDateDe(string $date_string): ?\DateTime {
        $pattern = '/([0-9]+):([0-9]+) am \w+, den ([0-9]+). (\w+) ([0-9]+)/';
        $res = preg_match($pattern, $date_string, $matches);
        if (!$res) {
            return null;
        }
        $hour = $matches[1];
        $minute = $matches[2];
        $day = $matches[3];
        $pretty_month = strtolower($matches[4]);
        $month_map = [
            'januar' => '01',
            'februar' => '02',
            'märz' => '03',
            'april' => '04',
            'mai' => '05',
            'juni' => '06',
            'juli' => '07',
            'august' => '08',
            'september' => '09',
            'oktober' => '10',
            'november' => '11',
            'dezember' => '12',
        ];
        $month = $month_map[$pretty_month] ?? null;
        $year = $matches[5];
        try {
            return new \DateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:00");
        } catch (\Throwable $th) {
            return null;
        }
    }
}
