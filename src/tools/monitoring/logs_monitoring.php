<?php

function logs_monitoring() {
    require_once __DIR__.'/../../utils/env/EnvUtils.php';
    $env_utils = EnvUtils::fromEnv();
    $data_path = $env_utils->getDataPath();
    $logs_path = "{$data_path}logs/";
    if (!is_dir($logs_path)) {
        throw new Exception("Expected {$logs_path} to be a directory");
    }

    $last_two_merged_log_file_contents = "";
    $merged_log_index = 0;
    foreach (scandir($logs_path, SCANDIR_SORT_DESCENDING) as $filename) {
        if ($merged_log_index > 1) {
            break;
        }
        if (preg_match('/^merged-.*\.log$/', $filename)) {
            $last_two_merged_log_file_contents = file_get_contents("{$logs_path}{$filename}").$last_two_merged_log_file_contents;
            $merged_log_index++;
        }
    }

    $now = new DateTime();
    $minus_one_hour = DateInterval::createFromDateString("-1 hours");
    $one_hour_ago = $now->add($minus_one_hour);
    $minus_one_day = DateInterval::createFromDateString("-1 days");
    $one_day_ago = $now->add($minus_one_day);

    $logs_in_last_hour = [];
    $logs_in_last_day = [];

    foreach (explode("\n", $last_two_merged_log_file_contents) as $line) {
        $res = preg_match('/^\[([0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2})/', $line, $matches);
        if ($res) {
            $line_timestamp = strtotime($matches[1]);
            if ($line_timestamp > $one_hour_ago->getTimestamp()) {
                $logs_in_last_hour[] = $line;
            }
            if ($line_timestamp > $one_day_ago->getTimestamp()) {
                $logs_in_last_day[] = $line;
            }
        }
    }

    function is_emergency_line($line) {
        return preg_match('/\.EMERGENCY\:/', $line);
    }

    function is_alert_line($line) {
        return preg_match('/\.ALERT\:/', $line);
    }

    function is_critical_line($line) {
        return preg_match('/\.CRITICAL\:/', $line);
    }

    function is_error_line($line) {
        return preg_match('/\.ERROR\:/', $line);
    }

    function is_warning_line($line) {
        return preg_match('/\.WARNING\:/', $line);
    }

    function is_notice_line($line) {
        return preg_match('/\.NOTICE\:/', $line);
    }

    echo "Last hour: ".count($logs_in_last_hour)." logs\n";
    echo "Last day: ".count($logs_in_last_day)." logs\n";

    echo "Last hour: ".count(array_filter($logs_in_last_hour, 'is_emergency_line'))." emergency logs\n";
    echo "Last day: ".count(array_filter($logs_in_last_day, 'is_emergency_line'))." emergency logs\n";

    echo "Last hour: ".count(array_filter($logs_in_last_hour, 'is_alert_line'))." alert logs\n";
    echo "Last day: ".count(array_filter($logs_in_last_day, 'is_alert_line'))." alert logs\n";

    echo "Last hour: ".count(array_filter($logs_in_last_hour, 'is_critical_line'))." critical logs\n";
    echo "Last day: ".count(array_filter($logs_in_last_day, 'is_critical_line'))." critical logs\n";

    echo "Last hour: ".count(array_filter($logs_in_last_hour, 'is_error_line'))." error logs\n";
    echo "Last day: ".count(array_filter($logs_in_last_day, 'is_error_line'))." error logs\n";

    echo "Last hour: ".count(array_filter($logs_in_last_hour, 'is_warning_line'))." warning logs\n";
    echo "Last day: ".count(array_filter($logs_in_last_day, 'is_warning_line'))." warning logs\n";

    echo "Last hour: ".count(array_filter($logs_in_last_hour, 'is_notice_line'))." notice logs\n";
    echo "Last day: ".count(array_filter($logs_in_last_day, 'is_notice_line'))." notice logs\n";

    echo "OK:";
}
