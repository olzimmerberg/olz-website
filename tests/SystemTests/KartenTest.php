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
final class KartenTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testKartenScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doKartenReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testKartenScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doKartenReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doKartenReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('karten');

        $browser->get("{$this->getUrl()}/1");
        $this->screenshot('karten_detail');
    }

    protected function doKartenReadWrite(RemoteWebDriver $browser): void {
        $this->doKartenReadOnly($browser);

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-karte-button');
        $this->sendKeys('#name-input', 'Die Karte');
        $this->sendKeys('#latitude-input', '46.83474');
        $this->sendKeys('#longitude-input', '9.21544');
        $this->sendKeys('#year-input', '2020');
        $this->sendKeys('#scale-input', '1:15\'000');
        $this->click('#isKindScool-input');
        $this->sendKeys('#place-input', 'Wuut');
        $this->sendKeys('#zoom-input', '2');
        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $this->screenshot('karten_new_edit');

        $this->click('#submit-button');
        sleep(1);
        $this->screenshot('karten_new_finished');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/karten";
    }
}
