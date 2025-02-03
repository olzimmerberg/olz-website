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
final class TermineTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testTermineReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('termine');

        $this->click('#filter-type-training');
        if ($this::isInModes('prod')) {
            $this->click('#filter-date-'.date('Y')); // prod does not have fake data from 2020...
        } else {
            $this->click('#filter-date-2020');
        }
        $this->screenshot('termine_past');

        $browser->get($this->getDetailUrl());
        $this->screenshot('termine_detail');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTermineCreate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-termin-button');
        $this->waitForModal('#edit-termin-modal');
        $this->waitABit(); // Wait for TerminLabels
        $this->sendKeys('#edit-termin-modal #startTime-input', '14:00');
        $this->sendKeys('#edit-termin-modal #endDate-input', '2020-08-15');
        $this->sendKeys('#edit-termin-modal #endTime-input', '18:00');
        $this->sendKeys('#edit-termin-modal #title-input', 'Der Event');
        $this->sendKeys('#edit-termin-modal #text-input', "...wird episch!\n\n[Infos](https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=6822)");
        $this->sendKeys('#edit-termin-modal #deadline-input', '2020-08-01 23:59:59');
        $this->click('#edit-termin-modal #types-programm-input');
        $this->click('#edit-termin-modal #types-ol-input');
        $this->click('#edit-termin-modal #locationId-field button[data-bs-toggle="dropdown"]');
        $this->click('#edit-termin-modal #locationId-field #entity-index-1');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-termin-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        $this->sendKeys('#edit-termin-modal #files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-modal #files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });

        $this->click('#edit-termin-modal #hasNewsletter-input');

        $this->screenshot('termine_new_edit');

        $this->click('#edit-termin-modal #submit-button');
        $this->waitUntilGone('#edit-termin-modal');
        $browser->get("{$this->getUrl()}/1002");
        $this->screenshot('termine_new_finished');

        $this->resetDb();
        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTermineCreateFromTemplate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-termin-button');
        $this->waitForModal('#edit-termin-modal');
        $this->waitABit(); // Wait for TerminLabels
        $this->click('#edit-termin-modal .template-chooser #dropdownMenuButton');
        $this->click('#edit-termin-modal .template-chooser #entity-index-1');

        // Wait for data to be populated
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-termin-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 2;
        });

        $this->click('#edit-termin-modal #shouldPromote-input');

        $this->screenshot('termine_from_template_new_edit');

        $this->click('#edit-termin-modal #submit-button');
        $this->waitUntilGone('#edit-termin-modal');

        $browser->get("{$this->getUrl()}/1002");
        $this->assertSame('Kartentraining: <<< TODO >>>', $this->getBrowserElement('.olz-termin-detail h1')->getText());

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTermineCreateSolv(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->click('#filter-archive-mit');
        $this->click('#filter-date-2015');
        $this->assertNotNull($this->getBrowserElement('.olz-termine-list-middle .no-entries'));

        $this->click('#create-termin-button');
        $this->waitForModal('#edit-termin-modal');
        $this->waitABit(); // Wait for TerminLabels
        $this->click('#edit-termin-modal #solvId-field #dropdownMenuButton');
        $this->click('#edit-termin-modal #solvId-field #entity-index-1');
        $this->click('#edit-termin-modal #types-programm-input');
        $this->click('#edit-termin-modal #types-ol-input');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-termin-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $this->click('#edit-termin-modal #shouldPromote-input');

        $this->screenshot('termine_solv_new_edit');

        $this->click('#edit-termin-modal #submit-button');
        $this->waitUntilGone('#edit-termin-modal');

        $browser->get($this->getUrl());
        $this->click('#filter-archive-mit');
        $this->click('#filter-date-2015');
        $this->assertCount(2, $this->getBrowserElements('.olz-termine-list-middle .olz-termine-list-item'));

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTermineDetailDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getDetailUrl());

        $this->click('#edit-termin-button');
        $this->waitForModal('#edit-termin-modal');
        $this->click('#edit-termin-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-termin-modal');

        $this->assertSame(404, $this->getHeaders($this->getDetailUrl())['http_code']);

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/termine";
    }

    protected function getDetailUrl(): string {
        return "{$this->getTargetUrl()}/termine/7";
    }
}
