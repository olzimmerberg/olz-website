<?php

// Ampersand-Ausgabe
ini_set('arg_separator.output', '&amp;');

// Sprache für Datum-/Zeitangaben setzen
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de_DE.UTF8');

// Character encoding
mb_internal_encoding('UTF-8');

// Session-Sicherheit
ini_set('session.cookie_httponly', 1);
if ($_SERVER['SERVER_NAME'] != '127.0.0.1' && $_SERVER['SERVER_NAME'] != 'localhost') {
    ini_set('session.cookie_secure', 1);
}

// Session start
function session_start_if_cookie_set() {
    if (isset($_COOKIE[session_name()])) {
        session_start();
    }
}
