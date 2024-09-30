<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ProfilTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testProfilScreenshots(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->screenshot('profil_admin');

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

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/benutzer/ich";
    }
}
