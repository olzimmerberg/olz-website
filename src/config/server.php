<?php

$config_path = $_SERVER['DOCUMENT_ROOT'].'/config.php';
if (!is_file($config_path)) {
    die('Config file not found');
}
$_CONFIG = [];
require_once $config_path;
