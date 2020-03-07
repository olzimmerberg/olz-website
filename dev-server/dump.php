<?php

require_once __DIR__.'/_/config/database.php';
require_once __DIR__.'/_/tools/dev_data.php';

set_time_limit(120); // This might take some time...

$sql_structure = dump_db_structure_sql($db);
$sql_content = dump_db_content_sql($db);

file_put_contents(__DIR__.'/_/tools/dev-data/db_structure.sql', $sql_structure);
file_put_contents(__DIR__.'/_/tools/dev-data/db_content.sql', $sql_content);
