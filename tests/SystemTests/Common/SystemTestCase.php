<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests\Common;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SystemTestCase extends TestCase {
    private static $browser;

    protected function getBrowser($name = 'firefox') {
        $host = "http://localhost:4444/";
        if ($name == 'firefox') {
            $capabilities = DesiredCapabilities::firefox();
        } elseif ($name == 'chrome') {
            $capabilities = DesiredCapabilities::chrome();
        } else {
            throw new \Exception("Invalid browser: {$name}");
        }
        $this::$browser = RemoteWebDriver::create($host, $capabilities);
        return $this::$browser;
    }

    protected function findBrowserElement($css_selector) {
        return $this::$browser->findElement(
            WebDriverBy::cssSelector($css_selector)
        );
    }

    protected function getHeaders($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);
        return $header;
    }

    public static function tearDownAfterClass(): void {
        if (self::$browser !== null) {
            self::$browser->quit();
            shell_exec('killall geckodriver');
            shell_exec('killall chromedriver');
        }
    }
}
