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
final class TermineTest extends SystemTestCase {
    public function testTermineScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doTermineReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testTermineScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doTermineReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doTermineReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('termine');

        $this->click('#filter-type-training');
        $this->click('#filter-date-2020');
        $this->screenshot('termine_past');

        $browser->get("{$this->getUrl()}/7");
        $this->screenshot('termine_detail');
    }

    protected function doTermineReadWrite(RemoteWebDriver $browser): void {
        $this->doTermineReadOnly($browser);

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-termin-button');
        sleep(1);
        $this->sendKeys('#startTime-input', '14:00');
        $this->sendKeys('#endDate-input', '2020-08-15');
        $this->sendKeys('#endTime-input', '18:00');
        $this->sendKeys('#title-input', 'Der Event');
        $this->sendKeys('#text-input', "...wird episch!\n\n[Infos](https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=6822)");
        $this->sendKeys('#deadline-input', '2020-08-01 23:59:59');
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

        $this->screenshot('termine_new_edit');

        $this->click('#submit-button');
        sleep(4);
        $browser->get("{$this->getUrl()}/1002");
        $this->screenshot('termine_new_finished');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/termine";
    }
}
