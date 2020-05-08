<?php

$icns_entries = scandir(__DIR__);
$svg_icons = [];
foreach ($icns_entries as $entry) {
    $is_svg_icon = preg_match('/^([a-zA-Z0-9_]+)_([0-9]+)\.svg$/i', $entry, $matches);
    if (!$is_svg_icon) {
        continue;
    }
    $icon_name = $matches[1];
    $icon_size = $matches[2];
    if (!isset($svg_icons[$icon_name])) {
        $svg_icons[$icon_name] = [];
    }
    $svg_icons[$icon_name][$icon_size] = $entry;
}
foreach ($svg_icons as $icon_name => $icon_by_size) {
    echo "<div><h2>{$icon_name}</h2>";
    foreach ($icon_by_size as $icon_size => $icon) {
        echo "<div><h3>{$icon_size}</h3>";
        $original_size = intval($icon_size);
        $double_size = $original_size * 2;
        $triple_size = $original_size * 3;
        echo "<img src='{$icon}' style='width:{$original_size}px; margin:1px; border:1px solid black;'/>";
        echo "<img src='{$icon}' style='width:{$double_size}px; margin:1px; border:1px solid black;'/>";
        echo "<img src='{$icon}' style='width:{$triple_size}px; margin:1px; border:1px solid black;'/>";

        echo "<div style='display:inline-block; background-color:rgb(200,200,200);'>";
        echo "<img src='{$icon}' style='width:{$original_size}px; margin:1px; border:1px solid black;'/>";
        echo "<img src='{$icon}' style='width:{$double_size}px; margin:1px; border:1px solid black;'/>";
        echo "<img src='{$icon}' style='width:{$triple_size}px; margin:1px; border:1px solid black;'/>";
        echo "</div>";

        echo "</div>";
    }
    echo "</div>";
}
