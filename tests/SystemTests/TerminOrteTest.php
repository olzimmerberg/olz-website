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
final class TerminOrteTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testTerminOrteReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('termin_locations');

        $browser->get($this->getDetailUrl());
        $this->screenshot('termin_locations_detail');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminOrteCreate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-termin-location-button');
        $this->waitForModal('#edit-termin-location-modal');
        $this->sendKeys('#edit-termin-location-modal #name-input', 'Der Austragungsort');
        $this->sendKeys('#edit-termin-location-modal #details-input', '...ist perfekt!');
        $this->sendKeys('#edit-termin-location-modal #latitude-input', '46.83479');
        $this->sendKeys('#edit-termin-location-modal #longitude-input', '9.21555');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($image_path);
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
        $browser->get("{$this->getUrl()}/5");
        $this->screenshot('termin_locations_new_finished');

        $this->resetDb();
        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminOrteDetailDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getDetailUrl());

        $this->click('#edit-termin-location-button');
        $this->waitForModal('#edit-termin-location-modal');
        $this->click('#edit-termin-location-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-termin-location-modal');

        $this->assertSame(404, $this->getHeaders($this->getDetailUrl())['http_code']);

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/termine/orte";
    }

    protected function getDetailUrl(): string {
        return "{$this->getTargetUrl()}/termine/orte/3";
    }
}
