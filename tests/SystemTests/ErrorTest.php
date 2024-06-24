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
final class ErrorTest extends SystemTestCase {
    public function testErrorScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doErrorReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testErrorScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doErrorReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doErrorReadOnly(RemoteWebDriver $browser): void {
        $browser->get("{$this->getUrl()}/400");
        $this->screenshot('error_400');

        $browser->get("{$this->getUrl()}/401");
        $this->screenshot('error_401');

        $browser->get("{$this->getUrl()}/403");
        $this->screenshot('error_403');

        $browser->get("{$this->getUrl()}/404");
        $this->screenshot('error_404');

        $browser->get("{$this->getUrl()}/500");
        $this->screenshot('error_500');

        $browser->get("{$this->getUrl()}/529");
        $this->screenshot('error_529');
    }

    protected function doErrorReadWrite(RemoteWebDriver $browser): void {
        $this->doErrorReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/error";
    }
}
