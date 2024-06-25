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
final class MaterialTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testMaterialScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doMaterialReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testMaterialScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doMaterialReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doMaterialReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('material');
    }

    protected function doMaterialReadWrite(RemoteWebDriver $browser): void {
        $this->doMaterialReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/material";
    }
}
