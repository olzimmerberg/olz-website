<?php

namespace Olz\Entity;

function sanitize_datetime_value($value) {
    if ($value == null) {
        return null;
    }
    if ($value instanceof \DateTime) {
        return $value;
    }
    if (is_string($value)) {
        $res = preg_match('/[0-9]+\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $value);
        if (!$res) {
            throw new \Exception("Invalid datetime: {$value}", 1);
        }
        $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if (!$datetime) {
            throw new \Exception("Invalid datetime: {$value}", 1);
        }
        return $datetime;
    }
    throw new \Exception("Invalid datetime: {$value}", 1);
}

function sanitize_date_value($value) {
    if ($value == null) {
        return null;
    }
    if ($value instanceof \DateTime) {
        return $value;
    }
    if (is_string($value)) {
        $res = preg_match('/[0-9]+\-[0-9]{2}\-[0-9]{2}/', $value);
        if (!$res) {
            throw new \Exception("Invalid datetime: {$value}", 1);
        }
        $datetime = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$datetime) {
            throw new \Exception("Invalid datetime: {$value}", 1);
        }
        return $datetime;
    }
    throw new \Exception("Invalid datetime: {$value}", 1);
}
