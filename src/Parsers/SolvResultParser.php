<?php

namespace Olz\Parsers;

use Olz\Entity\SolvResult;

class SolvResultParser {
    protected TimeParser $timeParser;

    public function __construct() {
        $this->timeParser = new TimeParser();
    }

    /** @return array<int, array{result_list_id: string}> */
    public function parse_solv_yearly_results_json(string $json_content): array {
        $hacky_sanitized_json = str_replace(["\n", "\t"], ['', '  '], $json_content);
        if (!json_validate($hacky_sanitized_json, 512, JSON_INVALID_UTF8_IGNORE)) {
            $msg = json_last_error_msg();
            throw new \Exception("Invalid JSON in parse_solv_yearly_results_json (hackyly sanitized): {$msg}\n\n{$hacky_sanitized_json}");
        }
        $data = json_decode($hacky_sanitized_json, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);

        if (!$data || !isset($data['ResultLists']) || !is_array($data['ResultLists'])) {
            return [];
        }

        $result_by_uid = [];
        for ($res_ind = 0; $res_ind < count($data['ResultLists']); $res_ind++) {
            $res = $data['ResultLists'][$res_ind];
            if (!isset($res['UniqueID']) || $res['UniqueID'] == 0) {
                continue;
            }
            $uid = $res['UniqueID'];
            // TODO: find better solution to deal with multiple results
            if (isset($result_by_uid[$uid])) {
                continue;
            }
            $result_by_uid[$uid] = [
                'result_list_id' => strval($res['ResultListID']),
            ];
        }
        return $result_by_uid;
    }

    /** @return array<SolvResult> */
    public function parse_solv_event_result_html(string $html_content, int $event_uid): array {
        $class_headers_count = preg_match_all('/<b>(?:<p><\/p>)?<a href="results\?type=rang&year=([0-9]+)&rl_id=([0-9]+)&kat=([^"]+)&zwizt=1">([^<]+)<\/a><\/b>\s*<pre>/is', $html_content, $class_matches);
        $results = [];
        for ($class_ind = 0; $class_ind < $class_headers_count; $class_ind++) {
            $class_name = $class_matches[4][$class_ind];

            $found_at = mb_strpos($html_content, $class_matches[0][$class_ind]);
            $class_header_ends_at = $found_at + mb_strlen($class_matches[0][$class_ind]);
            $class_body_ends_at = mb_strpos($html_content, '</pre>', $class_header_ends_at);
            $class_body_length = $class_body_ends_at - $class_header_ends_at;
            $class_body = mb_substr($html_content, $class_header_ends_at, $class_body_length);

            $does_class_info_match = preg_match("/^\\s*\\(\\s*([0-9\\.]+)\\s*km\\s*,\\s*([0-9]+)\\s*m\\s*,\\s*([0-9]+)\\s*Po\\.\\s*\\)\\s*([0-9]+)\\s*Teilnehmer/", $class_body, $class_info_matches);
            $class_info = [
                'distance' => $does_class_info_match ? intval(floatval($class_info_matches[1]) * 1000) : 0,
                'elevation' => $does_class_info_match ? intval($class_info_matches[2]) : 0,
                'control_count' => $does_class_info_match ? intval($class_info_matches[3]) : 0,
                'competitor_count' => $does_class_info_match ? intval($class_info_matches[4]) : 0,
            ];

            $competitors_count = preg_match_all('/<b>([^<]+)<\/b>/', $class_body, $competitor_matches);
            for ($competitor_ind = 0; $competitor_ind < $competitors_count; $competitor_ind++) {
                $competitor_line = $competitor_matches[1][$competitor_ind];
                $rank = intval(mb_substr($competitor_line, 0, 3));
                $name = trim(mb_substr($competitor_line, 5, 22));
                $birth_year = trim(mb_substr($competitor_line, 28, 2));
                $domicile = trim(mb_substr($competitor_line, 32, 18));
                $club = trim(mb_substr($competitor_line, 51, 18));
                $result = $this->timeParser->time_str_to_seconds(trim(mb_substr($competitor_line, 70, 8)));

                $found_at = mb_strpos($class_body, $competitor_matches[0][$competitor_ind]);
                $competitor_line_ends_at = $found_at + mb_strlen($competitor_matches[0][$competitor_ind]);
                $splits_ends_at = mb_strpos($class_body, '<b>', $competitor_line_ends_at);
                if ($splits_ends_at === false) {
                    $splits_ends_at = mb_strlen($class_body);
                }
                $splits_length = $splits_ends_at - $competitor_line_ends_at;
                $splits = trim(mb_substr($class_body, $competitor_line_ends_at, $splits_length));

                $finish_offset = mb_strpos($splits, ' Zi ');
                $finish_split = $this->timeParser->time_str_to_seconds(trim(mb_substr($splits, $finish_offset + 4, 6)));

                if ($rank !== 1 || preg_match('/zimmerberg/i', $club)) {
                    $solv_result = new SolvResult();
                    $solv_result->setPerson(0);
                    $solv_result->setEvent($event_uid);
                    $solv_result->setClass($class_name);
                    $solv_result->setRank($rank);
                    $solv_result->setName($name);
                    $solv_result->setBirthYear($birth_year);
                    $solv_result->setDomicile($domicile);
                    $solv_result->setClub($club);
                    $solv_result->setResult($result);
                    $solv_result->setSplits($splits);
                    $solv_result->setFinishSplit($finish_split);
                    $solv_result->setClassDistance($class_info['distance']);
                    $solv_result->setClassElevation($class_info['elevation']);
                    $solv_result->setClassControlCount($class_info['control_count']);
                    $solv_result->setClassCompetitorCount($class_info['competitor_count']);
                    $results[] = $solv_result;
                }
            }
        }
        return $results;
    }
}
