<?php

$generated_dir = __DIR__.'/generated';
$generated_contents = scandir($generated_dir);
$screenshot_paths = [];
foreach ($generated_contents as $screenshot_path) {
    if ($screenshot_path[0] != '.') {
        $screenshot_paths[] = $screenshot_path;
    }
}
echo json_encode(['screenshot_paths' => $screenshot_paths]);
