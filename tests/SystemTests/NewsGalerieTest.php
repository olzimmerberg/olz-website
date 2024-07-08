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
final class NewsGalerieTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsGalerieCreateScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());

        $this->click('#create-news-button');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #dropdownMenuButton');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #user-index-0');
        $format_select = new WebDriverSelect($this->findBrowserElement('#edit-news-modal #format-input'));
        $format_select->selectByVisibleText('Galerie');
        $this->sendKeys('#edit-news-modal #title-input', 'Das Fotoshooting');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-news-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $this->screenshot('news_new_galerie_edit');

        $this->click('#edit-news-modal #submit-button');
        sleep(1);
        $this->screenshot('news_new_galerie_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsGalerieUpdateScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get("{$this->getUrl()}/5");

        $this->click('#edit-news-button');
        $this->sendKeys('#edit-news-modal #title-input', "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
        $this->screenshot('news_update_galerie_edit');

        $this->click('#edit-news-modal #submit-button');
        sleep(1);
        $this->screenshot('news_update_galerie_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }
}
