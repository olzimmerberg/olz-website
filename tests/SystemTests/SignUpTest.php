<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class SignUpTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testSignUpReadOnly(): void {
        $browser = $this->getBrowser();
        $this->doSignUpReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testSignUp(): void {
        $browser = $this->getBrowser();
        $this->doSignUpReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doSignUpReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());

        $this->click('#account-menu-link');
        $this->click('#login-menu-item');
        $this->waitForModal('#login-modal');
        $this->waitABit();
        $this->click('#sign-up-link');

        $this->waitForModal('#edit-user-modal');
        $this->sendKeys('#edit-user-modal #firstName-input', 'Integration T.');
        $this->sendKeys('#edit-user-modal #lastName-input', 'User');
        $this->click('#edit-user-modal #username-input');
        $this->sendKeys('#edit-user-modal #password-input', 'zukurz');
        $this->sendKeys('#edit-user-modal #passwordRepeat-input', 'anders');
        $this->sendKeys('#edit-user-modal #email-input', 'konto-passwort-test');
        $this->sendKeys('#edit-user-modal #gender-input', 'm');
        $this->sendKeys('#edit-user-modal #birthdate-input', '30.2.1999');
        $this->sendKeys('#edit-user-modal #street-input', 'Zimmerbergstrasse 270');
        $this->sendKeys('#edit-user-modal #postalCode-input', '8800');
        $this->sendKeys('#edit-user-modal #city-input', 'Thalwil');
        $this->sendKeys('#edit-user-modal #region-input', 'ZH');
        $this->sendKeys('#edit-user-modal #countryCode-input', 'CH');
        $this->sendKeys('#edit-user-modal #siCardNumber-input', 'nan');
        $this->sendKeys('#edit-user-modal #solvNumber-input', '123TEI');
        $this->sendKeys('#edit-user-modal #ahvNumber-input', '123.1234.1234.12');
        $this->selectOption('#edit-user-modal #dressSize-input', 'L');
        $this->click('#edit-user-modal #submit-button');
        $this->screenshot('sign_up_errors');

        $hide_tooltips_script = <<<'ZZZZZZZZZZ'
            [...document.querySelectorAll('.tooltip')].map(elem => {
                elem.style.display = 'none';
            });
            ZZZZZZZZZZ;
        $browser->executeScript($hide_tooltips_script);
    }

    protected function doSignUpReadWrite(RemoteWebDriver $browser): void {
        $this->doSignUpReadOnly($browser);

        $this->clear('#edit-user-modal #password-input');
        $this->sendKeys('#edit-user-modal #password-input', 'genügend&gleich');
        $this->clear('#edit-user-modal #passwordRepeat-input');
        $this->sendKeys('#edit-user-modal #passwordRepeat-input', 'genügend&gleich');
        $this->sendKeys('#edit-user-modal #email-input', '@staging.olzimmerberg.ch');
        $this->clear('#edit-user-modal #birthdate-input');
        $this->sendKeys('#edit-user-modal #birthdate-input', '13.1.2006');
        $this->clear('#edit-user-modal #siCardNumber-input');
        $this->sendKeys('#edit-user-modal #siCardNumber-input', '8123456');
        $this->clear('#edit-user-modal #ahvNumber-input');
        $this->sendKeys('#edit-user-modal #ahvNumber-input', '756.1234.1234.12');
        $this->click('#edit-user-modal #captcha-dev');
        $this->click('#edit-user-modal #submit-button');
        $this->waitUntilGone('#edit-user-modal');
        $this->screenshot('sign_up_submitted');

        $browser->get("{$this->getTargetUrl()}/apps/files/webdav/");
        $this->screenshot('sign_up_new_webdav');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }
}
