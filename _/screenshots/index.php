<?php

require_once __DIR__.'/../config/paths.php';

$main_href = 'https://olzimmerberg.ch/';

echo "<style>\n";
echo "body { margin: 0; }\n";
echo "#root .pair { border: 10px solid black; }\n";
echo "#root.main { background-color: red; }\n";
echo "#root.local { background-color: green; }\n";
echo "#root.main .pair { border-color: red; }\n";
echo "#root.local .pair { border-color: green; }\n";
echo "#root .main { float: left; }\n";
echo "#root .local { float: left; }\n";
echo "#root.main .local { margin-left:-10000px; }\n";
echo "#root.local .main { margin-left:-10000px; }\n";
echo "#root .after-pair { clear: both; }\n";
echo "#progress { position:fixed; top:0; left:0; background-color:blue; height:5px; width: 0%; }\n";
echo "</style>\n";
echo "<script>\n";
echo "let mode = 'local';\n";
echo "window.setInterval(() => {\n";
echo "    mode = (mode === 'local' ? 'main' : 'local');\n";
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

$main_index = json_decode(
    file_get_contents("{$main_href}screenshots/index.json.php"), true);
if ($main_index === null) {
    echo '<div>No JSON screenshot index on main</div>';
} elseif (!isset($main_index['screenshot_paths'])) {
    echo '<div>Invalid JSON screenshot index on main</div>';
} else {
    $main_paths = $main_index['screenshot_paths'];
    foreach ($main_paths as $main_path) {
        if (array_search($main_path, $screenshot_paths) === false) {
            $screenshot_paths[] = $main_path;
        }
    }
}

sort($screenshot_paths);

echo "<div id='root' class='local'>";
echo "<div id='progress'></div>";
$screenshot_paths_json = json_encode($screenshot_paths);
echo "<script>const screenshotPaths = {$screenshot_paths_json};</script>";
foreach ($screenshot_paths as $screenshot_path) {
    $has_screenshot_id = preg_match('/^([a-z0-9\\-\\_]+)\\.png$/', $screenshot_path, $matches);
    $screenshot_id = $has_screenshot_id ? " id='{$matches[1]}'" : "";
    echo "<div class='pair'>\n";
    echo "<h2{$screenshot_id}>{$screenshot_path}</h2>\n";
    echo "<img class='local' id='local-{$screenshot_path}' />\n";
    echo "<img class='main' id='main-{$screenshot_path}' />\n";
    echo "<div class='after-pair'></div>\n";
    echo "</div>\n";
}
echo "</div>";
echo "<script>
function loadNext(index) {
    const progressElem = document.getElementById('progress');
    progressElem.style.width = Math.round(index * 100 / screenshotPaths.length) + '%';
    if (index >= screenshotPaths.length) {
        return;
    }
    const loadLocal = new Promise((resolve) => {
        const localElem = document.getElementById('local-'+screenshotPaths[index]);
        localElem.src = '{$code_href}screenshots/generated/' + screenshotPaths[index];
        localElem.onload = resolve;
        localElem.onerror = resolve;
    });
    const loadMain = new Promise((resolve) => {
        const mainElem = document.getElementById('main-'+screenshotPaths[index]);
        mainElem.src = '{$main_href}screenshots/generated/' + screenshotPaths[index];
        mainElem.onload = resolve;
        mainElem.onerror = resolve;
    });
    Promise.all([loadLocal, loadMain]).then(() => loadNext(index + 1));
}
loadNext(0);
</script>";
