<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DownloadsTest extends SystemTestCase {
    public function testDownloadsScreenshots(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();

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

        $this->click('#submit-button');
        sleep(4);
        $this->screenshot('downloads_new_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service";
    }
}
