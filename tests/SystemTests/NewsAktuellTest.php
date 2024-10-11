<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class NewsAktuellTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsAktuellCreateScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();

        $this->login('vorstand', 'v0r57and');
        $browser->get($this->getUrl());

        $this->click('#create-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #dropdownMenuButton');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #role-index-0');
        $format_select = new WebDriverSelect($this->findBrowserElement('#edit-news-modal #format-input'));
        $format_select->selectByVisibleText('Aktuell');
        $this->sendKeys('#edit-news-modal #title-input', 'Das Geschehnis');
        $this->sendKeys('#edit-news-modal #teaser-input', 'Kleiner Teaser für den Artikel.');
        $this->sendKeys('#edit-news-modal #content-input', "<BILD1>Detailierte Schilderung des Geschehnisses.\n<DATEI1 text='Artikel als PDF'>");

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

        $this->screenshot('news_new_aktuell_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_new_aktuell_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsAktuellUpdateScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get("{$this->getUrl()}/5");

        $this->click('#edit-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->sendKeys('#edit-news-modal #content-input', "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
        $this->screenshot('news_update_aktuell_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_update_aktuell_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }
}
