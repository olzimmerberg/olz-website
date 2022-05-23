<?php

require_once __DIR__.'/../../public/_/utils/IdUtils.php';

$id_utils = new IdUtils();

$external_id = $id_utils->toExternalId($argv[1], $argv[2] ?? null);

echo "External ID: {$external_id}\n";
