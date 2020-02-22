<?php

require_once __DIR__.'/server.php';

if (!isset($db)) {
    $db = new mysqli($MYSQL_SERVER, $MYSQL_USERNAME, $MYSQL_PASSWORD, $MYSQL_SCHEMA);
}

if ($db->connect_error) {
    die("Connect Error (".$db->connect_errno.") ".$db->connect_error);
}

$db->query("SET NAMES utf8");

function DBEsc($str) {
    global $db;
    return $db->escape_string($str);
}
