<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class LoginLogoutTest extends SystemTestCase {
    public function testLoginLogoutScreenshots(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());

        $this->click('#account-menu-link');
        $this->click('#login-menu-item');
        $browser->wait()->until(function () {
            return $this->findBrowserElement('#login-modal')->getCssValue('opacity') == 1;
        });
        usleep(100 * 1000);

        $this->screenshot('login_modal');

        $this->sendKeys('#login-modal #usernameOrEmail-input', 'admin');
        $this->sendKeys('#login-modal #password-input', 'adm1n');
        $this->click('#login-modal #submit-button');
        sleep(1);

        $browser->get($this->getUrl());

        $this->click('#account-menu-link');
        $this->screenshot('logout_account_menu');

        $this->click('#logout-menu-item');
        usleep(100 * 1000);

        $browser->get($this->getUrl());

        $this->click('#account-menu-link');
        $this->screenshot('login_account_menu');
        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }
}
