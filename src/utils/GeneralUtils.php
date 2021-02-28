<?php

class GeneralUtils {
    public function base64EncodeUrl($string) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    public function base64DecodeUrl($string) {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $string));
    }
}
