<?php

class GeneralUtils {
    public function base64EncodeUrl($string) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    public function base64DecodeUrl($string) {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $string));
    }

    /**
     * Kompatibilitäts-Layer, falls der Hoster eine bescheuerte Content Security
     * Policy haben sollte (hat er).
     */
    public function obfuscateForUpload(string $content) {
        $url_encoded_content = rawurlencode($content);
        $iv = floor(rand() * 0xFFFF / getrandmax());
        $upload_str = '';
        $current = $iv;
        for ($i = 0; $i < strlen($url_encoded_content); $i++) {
            $chr = ord(substr($url_encoded_content, $i, 1));
            $upload_str .= chr($chr ^ (($current >> 8) & 0xFF));
            $current = (($current << 5) - $current) & 0xFFFF;
        }
        $base64 = base64_encode($upload_str);
        return "{$iv};{$base64}";
    }

    /**
     * Kompatibilitäts-Layer, falls der Hoster eine bescheuerte Content Security
     * Policy haben sollte (hat er).
     */
    public function deobfuscateUpload(string $obfuscated) {
        $semipos = strpos($obfuscated, ';');
        $iv = intval(substr($obfuscated, 0, $semipos));
        $obfusbase64 = substr($obfuscated, $semipos + 1);
        $obfuscontent = base64_decode($obfusbase64);
        $url_encoded_content = '';
        $current = $iv;
        for ($i = 0; $i < strlen($obfuscontent); $i++) {
            $url_encoded_content .= chr(ord($obfuscontent[$i]) ^ (($current >> 8) & 0xFF));
            $current = (($current << 5) - $current) & 0xFFFF;
        }
        $content = rawurldecode($url_encoded_content);
        return $content;
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
        return new self();
    }
}
