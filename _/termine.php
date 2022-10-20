<?php

use Olz\Termine\Components\OlzTermineDetail\OlzTermineDetail;
use Olz\Termine\Components\OlzTermineList\OlzTermineList;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$db_table = 'termine';
$id = $_GET['id'] ?? null;

if ($id === null) {
    echo OlzTermineList::render();
} else {
    echo OlzTermineDetail::render();
}
