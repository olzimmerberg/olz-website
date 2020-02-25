<?php

require_once __DIR__.'/time.php';
require_once __DIR__.'/../model/SolvResult.php';

function parse_solv_yearly_results_json($json_content) {
    $data = json_decode($json_content, true);

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
            'result_list_id' => $res['ResultListID'],
        ];
    }
    return $result_by_uid;
}

function parse_solv_event_result_html($html_content, $event_uid) {
    $class_headers_count = preg_match_all('/<b>(?:<p><\\/p>)?<a href="results\\?type=rang&year=([0-9]+)&rl_id=([0-9]+)&kat=([^"]+)&zwizt=1">([^<]+)<\\/a><\\/b>\s*<pre>/is', $html_content, $class_matches);
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

        $competitors_count = preg_match_all('/<b>([^<]+)<\\/b>/', $class_body, $competitor_matches);
        for ($competitor_ind = 0; $competitor_ind < $competitors_count; $competitor_ind++) {
            $competitor_line = $competitor_matches[1][$competitor_ind];
            $rank = intval(mb_substr($competitor_line, 0, 3));
            $name = trim(mb_substr($competitor_line, 5, 22));
            $birth_year = trim(mb_substr($competitor_line, 28, 2));
            $domicile = trim(mb_substr($competitor_line, 32, 18));
            $club = trim(mb_substr($competitor_line, 51, 18));
            $result = time_str_to_seconds(trim(mb_substr($competitor_line, 70, 8)));

            $found_at = mb_strpos($class_body, $competitor_matches[0][$competitor_ind]);
            $competitor_line_ends_at = $found_at + mb_strlen($competitor_matches[0][$competitor_ind]);
            $splits_ends_at = mb_strpos($class_body, '<b>', $competitor_line_ends_at);
            if ($splits_ends_at === false) {
                $splits_ends_at = mb_strlen($class_body);
            }
            $splits_length = $splits_ends_at - $competitor_line_ends_at;
            $splits = trim(mb_substr($class_body, $competitor_line_ends_at, $splits_length));

            $finish_offset = mb_strpos($splits, ' Zi ');
            $finish_split = time_str_to_seconds(trim(mb_substr($splits, $finish_offset + 4, 6)));

            if ($rank !== 1 || preg_match('/zimmerberg/i', $club)) {
                $solv_result = new SolvResult();
                $solv_result->event = $event_uid;
                $solv_result->class = $class_name;
                $solv_result->rank = $rank;
                $solv_result->name = $name;
                $solv_result->birth_year = $birth_year;
                $solv_result->domicile = $domicile;
                $solv_result->club = $club;
                $solv_result->result = $result;
                $solv_result->splits = $splits;
                $solv_result->finish_split = $finish_split;
                $solv_result->class_distance = $class_info['distance'];
                $solv_result->class_elevation = $class_info['elevation'];
                $solv_result->class_control_count = $class_info['control_count'];
                $solv_result->class_competitor_count = $class_info['competitor_count'];
                $results[] = $solv_result;
            }
        }
    }
    return $results;
}
