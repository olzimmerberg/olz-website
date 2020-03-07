<?php

require_once __DIR__.'/_/config/paths.php';
require_once __DIR__.'/_/config/database.php';
require_once __DIR__.'/_/tools/dev_data.php';

set_time_limit(120); // This might take some time...

init_dev_data($db, $data_path);

echo "RESET:SUCCESS";
