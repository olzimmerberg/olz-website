<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests\Common;

use Facebook\WebDriver\Exception\UnexpectedTagNameException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverSelect;
use Olz\Utils\GeneralUtils;
use Olz\Utils\WithUtilsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

/**
 * @internal
 *
 * @coversNothing
 */
class SystemTestCase extends KernelTestCase {
    use WithUtilsTrait;

    private static string $browser_name = 'firefox';
    private static ?Client $client = null;

    /** @var array<string, ?string> */
    private static array $targetUrlByMode = [
        'dev' => 'http://127.0.0.1:30270',
        'dev_rw' => 'http://127.0.0.1:30270',
        'staging' => 'https://staging.olzimmerberg.ch', // TODO: staging token
        'staging_rw' => 'https://staging.olzimmerberg.ch', // TODO: staging token
        'prod' => 'https://olzimmerberg.ch',
        'meta' => null,
    ];

    protected static bool $shutdownFunctionRegistered = false;

    protected bool $isSkipped = false;

    protected function generalUtils(): GeneralUtils {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(GeneralUtils::class);
    }

    protected function getClient(): Client {
        if (self::$client === null) {
            $base_uri = $this->getTargetUrl() ?? 'http://127.0.0.1:30270';
            if (self::$browser_name === 'firefox') {
                // TODO: Re-add the `general.useragent.override = OlzSystemTest/1.0` preference.
                // Panther's FirefoxManager does not expose Firefox preferences directly;
                // it would require passing a custom `moz:firefoxOptions` capability.
                self::$client = Client::createFirefoxClient(null, null, [], $base_uri);
            } elseif (self::$browser_name === 'chrome') {
                self::$client = Client::createChromeClient(null, ['--user-agent=OlzSystemTest/1.0'], [], $base_uri);
            } else {
                $browser_name = self::$browser_name;
                throw new \Exception("Invalid browser: {$browser_name}");
            }
            $this->setWindowInnerSize(1280, 1024);
        }
        $this->generalUtils()->checkNotNull(self::$client, "Client expected");
        return self::$client;
    }

    protected function getCrawler(): Crawler {
        return $this->getClient()->refreshCrawler();
    }

    protected function filter(string $selector): Crawler {
        return $this->getCrawler()->filter($selector);
    }

    private function setWindowInnerSize(int $width, int $height): void {
        $client = self::$client;
        $this->generalUtils()->checkNotNull($client, "Client expected");
        $size = $client->manage()->window()->getSize();
        $inner_width = intval($client->executeScript("return window.innerWidth", []));
        $inner_height = intval($client->executeScript("return window.innerHeight", []));
        $size_to_set = new WebDriverDimension(
            $size->getWidth() + $width - $inner_width,
            $size->getHeight() + $height - $inner_height,
        );
        $client->manage()->window()->setSize($size_to_set);
    }

    protected function loadUrl(string $url): void {
        $client = $this->getClient();
        $client->get($url);
        for ($i = 0; $i < 30; $i++) {
            if ($client->getTitle() === 'One moment, please...') {
                echo "Waiting one moment...\n";
                $client->wait(1);
            }
        }
    }

    protected function getTitle(): string {
        return $this->getClient()->getTitle();
    }

    protected function retry(callable $fn, int $num = 10): void {
        $last_th = null;
        for ($i = 0; $i < $num; $i++) {
            try {
                $fn();
                return;
            } catch (\Throwable $th) {
                $last_th = $th;
                $this->waitABit();
            }
        }
        throw $last_th ?? new \Exception("Failed after {$num} retries");
    }

    protected function click(string $css_selector): void {
        $this->retry(fn () => $this->doClick($css_selector));
    }

    protected function doClick(string $css_selector): void {
        $element = $this->filter($css_selector);
        $element->getLocationOnScreenOnceScrolledIntoView();
        $this->waitABit();
        $element->click();
        $this->waitABit();
    }

    protected function clear(string $css_selector): void {
        $this->retry(fn () => $this->doClear($css_selector));
    }

    protected function doClear(string $css_selector): void {
        // TODO: Migrate this to panther as well
        $browser = $this->getClient()->getWebDriver();
        $element = $browser->findElement(WebDriverBy::cssSelector($css_selector));
        $element->getLocationOnScreenOnceScrolledIntoView();
        $this->waitABit();
        $element->clear();
    }

