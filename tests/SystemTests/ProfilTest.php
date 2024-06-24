<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ProfilTest extends SystemTestCase {
    public function testProfilScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doProfilReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testProfilScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doProfilReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doProfilReadOnly(RemoteWebDriver $browser): void {
        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->screenshot('profil_admin');
    }

    protected function doProfilReadWrite(RemoteWebDriver $browser): void {
        $this->doProfilReadOnly($browser);

        $this->click('#change-password-button');
        $browser->wait()->until(function () {
            return $this->findBrowserElement('#change-password-modal')->getCssValue('opacity') == 1;
        });
        $this->sendKeys('#change-password-modal #oldPassword-input', 'kurz');
        $this->sendKeys('#change-password-modal #newPassword-input', 'zukurz');
        $this->sendKeys('#change-password-modal #newPasswordRepeat-input', 'anders');
        $this->click('#change-password-modal #submit-button');
        $this->screenshot('change_password_admin');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/profil";
    }
}
