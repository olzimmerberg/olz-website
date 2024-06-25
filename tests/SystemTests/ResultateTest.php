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
final class ResultateTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testResultateScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doResultateReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testResultateScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doResultateReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doResultateReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        sleep(1);

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
