<?php

namespace Olz\Components\Page\OlzHeader;

use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;
use Olz\Utils\HttpUtils;

class OlzHeader {
    public static function render($args = []) {
        global $_CONFIG, $_SERVER;

        require_once __DIR__.'/../../../../_/config/server.php';

        $is_insecure_nonlocal = !($_SERVER['HTTPS'] ?? false) && preg_match('/olzimmerberg\.ch/', $_SERVER['HTTP_HOST']);
        $host_has_www = preg_match('/www\./', $_SERVER['HTTP_HOST']);
        $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        if ($is_insecure_nonlocal || $host_has_www) {
            $request_uri = $_SERVER['REQUEST_URI'];
            HttpUtils::fromEnv()->redirect("https://{$host}{$request_uri}", 308);
        }

        return OlzHeaderWithoutRouting::render($args);
    }
}