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
final class LinksTest extends SystemTestCase {
    public function testLinksScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doLinksReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testLinksScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doLinksReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doLinksReadOnly(RemoteWebDriver $browser): void {
        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-link-button');
        $this->sendKeys('#edit-link-modal #name-input', 'OLZ');
        $this->sendKeys('#edit-link-modal #position-input', '0');
        $this->sendKeys('#edit-link-modal #url-input', 'https://olzimmerberg.ch');

        $this->screenshot('links_new_edit');
    }

    protected function doLinksReadWrite(RemoteWebDriver $browser): void {
        $this->doLinksReadOnly($browser);

        $this->click('#submit-button');
        sleep(4);
        $this->screenshot('links_new_finished');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service";
    }
}
