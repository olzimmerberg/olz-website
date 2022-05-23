<?php

require_once __DIR__.'/../config/paths.php';

// TODO: deprecate this in favor of `migrate_to`!
function migrate_to_latest() {
    global $code_href;
    $cwd = getcwd();
    $target_dir = realpath(__DIR__."/../config");
    chdir($target_dir);
    $command = "./vendor/bin/doctrine-migrations migrate latest --no-interaction";
    exec($command, $output, $code);
    if ($code !== 0) { // Because CLI PHP on Hoststar does not work!
        echo $output;
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = 'tools/doctrine_migrate_to_latest.php';
        $url = "{$protocol}://{$host}{$code_href}{$path}";
        file_get_contents($url);
    }
    chdir($cwd);
}

function migrate_to($version = 'latest') {
    global $code_href;
    $cwd = getcwd();
    $target_dir = realpath(__DIR__."/../config");
    chdir($target_dir);
    $command = "./vendor/bin/doctrine-migrations migrate '{$version}' --no-interaction";
    exec($command, $output, $code);
    chdir($cwd);
}
