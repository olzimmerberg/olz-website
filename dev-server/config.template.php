<?php

/** Copy this file to ./config.php and fill in info for local MySQL server. */

$db = new mysqli("localhost:3306", "db-username", "db-password", "db-schema");

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

?>