    protected function sendKeys(string $css_selector, string $string): void {
        $this->retry(fn () => $this->doSendKeys($css_selector, $string));
    }

    protected function doSendKeys(string $css_selector, string $string): void {
        $element = $this->filter($css_selector);
        $element->getLocationOnScreenOnceScrolledIntoView();
        $this->waitABit();
        $element->sendKeys($string);
        $this->waitABit();
    }

    protected function selectOption(string $css_selector, string $option): void {
        $this->retry(fn () => $this->doSelectOption($css_selector, $option));
    }

    protected function doSelectOption(string $css_selector, string $option): void {
        $element = $this->filter($css_selector);
        $element->getLocationOnScreenOnceScrolledIntoView();
        $this->waitABit();
        try {
            $select = new WebDriverSelect($element);
            $select->selectByVisibleText($option);
        } catch (UnexpectedTagNameException $exc) {
            $this->waitUntil(function () use ($css_selector) {
                return $this->filter("{$css_selector} #dropdown-menu-button")->getText() !== 'Lädt...';
            });
            $this->click("{$css_selector} #dropdown-menu-button");
            $this->sendKeys("{$css_selector} #entity-search-input", $option);
            $this->waitUntil(function () use ($css_selector, $option) {
                return str_starts_with($this->filter("{$css_selector} #entity-index-0")->getText(), $option);
            });
            $this->click("{$css_selector} #entity-index-0");
            $this->waitUntil(function () use ($css_selector, $option) {
                return str_starts_with($this->filter("{$css_selector} #dropdown-menu-button")->getText(), $option);
            });
        }
    }

    protected function waitABit(): void {
        $this->tick('waitABit');
        usleep(100 * 1000);
        $this->tock('waitABit', 'waitABit');
    }

    /**
     * @param callable(): bool $is_finished
     */
    protected function waitUntil(callable $is_finished): void {
        $client = self::$client;
        $this->generalUtils()->checkNotNull($client, "Client expected");
        $this->tick('waitUntil');
        $client->wait()->until(function () use ($is_finished) {
            try {
                return $is_finished();
            } catch (\Throwable $th) {
                return false;
            }
        });
        $this->tock('waitUntil', 'waitUntil');
    }

    protected function waitForModal(string $css_selector): void {
        $this->tick('waitForModal');
        $this->waitUntil(function () use ($css_selector) {
            return $this->filter($css_selector)->getCSSValue('opacity') == 1;
        });
        $this->tock('waitForModal', 'waitForModal');
    }

    protected function waitUntilGone(string $css_selector): void {
        $client = self::$client;
        $this->generalUtils()->checkNotNull($client, "Client expected");
        $this->tick('waitUntilGone');
        $this->waitUntil(function () use ($css_selector, $client) {
            $elements = $client->findElements(
                WebDriverBy::cssSelector($css_selector)
            );
            return count($elements) === 0;
        });
        $this->tock('waitUntilGone', 'waitUntilGone');
    }

    protected function waitFor(string $css_selector): void {
        $client = self::$client;
        $this->generalUtils()->checkNotNull($client, "Client expected");
        $this->tick('waitFor');
        $this->waitUntil(function () use ($css_selector, $client) {
            $elements = $client->findElements(
                WebDriverBy::cssSelector($css_selector)
            );
            return count($elements) === 1;
        });
        $this->tock('waitFor', 'waitFor');
    }

