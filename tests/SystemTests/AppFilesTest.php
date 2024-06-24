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
final class AppFilesTest extends SystemTestCase {
    public function testAppFilesScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doAppFilesReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testAppFilesScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doAppFilesReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doAppFilesReadOnly(RemoteWebDriver $browser): void {
        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->screenshot('app_files_admin');
        $this->logout();

        $this->login('vorstand', 'v0r57and');
        $browser->get($this->getUrl());
        $this->screenshot('app_files_vorstand');
        $this->logout();

        $this->login('karten', 'kar73n');
        $browser->get($this->getUrl());
        $this->screenshot('app_files_karten');
        $this->logout();

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());
        $this->screenshot('app_files_benutzer');
        $this->logout();

        $browser->get($this->getUrl());
        $this->screenshot('app_files_anonym');
    }

    protected function doAppFilesReadWrite(RemoteWebDriver $browser): void {
        $this->doAppFilesReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/files/";
    }
}
