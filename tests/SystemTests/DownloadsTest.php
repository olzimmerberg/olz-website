<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DownloadsTest extends SystemTestCase {
    public function testDownloadsScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doDownloadsReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testDownloadsScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doDownloadsReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doDownloadsReadOnly(RemoteWebDriver $browser): void {
        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-download-button');
        $this->sendKeys('#edit-download-modal #name-input', 'Neues Jahresprogramm');
        $this->sendKeys('#edit-download-modal #position-input', '0');
        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        $this->sendKeys('#edit-download-modal #file-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-download-modal #file-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });

        $this->screenshot('downloads_new_edit');
    }

    protected function doDownloadsReadWrite(RemoteWebDriver $browser): void {
        $this->doDownloadsReadOnly($browser);

        $this->click('#submit-button');
        sleep(4);
        $this->screenshot('downloads_new_finished');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service";
    }
}
