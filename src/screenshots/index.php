<?php

require_once __DIR__.'/../admin/olz_init.php';

$master_href = 'https://olzimmerberg.ch/_/';

echo "<style>\n";
echo "#root { text-align: center; }\n";
echo "#root.master { background-color: red; }\n";
echo "#root.local { background-color: green; }\n";
echo "#root.master .local { display: none; }\n";
echo "#root.local .master { display: none; }\n";
echo "</style>\n";
echo "<script>\n";
echo "let mode = 'local';\n";
echo "window.setInterval(() => {\n";
echo "    mode = (mode === 'local' ? 'master' : 'local');\n";
echo "    document.getElementById('root').className = mode;\n";
echo "}, 1000);\n";
echo "</script>\n";

$generated_dir = __DIR__.'/generated';
$generated_contents = scandir($generated_dir);
echo "<div id='root' class='local'>";
foreach ($generated_contents as $screenshot_path) {
    if ($screenshot_path[0] != '.') {
        echo "<img src='{$code_href}screenshots/generated/{$screenshot_path}' class='local' />\n";
        echo "<img src='{$master_href}screenshots/generated/{$screenshot_path}' class='master' />\n";
        echo "<br>\n";
    }
}
echo "</div>";
