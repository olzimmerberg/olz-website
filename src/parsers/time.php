<?php

function time_str_to_seconds($time_str) {
    $time_str = trim($time_str);
    $res = preg_match("/(([0-9]+)[:\\.])?([0-9]+)[:\\.]([0-9]+)/", $time_str, $matches);
    if (!$res) {
        return -1;
    }
    $h = $matches[2];
    $m = $matches[3];
    $s = $matches[4];
    return $h * 3600 + $m * 60 + $s;
}
