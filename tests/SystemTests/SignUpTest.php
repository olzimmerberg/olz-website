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
    public function testSignUpScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doSignUpReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testSignUpScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doSignUpReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doSignUpReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());

        $this->click('#account-menu-link');
        $this->click('#login-menu-item');
        $browser->wait()->until(function () {
            return $this->findBrowserElement('#login-modal')->getCssValue('opacity') == 1;
        });
        usleep(100 * 1000);
        $this->click('#sign-up-link');

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
        $this->click('#edit-user-modal #recaptcha-consent-given-input');
        sleep(random_int(2, 3));
        usleep(random_int(0, 999999));
        $this->click('#edit-user-modal #cookie-consent-given-input');
        sleep(random_int(0, 1));
        usleep(random_int(0, 999999));
        $this->click('#edit-user-modal #submit-button');
        sleep(1);
        $this->screenshot('sign_up_submitted');

        $browser->get("{$this->getTargetUrl()}/apps/files/webdav/");
        $this->screenshot('sign_up_new_webdav');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }
}
