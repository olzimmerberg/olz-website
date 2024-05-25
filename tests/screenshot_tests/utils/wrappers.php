<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebElement;

function click(RemoteWebElement $element): void {
    $element->getLocationOnScreenOnceScrolledIntoView();
    usleep(100 * 1000);
    $element->click();
}

function sendKeys(RemoteWebElement $element, string $string): void {
    $element->getLocationOnScreenOnceScrolledIntoView();
    usleep(100 * 1000);
    $element->sendKeys($string);
}
