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
final class ServiceTest extends SystemTestCase {
    public function testServiceScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doServiceReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testServiceScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
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
