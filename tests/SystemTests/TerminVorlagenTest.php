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
final class TerminVorlagenTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminVorlagenScreenshots(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');

        $browser->get($this->getUrl());
        $this->screenshot('termin_templates');

        $browser->get("{$this->getUrl()}/2");
        $this->screenshot('termin_templates_detail');

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-termin-template-button');
        $this->waitForModal('#edit-termin-template-modal');
        $this->sendKeys('#edit-termin-template-modal #startTime-input', '14:00');
        $this->sendKeys('#edit-termin-template-modal #durationSeconds-input', '7200');
        $this->sendKeys('#edit-termin-template-modal #title-input', 'Die Event-Vorlage');
        $this->sendKeys('#edit-termin-template-modal #text-input', "...wird jedes Mal episch!\n\n[immer dasselbe](https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=6822)");
        $this->sendKeys('#edit-termin-template-modal #deadlineEarlierSeconds-input', '259200');
        $this->sendKeys('#edit-termin-template-modal #deadlineTime-input', '23:59:59');
        $this->click('#edit-termin-template-modal #types-programm-input');
        $this->click('#edit-termin-template-modal #types-ol-input');
        $this->click('#edit-termin-template-modal #locationId-field button[data-bs-toggle="dropdown"]');
        $this->click('#edit-termin-template-modal #locationId-field #entity-index-1');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-termin-template-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-template-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        $this->sendKeys('#edit-termin-template-modal #files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-template-modal #files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });

        $this->click('#edit-termin-template-modal #hasNewsletter-input');

        $this->screenshot('termin_templates_new_edit');

        $this->click('#edit-termin-template-modal #submit-button');
        $this->waitUntilGone('#edit-termin-template-modal');

        $browser->get("{$this->getUrl()}/7");
        $this->screenshot('termin_templates_new_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/termine/vorlagen";
    }
}
