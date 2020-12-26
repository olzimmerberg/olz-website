<?php

// Ampersand-Ausgabe
ini_set('arg_separator.output', '&amp;');

// Sprache für Datum-/Zeitangaben setzen
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de_DE.UTF8');

// Character encoding
mb_internal_encoding('UTF-8');

// Session-Sicherheit
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
