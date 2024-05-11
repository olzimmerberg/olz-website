<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests\Common;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SystemTestCase extends TestCase {
    private static ?RemoteWebDriver $browser = null;

    /** @var array<string, ?string> */
    private static array $targetUrlByMode = [
        'dev' => 'http://127.0.0.1:30270',
        'dev_rw' => 'http://127.0.0.1:30270',
        'staging' => 'https://staging.olzimmerberg.ch', // TODO: staging token
        'staging_rw' => 'https://staging.olzimmerberg.ch', // TODO: staging token
        'prod' => 'https://olzimmerberg.ch',
        'meta' => null,
    ];

    /** @var array<string> */
    protected static array $readOnlyModes = ['dev', 'dev_rw', 'staging', 'staging_rw', 'prod'];
    /** @var array<string> */
    protected static array $readWriteModes = ['dev_rw', 'staging_rw'];

    protected function getBrowser(string $name = 'firefox'): RemoteWebDriver {
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

    protected function findBrowserElement(string $css_selector): RemoteWebElement {
        return $this::$browser->findElement(
            WebDriverBy::cssSelector($css_selector)
        );
    }

    /** @return array<string, mixed> */
    protected function getHeaders(string $url): array {
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

    protected function getTargetUrl(): ?string {
        $mode = getenv('SYSTEM_TEST_MODE');
        return $this::$targetUrlByMode[$mode] ?? null;
    }

    /** @param string|array<string> $modes */
    protected function onlyRunInModes(string|array $modes): void {
        $modes_array = is_string($modes) ? [$modes] : $modes;
        $actual_mode = getenv('SYSTEM_TEST_MODE');
        $should_run = false;
        foreach ($modes_array as $mode) {
            if ($mode === $actual_mode) {
                $should_run = true;
            }
        }
        if (!$should_run) {
            $this->markTestSkipped("Shouldn't run in mode {$actual_mode}");
        }
    }

    public static function tearDownAfterClass(): void {
        if (self::$browser !== null) {
            self::$browser->quit();
            shell_exec('killall geckodriver');
            shell_exec('killall chromedriver');
        }
    }
}
