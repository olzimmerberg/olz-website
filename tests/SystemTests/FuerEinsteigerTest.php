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
final class FuerEinsteigerTest extends SystemTestCase {
    public function testFuerEinsteigerScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doFuerEinsteigerReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testFuerEinsteigerScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doFuerEinsteigerReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doFuerEinsteigerReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('fuer_einsteiger');
    }

    protected function doFuerEinsteigerReadWrite(RemoteWebDriver $browser): void {
        $this->doFuerEinsteigerReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/fuer_einsteiger";
    }
}
