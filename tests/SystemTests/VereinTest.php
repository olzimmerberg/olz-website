<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class VereinTest extends SystemTestCase {
    public function testVereinScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doVereinReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testVereinScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doVereinReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doVereinReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('verein');

        $browser->get("{$this->getUrl()}/praesi");
        $this->screenshot('verein_praesi');
    }

    protected function doVereinReadWrite(RemoteWebDriver $browser): void {
        $this->doVereinReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/verein";
    }
}
