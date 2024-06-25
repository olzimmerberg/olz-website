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
final class FuerEinsteigerTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testFuerEinsteigerScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doFuerEinsteigerReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testFuerEinsteigerScreenshotReadWriteLegacy(): void {
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
