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
final class SucheTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testSucheScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doSucheReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testSucheScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doSucheReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doSucheReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('suche');
    }

    protected function doSucheReadWrite(RemoteWebDriver $browser): void {
        $this->doSucheReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/suche?anfrage=neujahr";
    }
}
