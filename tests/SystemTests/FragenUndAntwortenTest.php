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
final class FragenUndAntwortenTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testFragenUndAntwortenScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doFragenUndAntwortenReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testFragenUndAntwortenScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doFragenUndAntwortenReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doFragenUndAntwortenReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('fragen_und_antworten');
    }

    protected function doFragenUndAntwortenReadWrite(RemoteWebDriver $browser): void {
        $this->doFragenUndAntwortenReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/fragen_und_antworten";
    }
}
