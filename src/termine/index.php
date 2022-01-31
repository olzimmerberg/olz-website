<?php

require_once __DIR__.'/../config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/../admin/olz_functions.php';

require_once __DIR__.'/../file_tools.php';
require_once __DIR__.'/../image_tools.php';

$db_table = 'termine';
$id = $_GET['id'] ?? null;

if ($id === null) {
    include __DIR__.'/termine_list.php';
} else {
    include __DIR__.'/termine_detail.php';
}