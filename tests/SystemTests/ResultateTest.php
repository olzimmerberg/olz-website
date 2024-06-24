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
final class ResultateTest extends SystemTestCase {
    public function testResultateScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doResultateReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testResultateScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doResultateReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doResultateReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());

        $this->click('input#chk-0');
        $this->click('input#chk-1');

        $this->screenshot('resultate');
    }

    protected function doResultateReadWrite(RemoteWebDriver $browser): void {
        $this->doResultateReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/resultate/?file=results.xml#/class0";
    }
}
