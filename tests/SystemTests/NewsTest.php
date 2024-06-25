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
final class NewsTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testNewsScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doNewsReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doNewsReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doNewsReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('news');
        $browser->get("{$this->getUrl()}/3");
        $this->screenshot('news_id_3');
    }

    protected function doNewsReadWrite(RemoteWebDriver $browser): void {
        $this->doNewsReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }
}
