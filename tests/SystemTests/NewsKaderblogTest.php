<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class NewsKaderblogTest extends SystemTestCase {
    public function testNewsKaderblogScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doNewsKaderblogReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doNewsKaderblogReadWrite(RemoteWebDriver $browser): void {
        $this->login('kaderlaeufer', 'kad3rla3uf3r');
        $browser->get($this->getUrl());

        $this->click('#create-news-button');
        sleep(1);
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #dropdownMenuButton');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #role-index-0');
        $format_select = new WebDriverSelect($this->findBrowserElement('#edit-news-modal #format-input'));
        $format_select->selectByVisibleText('Kaderblog');
        $this->sendKeys('#edit-news-modal #title-input', 'Das Training');
        $this->sendKeys('#edit-news-modal #content-input', "<BILD1>Detailierte Schilderung des Trainings.\n<DATEI1 text='Artikel als PDF'>");

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-news-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        $this->sendKeys('#edit-news-modal #files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-news-modal #files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });

        $this->screenshot('news_new_kaderblog_edit');

        $this->click('#edit-news-modal #submit-button');
        sleep(4);
        $this->screenshot('news_new_kaderblog_finished');

        $browser->get("{$this->getUrl()}/10");

        $this->click('#edit-news-button');
        sleep(1);
        $this->sendKeys('#edit-news-modal #content-input', "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
        $this->screenshot('news_update_kaderblog_edit');

        $this->click('#edit-news-modal #submit-button');
        sleep(4);
        $this->screenshot('news_update_kaderblog_finished');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }
}
