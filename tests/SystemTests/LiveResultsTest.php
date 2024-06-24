<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Tests\SystemTests\Common\SystemTestCase;
use Olz\Utils\AbstractDateUtils;

/**
 * @internal
 *
 * @coversNothing
 */
final class LiveResultsTest extends SystemTestCase {
    public function testLiveResultsScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doLiveResultsReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testLiveResultsScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doLiveResultsReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doLiveResultsReadOnly(RemoteWebDriver $browser): void {
        $date_utils = AbstractDateUtils::fromEnv();
        $live_file_content = json_encode([
            'last_updated_at' => $date_utils->getIsoNow(),
            'file' => 'results.xml',
        ]);
        file_put_contents($this->getLiveFilePath(), $live_file_content);

        $browser->get($this->getUrl());
        $this->screenshot('live_results_link');

        unlink($this->getLiveFilePath());
    }

    protected function doLiveResultsReadWrite(RemoteWebDriver $browser): void {
        $this->doLiveResultsReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }

    protected function getLiveFilePath(): string {
        return __DIR__.'/../../public/results/_live.json';
    }
}