    /** @return array<string, mixed> */
    protected function getHeaders(string $url): array {
        assert($url);
        $ch = $this->httpUtils()->curlInit($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $this->httpUtils()->curlExec($ch);
        return curl_getinfo($ch);
    }

    protected function getTargetUrl(): ?string {
        $mode = getenv('SYSTEM_TEST_MODE');
        return $this::$targetUrlByMode[$mode] ?? null;
    }

    /** @param string|array<string> $modes */
    protected static function isInModes(string|array $modes): bool {
        $modes_array = is_string($modes) ? [$modes] : $modes;
        $actual_mode = getenv('SYSTEM_TEST_MODE');
        foreach ($modes_array as $mode) {
            if ($mode === $actual_mode) {
                return true;
            }
        }
        return false;
    }

    protected static ?int $slice_index = null;
    protected static ?int $num_slices = null;
    /** @var array<string, int> */
    protected static ?array $slice_by_test = null;

    protected function setUp(): void {
        parent::setUp();
        self::bootKernel();
        $test_class_name = get_called_class();
        $test_name = "{$test_class_name}::{$this->name()}";
        $slice_index = SystemTestCase::$slice_by_test[$test_name] ?? null;
        if ($slice_index === null) {
            echo <<<ZZZZZZZZZZ


                #####################################################
                {$test_name} is missing in timing_report.json
                #####################################################

                ZZZZZZZZZZ;
            $this->isSkipped = false;
        } elseif ($slice_index === SystemTestCase::$slice_index) {
            $this->isSkipped = false;
        } else {
            $this->isSkipped = true;
            $this->markTestSkipped("Not in slice ({$slice_index})");
        }
        $this::tick($test_name);
        $is_not_prod = $this->isInModes(['dev', 'dev_rw', 'staging', 'staging_rw']);
        if (self::$client !== null && $is_not_prod) {
            $this->logout();
        }
    }

    protected function tearDown(): void {
        parent::tearDown();
        $test_class_name = get_called_class();
        $test_name = "{$test_class_name}::{$this->name()}";
        if (!$this->isSkipped) {
            $this::tock($test_name, $test_name);
        }
    }

    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        $browser = getenv('SYSTEM_TEST_BROWSER');
        if ($browser === 'firefox' || $browser === 'chrome') {
            self::$browser_name = $browser;
        }
        self::tick('total');
        self::setUpSlices();
    }

    public static function tearDownAfterClass(): void {
        parent::tearDownAfterClass();
        SystemTestCase::tock('total', 'total');
        if (!self::$shutdownFunctionRegistered) {
            register_shutdown_function(function () {
                if (self::$client !== null) {
                    self::$client->quit();
                }
                echo self::getPrettyTimingReport();
                self::persistTimingReport();
            });
            self::$shutdownFunctionRegistered = true;
        }
    }

    public function onNotSuccessfulTest(\Throwable $t): never {
        try {
            $this->screenshot('testing_error');
        } catch (\Throwable $th) {
            echo "\n  Could not get testing_error screenshot!\n";
        }
        throw $t;
    }

    // Auth

    protected static string $login_api_url = '/api/login';
    protected static string $logout_api_url = '/api/logout';

    public function login(string $username, string $password): void {
        $this->tick('login');
        $esc_request = json_encode([
            'usernameOrEmail' => $username,
            'password' => $password,
            'rememberMe' => false,
        ]);
        $get_params = "?request={$esc_request}";
        $this->getClient()->get("{$this->getTargetUrl()}{$this::$login_api_url}{$get_params}");
        $this->tock('login', 'login');
    }

    public function logout(): void {
        $this->tick('logout');
        $this->getClient()->get("{$this->getTargetUrl()}{$this::$logout_api_url}");
        $this->tock('logout', 'logout');
    }

    // Commands

    protected function runCommand(string $command, ?string $argv): string {
        $token = urlencode($this->getBotAccessToken());
        $request = ['command' => $command, 'argv' => $argv];
        $enc_request = urlencode(json_encode($request) ?: '');
        $result = file_get_contents("{$this->getTargetUrl()}/api/executeCommand?access_token={$token}&request={$enc_request}");
        if (!$result) {
            $actual_mode = getenv('SYSTEM_TEST_MODE');
            throw new \Exception("Command {$command}({$argv}) failed in mode {$actual_mode}");
        }
        return $result;
    }

    protected function getBotAccessToken(): string {
        $mode = getenv('SYSTEM_TEST_MODE');
        if ($mode === 'prod') {
            $token = getenv('BOT_ACCESS_TOKEN');
            if (!$token) {
                throw new \Exception("Prod access token not set (BOT_ACCESS_TOKEN)");
            }
            return $token;
        }
        return 'public_dev_data_access_token';
    }

    // Database

