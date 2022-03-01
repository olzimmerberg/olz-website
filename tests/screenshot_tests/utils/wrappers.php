<?php

namespace Facebook\WebDriver;

function click($element) {
    $element->getLocationOnScreenOnceScrolledIntoView();
    sleep(0.5);
    $element->click();
}

function sendKeys($element, $string) {
    $element->getLocationOnScreenOnceScrolledIntoView();
    $element->sendKeys($string);
}
