<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/window.php';

function take_pageshot($driver, $name) {
    adjust_css_for_pageshot($driver);
    $browser_name = $driver->getCapabilities()->getBrowserName();
    $screenshots_path = __DIR__.'/../../../screenshots/';
    $screenshot_filename = "{$name}-{$browser_name}.png";
    $window_width = get_window_width($driver);
    $window_height = get_window_height($driver);
    $body_width = get_body_width($driver);
    $body_height = get_body_height($driver);
    $num_x = ceil($body_width / $window_width);
    $num_y = ceil($body_height / $window_height);
    $dest = imagecreatetruecolor($body_width, $body_height);
    for ($x = 0; $x < $num_x; $x++) {
        for ($y = 0; $y < $num_y; $y++) {
            $scroll_x = $x * $window_width;
            $scroll_y = $y * $window_height;
            window_scroll_to($driver, $scroll_x, $scroll_y);
            $path = "{$screenshots_path}{$x}-{$y}-{$screenshot_filename}";
            hide_flaky_elements($driver);
            $driver->takeScreenshot($path);
            $scroll_x_diff = $scroll_x - get_window_scroll_x($driver);
            $scroll_y_diff = $scroll_y - get_window_scroll_y($driver);
            $src = imagecreatefrompng($path);
            imagecopy($dest, $src, $scroll_x, $scroll_y, $scroll_x_diff, $scroll_y_diff, $window_width, $window_height);
            imagedestroy($src);
            unlink($path);
        }
    }
    imagepng($dest, "{$screenshots_path}{$screenshot_filename}");
    imagedestroy($dest);
}

function take_screenshot($driver, $name) {
    $browser_name = $driver->getCapabilities()->getBrowserName();
    $screenshots_path = __DIR__.'/../../../screenshots/';
    $screenshot_filename = "{$name}-{$browser_name}.png";
    $driver->takeScreenshot("{$screenshots_path}{$screenshot_filename}");
}

function adjust_css_for_pageshot($driver) {
    $adjust_for_pageshot = file_get_contents(__DIR__.'/adjust_for_pageshot.css');
    $css_string = json_encode($adjust_for_pageshot);
    $js_code = "document.head.innerHTML += '<style>'+{$css_string}+'</style>';";
    $driver->executeScript($js_code);
}

function hide_flaky_elements($driver) {
    $hide_flaky_code = file_get_contents(__DIR__.'/hideFlaky.js');
    $driver->executeScript($hide_flaky_code);
}
