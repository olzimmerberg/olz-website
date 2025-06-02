<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DownloadsTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testDownloadsCreate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->assertSame([
            'Statuten',
            'Spesenreglement',
        ], array_map(
            fn ($elem) => $elem->getText(),
            $this->getBrowserElements('.olz-downloads li')
        ));

        $this->click('#create-download-button');
        $this->waitForModal('#edit-download-modal');
        $this->sendKeys('#edit-download-modal #name-input', 'Neues Jahresprogramm');
        $this->selectOption('#edit-download-modal #position-field #before-after-input', 'vor');
        $this->selectOption('#edit-download-modal #position-field .olz-entity-chooser', 'Statuten');
        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        assert($document_path);
        $this->sendKeys('#edit-download-modal #file-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-download-modal #file-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });

        $this->screenshot('downloads_new_edit');

        $this->click('#edit-download-modal #submit-button');
        $this->waitUntilGone('#edit-download-modal');
        $browser->get($this->getUrl());
        $this->assertSame([
            'Neues Jahresprogramm',
            'Statuten',
            'Spesenreglement',
        ], array_map(
            fn ($elem) => $elem->getText(),
            $this->getBrowserElements('.olz-downloads li')
        ));

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testDownloadsDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#edit-download-1-button');
        $this->waitForModal('#edit-download-modal');
        $this->click('#edit-download-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-download-modal');

        $browser->get($this->getUrl());
        $this->assertNull($this->getBrowserElement('#edit-download-1-button'));

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service";
    }
}
