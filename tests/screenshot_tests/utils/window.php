<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

function set_window_size(RemoteWebDriver $driver, float|int $width, float|int $height): void {
    $size = $driver->manage()->window()->getSize();
    $width_diff = $size->getWidth() - get_window_width($driver);
    $height_diff = $size->getHeight() - get_window_height($driver);
    $size_to_set = new WebDriverDimension(
        $width + $width_diff,
        $height + $height_diff,
    );
    $driver->manage()->window()->setSize($size_to_set);
}

$modal = "[...document.querySelectorAll('.modal')].filter(i => i.style.display === 'block')[0]";
$window_or_modal = "";

function get_window_width(RemoteWebDriver $driver): float|int {
    global $modal;
    return $driver->executeScript("return window.innerWidth", []);
}

function get_window_height(RemoteWebDriver $driver): float|int {
    global $modal;
    return $driver->executeScript("return window.innerHeight", []);
}

function window_scroll_to(RemoteWebDriver $driver, float|int $x, float|int $y): void {
    global $modal;
    $driver->executeScript("({$modal} ?? window).scrollTo({top:{$y},left:{$x},behavior:'instant'})", []);
}

function get_window_scroll_x(RemoteWebDriver $driver): float|int {
    global $modal;
    return $driver->executeScript("return ({$modal}?.scrollLeft ?? window.scrollX)", []);
}

function get_window_scroll_y(RemoteWebDriver $driver): float|int {
    global $modal;
    return $driver->executeScript("return ({$modal}?.scrollTop ?? window.scrollY)", []);
}

function get_body_width(RemoteWebDriver $driver): float|int {
    global $modal;
    return $driver->executeScript("return ({$modal}?.children[0]?.offsetWidth ?? document.body.offsetWidth)", []);
}

function get_body_height(RemoteWebDriver $driver): float|int {
    global $modal;
    return $driver->executeScript("return ({$modal}?.children[0]?.offsetHeight ?? document.body.offsetHeight)", []);
}
