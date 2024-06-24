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
final class KontoPasswortTest extends SystemTestCase {
    public function testKontoPasswortScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doKontoPasswortReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testKontoPasswortScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doKontoPasswortReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doKontoPasswortReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());

        $this->sendKeys('#profile-first-name-input', 'Integration T.');
        $this->sendKeys('#profile-last-name-input', 'User');
        $this->click('#profile-username-input');
        $this->sendKeys('#profile-password-input', 'zukurz');
        $this->sendKeys('#profile-password-repeat-input', 'anders');
        $this->sendKeys('#profile-email-input', 'konto-passwort-test');
        $this->sendKeys('#profile-gender-input', 'm');
        $this->sendKeys('#profile-birthdate-input', '30.2.1999');
        $this->sendKeys('#profile-street-input', 'Zimmerbergstrasse 270');
        $this->sendKeys('#profile-postal-code-input', '8800');
        $this->sendKeys('#profile-city-input', 'Thalwil');
        $this->click('#sign-up-with-password-submit-button');
        $this->screenshot('konto_passwort_errors');

        $hide_tooltips_script = <<<'ZZZZZZZZZZ'
            [...document.querySelectorAll('.tooltip')].map(elem => {
                elem.style.display = 'none';
            });
            ZZZZZZZZZZ;
        $browser->executeScript($hide_tooltips_script);
    }

    protected function doKontoPasswortReadWrite(RemoteWebDriver $browser): void {
        $this->doKontoPasswortReadOnly($browser);

        $this->clear('#profile-password-input');
        $this->sendKeys('#profile-password-input', 'genügend&gleich');
        $this->clear('#profile-password-repeat-input');
        $this->sendKeys('#profile-password-repeat-input', 'genügend&gleich');
        $this->sendKeys('#profile-email-input', '@staging.olzimmerberg.ch');
        $this->clear('#profile-birthdate-input');
        $this->sendKeys('#profile-birthdate-input', '13.1.2006');
        $this->click('input[name="recaptcha-consent-given"]');
        sleep(random_int(2, 3));
        usleep(random_int(0, 999999));
        $this->click('input[name="cookie-consent-given"]');
        sleep(random_int(1, 2));
        usleep(random_int(0, 999999));
        $this->click('#sign-up-with-password-submit-button');
        sleep(1);
        $this->screenshot('konto_passwort_submitted');
        sleep(1);

        $browser->get("{$this->getTargetUrl()}/apps/files/webdav/");
        $this->screenshot('konto_passwort_new_webdav');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/konto_passwort";
    }
}
