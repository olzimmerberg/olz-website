<?php

// Ampersand output
ini_set('arg_separator.output', '&amp;');

// Language for Date / Time output
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de_DE.UTF8');

// Character encoding
mb_internal_encoding('UTF-8');

// Session security
if (!headers_sent()) {
    ini_set('session.gc_maxlifetime', 2419200); // keep one month
    ini_set('session.cookie_httponly', 1);
    $server_name = $_SERVER['SERVER_NAME'] ?? '';
    if ($server_name != '127.0.0.1' && $server_name != 'localhost') {
        ini_set('session.cookie_secure', 1);
    }
}

// Session start
function session_start_if_cookie_set() {
    if (isset($_COOKIE[session_name()])) {
        @session_start();
    }
}
