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
final class FragenUndAntwortenTest extends SystemTestCase {
    public function testFragenUndAntwortenScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doFragenUndAntwortenReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testFragenUndAntwortenScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
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