    protected function resetDb(): void {
        if (!$this->isInModes(['dev_rw', 'staging_rw'])) {
            $actual_mode = getenv('SYSTEM_TEST_MODE');
            throw new \Exception("Cannot call resetDb in mode: {$actual_mode}");
        }
        $this->tick('reset');
        for ($i = 0; $i < 100; $i++) {
            $result = file_get_contents("{$this->getTargetUrl()}/api/executeCommand?access_token=public_dev_data_access_token&request={\"command\":\"olz:db-reset\",\"argv\":\"content\"}") ?: '';
            $output = json_decode($result, true)['output'] ?? null;
            if (str_contains($output, "Database content reset successful.\n")) {
                $this->tock('reset', 'db_reset');
                return;
            }
            echo "DB content reset failed: {$output}\n";
            $this->waitABit();
        }
        throw new \Exception("Resetting dev data timed out");
    }

    // Screenshot

    public function screenshot(string $name): void {
        $client = self::$client;
        $this->generalUtils()->checkNotNull($client, "Client expected");
        $this->waitFor('body');
        $this->tick('screenshot');
        $browser_name = self::$browser_name;
        $screenshots_path = __DIR__.'/../../../screenshots/generated/';

        // Remove content that is not deterministic between two runs of the same
        // code, so that screenshots only differ on actual changes.
        $capture_script = <<<'ZZZZZZZZZZ'
            for (const element of document.querySelectorAll('[data-lg-id]')) {
                element.removeAttribute('data-lg-id');
            }
            for (const element of document.querySelectorAll('.test-flaky')) {
                const parentElement = element.parentElement;
                parentElement.removeChild(element);
            }
            return document.documentElement.outerHTML;
            ZZZZZZZZZZ;
        $html = $client->executeScript($capture_script);
        if (!is_string($html) || $html === '') {
            echo "\n  Could not capture HTML screenshot {$name}!\n";
            return;
        }
        $html = preg_replace('/(\?|\&)modified\=[0-9\-\_]+/', '?modified=TIMESTAMP', $html) ?? $html;
        $html = preg_replace('/(\/\*\s*test\-flaky\(\s*\*\/).*(\/\*\s*\)test\-flaky\s*\*\/)/', '$1$2', $html) ?? $html;
        $html = preg_replace('/(\<\!\-\-\s*test\-flaky\(\s*\-\-\>).*(\<\!\-\-\s*\)test\-flaky\s*\-\-\>)/', '$1$2', $html) ?? $html;
        if (!is_dir($screenshots_path)) {
            mkdir($screenshots_path, 0o777, true);
        }
        $html = "<!DOCTYPE html>\n{$html}";
        file_put_contents("{$screenshots_path}{$name}-{$browser_name}.html", $html);

        $this->tock('screenshot', 'screenshot');
    }

    // Timing

    /** @var array<string, float> */
    protected static array $timing_timestamps = [];
    /** @var array<string, float> */
    protected static array $timing_report = [];
    protected static string $timing_report_filename = __DIR__.'/timing_report.json';

    protected static function resetTiming(): void {
        SystemTestCase::$timing_timestamps = [];
        SystemTestCase::$timing_report = [];
    }

    protected static function tick(string $name): void {
        $now = floatval(microtime(true));
        SystemTestCase::$timing_timestamps[$name] = $now;
    }

    protected static function tock(string $name, string $report): void {
        $now = microtime(true);
        $existing_report = SystemTestCase::$timing_report[$report] ?? 0.0;
        $existing_timestamp = SystemTestCase::$timing_timestamps[$name] ?? $now;
        SystemTestCase::$timing_report[$report] = $existing_report + ($now - $existing_timestamp);
    }

    protected static function getPrettyTimingReport(): string {
        $max_name_strlen = 0;
        $max_time_intval_strlen = 0;
        foreach (SystemTestCase::$timing_report as $name => $time) {
            $name_strlen = strlen($name);
            if ($name_strlen > $max_name_strlen) {
                $max_name_strlen = $name_strlen;
            }
            $time_intval_strlen = strlen(strval(intval($time)));
            if ($time_intval_strlen > $max_time_intval_strlen) {
                $max_time_intval_strlen = $time_intval_strlen;
            }
        }
        $out = "\nTiming report\n\n";
        $total_time = SystemTestCase::$timing_report['total'];
        foreach (SystemTestCase::$timing_report as $name => $time) {
            $pad_name = str_pad($name, $max_name_strlen, ' ', STR_PAD_LEFT);
            $pad_time = str_pad(number_format($time, 3, '.', '').' s', $max_time_intval_strlen + 6, ' ', STR_PAD_LEFT);
            $pad_percent = str_pad(number_format($time * 100 / $total_time, 1, '.', '').' %', 7, ' ', STR_PAD_LEFT);
            $out .= "{$pad_name} | {$pad_time} | {$pad_percent}\n";
        }
        $out .= "\n";
        return $out;
    }

