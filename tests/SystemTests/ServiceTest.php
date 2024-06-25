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
final class ServiceTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testServiceScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doServiceReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testServiceScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doServiceReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doServiceReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('service');
    }

    protected function doServiceReadWrite(RemoteWebDriver $browser): void {
        $this->doServiceReadOnly($browser);

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->screenshot('service_authenticated');
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service";
    }
}
