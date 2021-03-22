<?php

class GeneralUtils {
    public function base64EncodeUrl($string) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    public function base64DecodeUrl($string) {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $string));
    }

    public function getPrettyTrace($trace) {
        $output = 'Stack trace:'.PHP_EOL;

        $trace_len = count($trace);
        for ($i = 1; $i < $trace_len; $i++) {
            $entry = $trace[$i];

            $func = $entry['function'].'(';
            $args_len = count($entry['args']);
            for ($j = 0; $j < $args_len; $j++) {
                $func .= json_encode($entry['args'][$j]);
                if ($j < $args_len - 1) {
                    $func .= ', ';
                }
            }
            $func .= ')';

            $output .= '#'.($i - 1).' '.$entry['file'].':'.$entry['line'].' - '.$func.PHP_EOL;
        }
        return $output;
    }

    public static function fromEnv() {
        return new GeneralUtils();
    }
}
