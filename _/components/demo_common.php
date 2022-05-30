<?php

function get_demo($base_dir, $demo_name) {
    $css_path = $base_dir."/jsbuild/main.min.css";
    $js_path = $base_dir."/jsbuild/main.js";
    $css_modified = is_file($css_path) ? filemtime($css_path) : 0;
    $js_modified = is_file($js_path) ? filemtime($js_path) : 0;

    $demo_name_json = json_encode($demo_name);

    return <<<ZZZZZZZZZZ
    <!DOCTYPE html>
    <html lang='de'>
    <head>
        <base href='/_/' />
        <link rel='stylesheet' href='/_/jsbuild/main.min.css?modified={$css_modified}' />
        <script type='text/javascript' src='/_/jsbuild/main.min.js?modified={$js_modified}'></script>
    </head>
    <body>
        <div id='demo-root'></div>
        <script type='text/javascript'>
            window.demos[{$demo_name_json}]();
        </script>
    </body>
    </html>
    ZZZZZZZZZZ;
}