    /** @return array<string, float> */
    protected static function getPersistedTimingReport(): array {
        $json_content = file_get_contents(self::$timing_report_filename);
        if (!$json_content) {
            return [];
        }
        $report = json_decode($json_content, true);
        return $report ? $report : [];
    }

    protected static function persistTimingReport(): void {
        $report = self::getPersistedTimingReport();
        foreach (SystemTestCase::$timing_report as $name => $time) {
            $previous_time = $report[$name] ?? $time;
            $report[$name] = round(($time + $previous_time) / 2, 1);
        }
        $sorted_report = [];
        $keys = array_keys($report);
        sort($keys);
        foreach ($keys as $key) {
            if (preg_match('/^(Olz\\\Tests.*)::(.*)$/', $key, $matches)) {
                try {
                    new \ReflectionMethod($matches[1], $matches[2]);
                    $sorted_report[$key] = $report[$key];
                } catch (\ReflectionException $exc) {
                    // Don't keep the record for an inexistent method
                }
            } else {
                // Keep the custom record
                $sorted_report[$key] = $report[$key];
            }
        }
        file_put_contents(
            self::$timing_report_filename,
            json_encode($sorted_report, JSON_PRETTY_PRINT)
        );
    }

    protected static function setUpSlices(): void {
        if (SystemTestCase::$slice_by_test !== null) {
            return;
        }
        $actual_slice_config = getenv('SYSTEM_TEST_SLICE') ?: '';
        $res = preg_match('/^([0-9]+)\/([0-9]+)$/', $actual_slice_config, $matches);
        if (!$res) {
            throw new \Exception("Invalid slice: {$actual_slice_config}");
        }
        SystemTestCase::$slice_index = intval($matches[1]) - 1;
        SystemTestCase::$num_slices = intval($matches[2]);
        if (
            SystemTestCase::$slice_index < 0
            || SystemTestCase::$slice_index >= SystemTestCase::$num_slices
        ) {
            throw new \Exception("Invalid slice: {$actual_slice_config}");
        }
        SystemTestCase::$slice_by_test = self::getSliceByTest(SystemTestCase::$num_slices);
    }

    /** @return array<string, int> */
    protected static function getSliceByTest(int $num_slices): array {
        $report = self::getPersistedTimingReport();
        $relevant_reports = [];
        foreach ($report as $name => $time) {
            if (preg_match('/^(Olz\\\Tests.*)::(.*)$/', $name, $matches)) {
                try {
                    $method = new \ReflectionMethod($matches[1], $matches[2]);
                    /** @var ?OnlyInModes */
                    $only_in_modes = null;
                    foreach ($method->getAttributes() as $attribute) {
                        if ($attribute->getName() === OnlyInModes::class) {
                            $only_in_modes = $attribute->newInstance();
                        }
                    }
                    // @phpstan-ignore-next-line
                    $mode_ok = $only_in_modes === null || self::isInModes($only_in_modes->modes);
                    $relevant_reports[] = [
                        'name' => $name,
                        'time' => $time,
                        'mode_ok' => $mode_ok,
                    ];
                } catch (\ReflectionException $exc) {
                    // ignore
                }
            }
        }
        usort($relevant_reports, function ($a, $b) {
            return $a['time'] < $b['time'] ? 1 : -1;
        });
        $slice_by_test = [];
        $num_relevant_reports = count($relevant_reports);
        for ($i = 0; $i < $num_relevant_reports; $i++) {
            $name = $relevant_reports[$i]['name'];
            $mode_ok = $relevant_reports[$i]['mode_ok'];
            $slice_by_test[$name] = $mode_ok ? $i % $num_slices : -1;
        }
        return $slice_by_test;
    }
}
