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
final class TerminVorlagenTest extends SystemTestCase {
    public function testTerminVorlagenScreenshots(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');

        $browser->get($this->getUrl());
        $this->screenshot('termin_templates');

        $browser->get("{$this->getUrl()}/2");
        $this->screenshot('termin_templates_detail');

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-termin-template-button');
        sleep(1);
        $this->sendKeys('#startTime-input', '14:00');
        $this->sendKeys('#durationSeconds-input', '7200');
        $this->sendKeys('#title-input', 'Die Event-Vorlage');
        $this->sendKeys('#text-input', "...wird jedes Mal episch!\n\n[immer dasselbe](https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=6822)");
        $this->sendKeys('#deadlineEarlierSeconds-input', '259200');
        $this->sendKeys('#deadlineTime-input', '23:59:59');
        $this->click('#types-programm-input');
        $this->click('#types-ol-input');
        $this->click('#locationId-field button[data-bs-toggle="dropdown"]');
        $this->click('#locationId-field #entity-index-1');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        $this->sendKeys('#files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });

        $this->click('#hasNewsletter-input');

        $this->screenshot('termin_templates_new_edit');

        $this->click('#submit-button');
        sleep(4);
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
