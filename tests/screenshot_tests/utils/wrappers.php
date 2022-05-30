<?php

namespace Facebook\WebDriver;

function click($element) {
    $element->getLocationOnScreenOnceScrolledIntoView();
    usleep(100 * 1000);
    $element->click();
}

function sendKeys($element, $string) {
    $element->getLocationOnScreenOnceScrolledIntoView();
    usleep(100 * 1000);
    $element->sendKeys($string);
}
