<?php

// =============================================================================
// Konfiguration der Datenbank-Verbindung
// =============================================================================

global $db;

if (!isset($db)) {
    require_once __DIR__.'/server.php';

    $db = new mysqli(
        $_CONFIG->getMysqlServer(),
        $_CONFIG->getMysqlUsername(),
        $_CONFIG->getMysqlPassword(),
        $_CONFIG->getMysqlSchema()
    );
}

if ($db->connect_error) {
    exit("Connect Error (".$db->connect_errno.") ".$db->connect_error);
}

$db->set_charset('utf8mb4');
$db->query("SET NAMES utf8mb4");
$db->query("SET time_zone = '+00:00';");

function DBEsc($str) {
    global $db;
    return $db->escape_string($str);
}
