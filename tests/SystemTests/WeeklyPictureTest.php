<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class WeeklyPictureTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testWeeklyPictureScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doWeeklyPictureReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testWeeklyPictureScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doWeeklyPictureReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doWeeklyPictureReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->click('#weekly-picture-carousel .active a[href*="/img/weekly_picture/"]');
        $browser->wait()->until(function () {
            return $this->findBrowserElement('.lg-container.lg-show img[src*="/img/weekly_picture/"]')->getCssValue('opacity') == 1;
        });
        $this->screenshot('startseite_weekly_picture');
    }

    protected function doWeeklyPictureReadWrite(RemoteWebDriver $browser): void {
        $this->doWeeklyPictureReadOnly($browser);

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-weekly-picture-button');
        $this->sendKeys('#edit-weekly-picture-modal #text-input', 'Neues Bild der Woche');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-weekly-picture-modal #image-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-weekly-picture-modal #image-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $this->screenshot('weekly_picture_new_edit');

        $this->click('#edit-weekly-picture-modal #submit-button');
        sleep(1);
        $this->screenshot('weekly_picture_new_finished');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }
}
