<?php

require_once __DIR__.'/../config/paths.php';

$master_href = 'https://olzimmerberg.ch/_/';

echo "<style>\n";
echo "body { margin: 0; }\n";
echo "#root .pair { border: 10px solid black; }\n";
echo "#root.master { background-color: red; }\n";
echo "#root.local { background-color: green; }\n";
echo "#root.master .pair { border-color: red; }\n";
echo "#root.local .pair { border-color: green; }\n";
echo "#root .master { float: left; }\n";
echo "#root .local { float: left; }\n";
echo "#root.master .local { margin-left:-10000px; }\n";
echo "#root.local .master { margin-left:-10000px; }\n";
echo "#root .after-pair { clear: both; }\n";
echo "</style>\n";
echo "<script>\n";
echo "let mode = 'local';\n";
echo "window.setInterval(() => {\n";
echo "    mode = (mode === 'local' ? 'master' : 'local');\n";
echo "    document.getElementById('root').className = mode;\n";
echo "}, 1000);\n";
echo "</script>\n";

$screenshot_paths = [];

$generated_dir = __DIR__.'/generated';
$generated_contents = scandir($generated_dir);
foreach ($generated_contents as $screenshot_path) {
    if ($screenshot_path[0] != '.') {
        $screenshot_paths[] = $screenshot_path;
    }
}

$master_index = json_decode(
    file_get_contents("{$master_href}screenshots/index.json.php"), true);
if ($master_index === null) {
    echo '<div>No JSON screenshot index on master</div>';
} elseif (!isset($master_index['screenshot_paths'])) {
    echo '<div>Invalid JSON screenshot index on master</div>';
} else {
    $master_paths = $master_index['screenshot_paths'];
    foreach ($master_paths as $master_path) {
        if (array_search($master_path, $screenshot_paths) === false) {
            $screenshot_paths[] = $master_path;
        }
    }
}

sort($screenshot_paths);

echo "<div id='root' class='local'>";
foreach ($screenshot_paths as $screenshot_path) {
    echo "<div class='pair'>\n";
    echo "<h2>{$screenshot_path}</h2>\n";
    echo "<img src='{$code_href}screenshots/generated/{$screenshot_path}' class='local' />\n";
    echo "<img src='{$master_href}screenshots/generated/{$screenshot_path}' class='master' />\n";
    echo "<div class='after-pair'></div>\n";
    echo "</div>\n";
}
echo "</div>";
