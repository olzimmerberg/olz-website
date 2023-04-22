<?php

use Olz\Utils\EnvUtils;

function logs_monitoring() {
    $env_utils = EnvUtils::fromEnv();
    $data_path = $env_utils->getDataPath();
    $logs_path = "{$data_path}logs/";
    if (!is_dir($logs_path)) {
        throw new \Exception("Expected {$logs_path} to be a directory");
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

    $now = new \DateTime();
    $minus_one_hour = \DateInterval::createFromDateString("-1 hours");
    $one_hour_ago = $now->add($minus_one_hour);

    $now = new \DateTime();
    $minus_one_day = \DateInterval::createFromDateString("-1 days");
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

    check_emergencies($logs_in_last_hour, $logs_in_last_day);
    check_alerts($logs_in_last_hour, $logs_in_last_day);
    check_critical($logs_in_last_hour, $logs_in_last_day);
    check_many_errors($logs_in_last_hour, $logs_in_last_day);
    check_many_warnings($logs_in_last_hour, $logs_in_last_day);
    check_many_notices($logs_in_last_hour, $logs_in_last_day);

    echo "OK:";
}

function check_emergencies($logs_in_last_hour, $logs_in_last_day) {
    if (count(array_filter($logs_in_last_hour, 'is_emergency_line')) > 0) {
        throw new \Exception("Expected no emergencies");
    }
}

function check_alerts($logs_in_last_hour, $logs_in_last_day) {
    if (count(array_filter($logs_in_last_hour, 'is_alert_line')) > 0) {
        throw new \Exception("Expected no alerts");
    }
}

function check_critical($logs_in_last_hour, $logs_in_last_day) {
    if (count(array_filter($logs_in_last_hour, 'is_critical_line')) > 0) {
        throw new \Exception("Expected no critical log entries");
    }
}

function check_many_errors($logs_in_last_hour, $logs_in_last_day) {
    $limit_per_hour = 1;
    $limit_per_day = 5;

    $errors_per_hour = count(array_filter($logs_in_last_hour, 'is_error_line'));
    if ($errors_per_hour > $limit_per_hour) {
        throw new \Exception("Expected fewer error log entries per hour ({$errors_per_hour} > {$limit_per_hour})");
    }

    $errors_per_day = count(array_filter($logs_in_last_day, 'is_error_line'));
    if ($errors_per_day > $limit_per_day) {
        throw new \Exception("Expected fewer error log entries per day ({$errors_per_day} > {$limit_per_day})");
    }
}

function check_many_warnings($logs_in_last_hour, $logs_in_last_day) {
    $limit_per_hour = 10;
    $limit_per_day = 50;

    $warnings_per_hour = count(array_filter($logs_in_last_hour, 'is_warning_line'));
    if ($warnings_per_hour > $limit_per_hour) {
        throw new \Exception("Expected fewer warning log entries per hour ({$warnings_per_hour} > {$limit_per_hour})");
    }

    $warnings_per_day = count(array_filter($logs_in_last_day, 'is_warning_line'));
    if ($warnings_per_day > $limit_per_day) {
        throw new \Exception("Expected fewer warning log entries per day ({$warnings_per_day} > {$limit_per_day})");
    }
}

function check_many_notices($logs_in_last_hour, $logs_in_last_day) {
    $limit_per_hour = 100;
    $limit_per_day = 5000;

    $notices_per_hour = count(array_filter($logs_in_last_hour, 'is_notice_line'));
    if ($notices_per_hour > $limit_per_hour) {
        throw new \Exception("Expected fewer notice log entries per hour ({$notices_per_hour} > {$limit_per_hour})");
    }

    $notices_per_day = count(array_filter($logs_in_last_day, 'is_notice_line'));
    if ($notices_per_day > $limit_per_day) {
        throw new \Exception("Expected fewer notice log entries per day ({$notices_per_day} > {$limit_per_day})");
    }
}

function is_emergency_line($line) {
    return preg_match('/\.EMERGENCY\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.EMERGENCY\:/', $line) && !preg_match('/Olz\\Command\\Monitor/', $line);
}

function is_alert_line($line) {
    return preg_match('/\.ALERT\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.ALERT\:/', $line) && !preg_match('/Olz\\Command\\Monitor/', $line);
}

function is_critical_line($line) {
    return preg_match('/\.CRITICAL\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.CRITICAL\:/', $line) && !preg_match('/Olz\\Command\\Monitor/', $line);
}

function is_error_line($line) {
    return preg_match('/\.ERROR\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.ERROR\:/', $line) && !preg_match('/Olz\\Command\\Monitor/', $line);
}

function is_warning_line($line) {
    return preg_match('/\.WARNING\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.WARNING\:/', $line) && !preg_match('/Olz\\Command\\Monitor/', $line);
}

function is_notice_line($line) {
    return preg_match('/\.NOTICE\:/', $line) && !preg_match('/Tool\:\w+-monitoring\.NOTICE\:/', $line) && !preg_match('/Olz\\Command\\Monitor/', $line);
}
