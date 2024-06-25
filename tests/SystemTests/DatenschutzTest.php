<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DatenschutzTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testDatenschutzScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doDatenschutzReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testDatenschutzScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doDatenschutzReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doDatenschutzReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('datenschutz');
    }

    protected function doDatenschutzReadWrite(RemoteWebDriver $browser): void {
        $this->doDatenschutzReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/datenschutz";
    }
}
