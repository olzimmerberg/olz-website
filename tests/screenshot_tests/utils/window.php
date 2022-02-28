<?php

namespace Facebook\WebDriver;

function set_window_size($driver, $width, $height) {
    $size = $driver->manage()->window()->getSize();
    $width_diff = $size->getWidth() - get_window_width($driver);
    $height_diff = $size->getHeight() - get_window_height($driver);
    $size_to_set = new WebDriverDimension(
        $width + $width_diff,
        $height + $height_diff,
    );
    $driver->manage()->window()->setSize($size_to_set);
}

function get_window_width($driver) {
    return $driver->executeScript('return window.innerWidth', []);
}

function get_window_height($driver) {
    return $driver->executeScript('return window.innerHeight', []);
}

function window_scroll_to($driver, $x, $y) {
    $driver->executeScript("window.scrollTo({top:{$y},left:{$x},behavior:'instant'})", []);
}

function get_window_scroll_x($driver) {
    return $driver->executeScript('return window.scrollX', []);
}

function get_window_scroll_y($driver) {
    return $driver->executeScript('return window.scrollY', []);
}

function get_body_width($driver) {
    return $driver->executeScript('return document.body.offsetWidth', []);
}

function get_body_height($driver) {
    return $driver->executeScript('return document.body.offsetHeight', []);
}
