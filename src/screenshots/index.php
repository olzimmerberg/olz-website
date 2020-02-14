<?php

require_once __DIR__.'/../admin/olz_init.php';

$generated_dir = __DIR__.'/generated';
$generated_contents = scandir($generated_dir);
foreach ($generated_contents as $screenshot_path) {
    if ($screenshot_path[0] != '.') {
        echo "<img src='{$code_href}/screenshots/generated/{$screenshot_path}'/><br>\n";
    }
}
