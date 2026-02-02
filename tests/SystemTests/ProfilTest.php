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
        $this->waitForModal('#change-password-modal');
        $this->sendKeys('#change-password-modal #oldPassword-input', 'kurz');
        $this->sendKeys('#change-password-modal #newPassword-input', 'zukurz');
        $this->sendKeys('#change-password-modal #newPasswordRepeat-input', 'anders');
        $this->click('#change-password-modal #submit-button');
        $this->screenshot('change_password_admin');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testProfilUpdateUser(): void {
        $browser = $this->getBrowser();

        $this->login('karten', 'kar73n');
        $browser->get($this->getUrl());
        $this->assertSame('Benutzername: karten', $this->getBrowserElement('.info-container.username')?->getText());
        $this->assertSame("(Keine Adresse)\n(Keine PLZ) (Kein Ort) (Keine Region, Kein Land)", $this->getBrowserElement('.info-container.address')?->getText());
        $this->assertSame('Geburtsdatum: (Unbekannt)', $this->getBrowserElement('.info-container.birthdate')?->getText());
        $this->assertSame('Telephon: (Unbekannt)', $this->getBrowserElement('.info-container.phone')?->getText());
        $this->assertSame('E-Mail', $this->getBrowserElement('.info-container.email')?->getText());

        $this->click('#edit-user-button');
        $this->waitForModal('#edit-user-modal');
        $this->sendKeys('#edit-user-modal #firstName-input', 'Integration T.');
        $this->sendKeys('#edit-user-modal #lastName-input', 'User');
        $this->click('#edit-user-modal #username-input');
        $this->clear('#edit-user-modal #email-input');
        $this->sendKeys('#edit-user-modal #email-input', 'update-test@staging.olzimmerberg.ch');
        $this->sendKeys('#edit-user-modal #gender-input', 'm');
        $this->sendKeys('#edit-user-modal #birthdate-input', '12.3.1999');
        $this->sendKeys('#edit-user-modal #street-input', 'Zimmerbergstrasse 270');
        $this->sendKeys('#edit-user-modal #postalCode-input', '8800');
        $this->sendKeys('#edit-user-modal #city-input', 'Thalwil');
        $this->sendKeys('#edit-user-modal #region-input', 'ZH');
        $this->sendKeys('#edit-user-modal #countryCode-input', 'CH');
        $this->click('#edit-user-modal #submit-button');

        $browser->get($this->getUrl());
        $this->assertSame('Benutzername: karten', $this->getBrowserElement('.info-container.username')?->getText());
        $this->assertSame("Zimmerbergstrasse 270\n8800 Thalwil (ZH, CH)", $this->getBrowserElement('.info-container.address')?->getText());
        $this->assertSame('Geburtsdatum: 12.03.1999', $this->getBrowserElement('.info-container.birthdate')?->getText());
        $this->assertSame('Telephon: (Unbekannt)', $this->getBrowserElement('.info-container.phone')?->getText());
        $this->assertSame('E-Mail', $this->getBrowserElement('.info-container.email')?->getText());

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testProfilDeleteUser(): void {
        $browser = $this->getBrowser();

        $this->assertSame(200, $this->getHeaders("{$this->getTargetUrl()}/news/7")['http_code']);
        $browser->get("{$this->getTargetUrl()}/verein");
        $this->assertStringContainsString('Volker Vorstand', $this->getBrowserElement('#organigramm')?->getText() ?? '');

        $this->login('vorstand', 'v0r57and');
        $browser->get($this->getUrl());

        $this->click('#edit-user-button');
        $this->waitForModal('#edit-user-modal');
        $this->click('#edit-user-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-user-modal');

        $this->assertSame(404, $this->getHeaders($this->getUrl())['http_code']);
        $this->assertSame(404, $this->getHeaders("{$this->getTargetUrl()}/news/7")['http_code']);
        $browser->get("{$this->getTargetUrl()}/verein");
        $this->assertStringNotContainsString('Volker Vorstand', $this->getBrowserElement('#organigramm')?->getText() ?? '');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/benutzer/ich";
    }
}
