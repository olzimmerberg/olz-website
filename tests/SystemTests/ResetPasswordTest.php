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
final class ResetPasswordTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw'])]
    public function testResetPasswordScreenshots(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());

        $this->click('#account-menu-link');
        $this->click('#login-menu-item');
        $this->waitForModal('#login-modal');
        $this->click('#login-modal #reset-password-link');
        $this->waitForModal('#reset-password-modal');
        $this->sendKeys('#reset-password-modal #usernameOrEmail-input', 'benutzer');
        $this->click('#reset-password-modal #recaptcha-consent-given-input');
        $this->screenshot('profil_password_reset');
        $this->click('#reset-password-modal #submit-button');
        $this->waitUntilGone('#reset-password-modal');

        $data_path = $this->envUtils()->getDataPath();
        $last_email_file = "{$data_path}last_email.txt";
        $this->assertFileExists($last_email_file);
        $email_text = file_get_contents($last_email_file);
        $this->assertMatchesRegularExpression(
            "/To\\: \"Be Nutzer\" \\<nutzer\\@staging\\.olzimmerberg\\.ch\\>/",
            $email_text,
        );
        $this->assertMatchesRegularExpression(
            "/Subject\\: \\[OLZ\\] Passwort zurücksetzen/",
            $email_text,
        );
        $res = preg_match('/(\/email_reaktion\?token\=\S+)\n/', $email_text, $matches);
        $this->assertSame(1, $res);
        $link = "{$this->getTargetUrl()}{$matches[1]}";
        $res = preg_match('/\n`(\S{8})`\n/', $email_text, $matches);
        $this->assertSame(1, $res);
        $new_password = "{$matches[1]}";

        $browser->get($link);
        $this->click('#execute-reaction-button');
        $this->waitABit(); // Wait until executed

        $this->login('benutzer', 'b3nu723r');
        $browser->get("{$this->getTargetUrl()}/benutzer/ich");
        $this->assertNull($this->getBrowserElement('h1.name-container'));

        $this->login('benutzer', $new_password);
        $browser->get("{$this->getTargetUrl()}/benutzer/ich");
        $this->assertNotNull($this->getBrowserElement('h1.name-container'));

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }
}
