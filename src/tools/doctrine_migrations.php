<?php

require_once __DIR__.'/../config/paths.php';

function migrate_to_latest() {
    global $code_href;
    $cwd = getcwd();
    $target_dir = realpath(__DIR__."/../config");
    chdir($target_dir);
    $command = "./vendor/bin/doctrine-migrations migrate latest --no-interaction";
    exec($command, $output, $code);
    chdir($cwd);
    if ($code !== 0) {
        throw new Exception("Migration failed");
    }
}
