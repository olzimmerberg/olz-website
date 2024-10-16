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
final class TerminOrteTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testTerminOrteScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doTerminOrteReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminOrteScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doTerminOrteReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doTerminOrteReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('termin_locations');

        $browser->get("{$this->getUrl()}/3");
        $this->screenshot('termin_locations_detail');
    }

    protected function doTerminOrteReadWrite(RemoteWebDriver $browser): void {
        $this->doTerminOrteReadOnly($browser);

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-termin-location-button');
        $this->waitForModal('#edit-termin-location-modal');
        $this->sendKeys('#edit-termin-location-modal #name-input', 'Der Austragungsort');
        $this->sendKeys('#edit-termin-location-modal #details-input', '...ist perfekt!');
        $this->sendKeys('#edit-termin-location-modal #latitude-input', '46.83479');
        $this->sendKeys('#edit-termin-location-modal #longitude-input', '9.21555');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-termin-location-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-location-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $this->screenshot('termin_locations_new_edit');

        $this->click('#edit-termin-location-modal #submit-button');
        $this->waitUntilGone('#edit-termin-location-modal');
        $browser->get("{$this->getUrl()}/4");
        $this->screenshot('termin_locations_new_finished');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/termine/orte";
    }
}
