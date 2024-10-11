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
final class LoginLogoutTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testLoginLogoutScreenshots(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());

        $this->click('#account-menu-link');
        $this->click('#login-menu-item');
        $this->waitForModal('#login-modal');

        $this->screenshot('login_modal');

        $this->sendKeys('#login-modal #usernameOrEmail-input', 'admin');
        $this->sendKeys('#login-modal #password-input', 'adm1n');
        $this->click('#login-modal #submit-button');
        $this->waitUntilGone('#login-modal');

        $browser->get($this->getUrl());

        $this->click('#account-menu-link');
        $this->screenshot('logout_account_menu');

        $this->click('#logout-menu-item');
        $this->waitABit();

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
