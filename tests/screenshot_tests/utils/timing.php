<?php

global $timing_timestamps, $timing_report;

$timing_timestamps = [];
$timing_report = [];

function reset_timing(): void {
    global $timing_timestamps, $timing_report;
    $timing_timestamps = [];
    $timing_report = [];
    tick('total');
}

function tick(string $name): void {
    global $timing_timestamps;
    $now = microtime(true);
    $timing_timestamps[$name] = $now;
}

function tock(string $name, string $report): void {
    global $timing_timestamps, $timing_report;
    $now = microtime(true);
    $existing_report = $timing_report[$report] ?? 0.0;
    $existing_timestamp = $timing_timestamps[$name] ?? $now;
    $timing_report[$report] = $existing_report + ($now - $existing_timestamp);
}

function get_pretty_timing_report(): string {
    global $timing_report;
    tock('total', 'total');
    $max_name_strlen = 0;
    $max_time_intval_strlen = 0;
    foreach ($timing_report as $name => $time) {
        $name_strlen = strlen($name);
        if ($name_strlen > $max_name_strlen) {
            $max_name_strlen = $name_strlen;
        }
        $time_intval_strlen = strlen(strval(intval($time)));
        if ($time_intval_strlen > $max_time_intval_strlen) {
            $max_time_intval_strlen = $time_intval_strlen;
        }
    }
    $out = "\nTiming report\n\n";
    $total_time = $timing_report['total'];
    foreach ($timing_report as $name => $time) {
        $pad_name = str_pad($name, $max_name_strlen, ' ', STR_PAD_LEFT);
        $pad_time = str_pad(number_format($time, 3, '.', '').' s', $max_time_intval_strlen + 6, ' ', STR_PAD_LEFT);
        $pad_percent = str_pad(number_format($time * 100 / $total_time, 1, '.', '').' %', 7, ' ', STR_PAD_LEFT);
        $out .= "{$pad_name} | {$pad_time} | {$pad_percent}\n";
    }
    $out .= "\n";
    return $out;
}
