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
final class SitemapTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testSitemapScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doSitemapReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testSitemapScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doSitemapReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doSitemapReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('sitemap');
    }

    protected function doSitemapReadWrite(RemoteWebDriver $browser): void {
        $this->doSitemapReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/sitemap";
    }
}
