<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests\Common;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Olz\Utils\WithUtilsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SystemTestCase extends TestCase {
    use WithUtilsTrait;

    private static string $browser_name = 'firefox';
    private static ?RemoteWebDriver $browser = null;
    private static int $max_timeout_seconds = 1;

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

    protected function getBrowser(): RemoteWebDriver {
        if ($this::$browser !== null) {
            return $this::$browser;
        }
        $host = "http://localhost:4444/";
        if (self::$browser_name == 'firefox') {
            $capabilities = DesiredCapabilities::firefox();
        } elseif (self::$browser_name == 'chrome') {
            $capabilities = DesiredCapabilities::chrome();
        } else {
            $browser_name = self::$browser_name;
            throw new \Exception("Invalid browser: {$browser_name}");
        }
        $this::$browser = RemoteWebDriver::create($host, $capabilities);
        $this->setWindowInnerSize(1280, 1024);
        return $this::$browser;
    }

    private function setWindowInnerSize(int $width, int $height): void {
        $size = $this::$browser->manage()->window()->getSize();
        $inner_width = intval($this::$browser->executeScript("return window.innerWidth", []));
        $inner_height = intval($this::$browser->executeScript("return window.innerHeight", []));
        $size_to_set = new WebDriverDimension(
            $size->getWidth() + $width - $inner_width,
            $size->getHeight() + $height - $inner_height,
        );
        $this::$browser->manage()->window()->setSize($size_to_set);
    }

    /**
     * @return array<RemoteWebElement>
     */
    protected function getBrowserElements(string $css_selector): array {
        return $this::$browser->findElements(
            WebDriverBy::cssSelector($css_selector)
        );
    }

    protected function getBrowserElement(string $css_selector): ?RemoteWebElement {
        try {
            return $this->findBrowserElement($css_selector);
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function findBrowserElement(string $css_selector): RemoteWebElement {
        $this::$browser
            ->wait($this::$max_timeout_seconds)
            ->until(function () use ($css_selector) {
                return $this::$browser->findElement(
                    WebDriverBy::cssSelector($css_selector)
                );
            })
        ;
        return $this::$browser->findElement(
            WebDriverBy::cssSelector($css_selector)
        );
    }

    protected function click(string $css_selector): void {
        try {
            $this->doClick($css_selector);
        } catch (\Throwable $th) {
            $this->waitABit();
            $this->doClick($css_selector);
        }
    }

    protected function doClick(string $css_selector): void {
        $element = $this->findBrowserElement($css_selector);
        $element->getLocationOnScreenOnceScrolledIntoView();
        $this->waitABit();
        $element->click();
    }

    protected function clear(string $css_selector): void {
        try {
            $this->doClear($css_selector);
        } catch (\Throwable $th) {
            $this->waitABit();
            $this->doClear($css_selector);
        }
    }

    protected function doClear(string $css_selector): void {
        $element = $this->findBrowserElement($css_selector);
        $element->getLocationOnScreenOnceScrolledIntoView();
        $this->waitABit();
        $element->clear();
    }

    protected function sendKeys(string $css_selector, string $string): void {
        try {
            $this->doSendKeys($css_selector, $string);
        } catch (\Throwable $th) {
            $this->waitABit();
            $this->doSendKeys($css_selector, $string);
        }
    }

    protected function doSendKeys(string $css_selector, string $string): void {
        $element = $this->findBrowserElement($css_selector);
        $element->getLocationOnScreenOnceScrolledIntoView();
        $this->waitABit();
        $element->sendKeys($string);
    }

    protected function waitABit(): void {
        usleep(100 * 1000);
    }

    protected function waitForModal(string $css_selector): void {
        $this::$browser->wait()->until(function () use ($css_selector) {
            return $this->findBrowserElement($css_selector)->getCssValue('opacity') == 1;
        });
    }

    protected function waitUntilGone(string $css_selector): void {
        $this::$browser->wait()->until(function () use ($css_selector) {
            $elements = $this::$browser->findElements(
                WebDriverBy::cssSelector($css_selector)
            );
            return count($elements) == 0;
        });
    }

    protected function waitFor(string $css_selector): void {
        $this::$browser->wait()->until(function () use ($css_selector) {
            $elements = $this::$browser->findElements(
                WebDriverBy::cssSelector($css_selector)
            );
            return count($elements) > 0;
        });
    }

    /** @return array<string, mixed> */
    protected function getHeaders(string $url): array {
        assert($url);
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
        $test_class_name = get_called_class();
        $test_name = "{$test_class_name}::{$this->getName()}";
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
        if ($this::$browser !== null && $is_not_prod) {
            $this->logout();
        }
    }

    protected function tearDown(): void {
        parent::tearDown();
        $test_class_name = get_called_class();
        $test_name = "{$test_class_name}::{$this->getName()}";
        if (!$this->isSkipped) {
            $this::tock($test_name, $test_name);
        }
    }

    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        self::tick('total');
        self::setUpSlices();
    }

    public static function tearDownAfterClass(): void {
        parent::tearDownAfterClass();
        SystemTestCase::tock('total', 'total');
        if (!self::$shutdownFunctionRegistered) {
            register_shutdown_function(function () {
                if (self::$browser !== null) {
                    self::$browser->quit();
                }
                echo self::getPrettyTimingReport();
                self::persistTimingReport();
            });
            self::$shutdownFunctionRegistered = true;
        }
    }

    public function onNotSuccessfulTest(\Throwable $t): void {
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
        $this::$browser->get("{$this->getTargetUrl()}{$this::$login_api_url}{$get_params}");
        $this->tock('login', 'login');
    }

    public function logout(): void {
        $this->tick('logout');
        $this::$browser->get("{$this->getTargetUrl()}{$this::$logout_api_url}");
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
        $this->waitFor('body');
        $this->tick('screenshot');
        $this->adjustCssForScreenshot();
        $browser_name = $this::$browser->getCapabilities()->getBrowserName();
        $screenshots_path = __DIR__.'/../../../screenshots/';
        $screenshot_filename = "{$name}-{$browser_name}.png";
        $window_width = $this->getWindowWidth();
        $window_height = $this->getWindowHeight();
        $body_width = max($this->getBodyWidth(), $window_width);
        $body_height = max($this->getBodyHeight(), $window_height);
        $num_x = ceil($body_width / $window_width);
        $num_y = ceil($body_height / $window_height);
        $dest = imagecreatetruecolor(max(1, $body_width), max(1, $body_height));
        for ($x = 0; $x < $num_x; $x++) {
            for ($y = 0; $y < $num_y; $y++) {
                $scroll_x = $x * $window_width;
                $scroll_y = $y * $window_height;
                $this->scrollTo($scroll_x, $scroll_y);
                $path = "{$screenshots_path}{$x}-{$y}-{$screenshot_filename}";
                $this->hideFlakyElements();
                $this::$browser->takeScreenshot($path);
                $scroll_x_diff = $scroll_x - $this->getScrollX();
                $scroll_y_diff = $scroll_y - $this->getScrollY();
                $src = imagecreatefrompng($path);
                assert((bool) $src);
                imagecopy($dest, $src, $scroll_x, $scroll_y, $scroll_x_diff, $scroll_y_diff, $window_width, $window_height);
                imagedestroy($src);
                unlink($path);
            }
        }
        imagepng($dest, "{$screenshots_path}{$screenshot_filename}");
        imagedestroy($dest);
        $this->tock('screenshot', 'screenshot');
    }

    protected function adjustCssForScreenshot(): void {
        $adjust_for_pageshot = file_get_contents(__DIR__.'/adjust_for_pageshot.css');
        $css_string = json_encode($adjust_for_pageshot);
        $js_code = "document.head.innerHTML += '<style>'+{$css_string}+'</style>';";
        $this::$browser->executeScript($js_code);
    }

    protected function hideFlakyElements(): void {
        $hide_flaky_code = file_get_contents(__DIR__.'/hideFlaky.js') ?: '';
        $this::$browser->executeScript($hide_flaky_code);
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

    // Utils

    protected static string $modal = "[...document.querySelectorAll('.modal')].filter(i => i.style.display === 'block')[0]";

    protected function getWindowWidth(): int {
        return intval($this::$browser->executeScript("return window.innerWidth", []));
    }

    protected function getWindowHeight(): int {
        return intval($this::$browser->executeScript("return window.innerHeight", []));
    }

    protected function getBodyWidth(): int {
        return intval($this::$browser->executeScript("return ({$this::$modal}?.children[0]?.offsetWidth ?? document.body.offsetWidth)", []));
    }

    protected function getBodyHeight(): int {
        return intval($this::$browser->executeScript("return ({$this::$modal}?.children[0]?.offsetHeight ?? document.body.offsetHeight)", []));
    }

    public function getScrollX(): int {
        return intval($this::$browser->executeScript("return ({$this::$modal}?.scrollLeft ?? window.scrollX)", []));
    }

    public function getScrollY(): int {
        return intval($this::$browser->executeScript("return ({$this::$modal}?.scrollTop ?? window.scrollY)", []));
    }

    protected function scrollTo(int $x, int $y): void {
        $this::$browser->executeScript("({$this::$modal} ?? window).scrollTo({top:{$y},left:{$x},behavior:'instant'})", []);
    }
}
