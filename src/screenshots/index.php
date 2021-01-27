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
$screenshot_paths_json = json_encode($screenshot_paths);
echo "<script>const screenshotPaths = {$screenshot_paths_json};</script>";
foreach ($screenshot_paths as $screenshot_path) {
    $has_screenshot_id = preg_match('/^([a-z0-9\\-\\_]+)\\.png$/', $screenshot_path, $matches);
    $screenshot_id = $has_screenshot_id ? " id='{$matches[1]}'" : "";
    echo "<div class='pair'>\n";
    echo "<h2{$screenshot_id}>{$screenshot_path}</h2>\n";
    echo "<img class='local' id='local-{$screenshot_path}' />\n";
    echo "<img class='master' id='master-{$screenshot_path}' />\n";
    echo "<div class='after-pair'></div>\n";
    echo "</div>\n";
}
echo "</div>";
echo "<script>
function loadNext(index) {
    if (index >= screenshotPaths.length) {
        return;
    }
    const loadLocal = new Promise((resolve) => {
        const localElem = document.getElementById('local-'+screenshotPaths[index]);
        localElem.src = '{$code_href}screenshots/generated/' + screenshotPaths[index];
        localElem.onload = resolve;
        localElem.onerror = resolve;
    });
    const loadMaster = new Promise((resolve) => {
        const masterElem = document.getElementById('master-'+screenshotPaths[index]);
        masterElem.src = '{$master_href}screenshots/generated/' + screenshotPaths[index];
        masterElem.onload = resolve;
        masterElem.onerror = resolve;
    });
    Promise.all([loadLocal, loadMaster]).then(() => loadNext(index + 1));
}
loadNext(0);
</script>";
