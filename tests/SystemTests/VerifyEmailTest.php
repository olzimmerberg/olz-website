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
final class VerifyEmailTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw'])]
    public function testVerifyEmailScreenshots(): void {
        $browser = $this->getBrowser();

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());

        $this->click('#verify-user-email-link');
        usleep(100 * 1000); // Wait a bit
        $this->screenshot('profil_verify_email');
        $this->click('#verify-user-email-modal #submit-button');
        $this->waitUntilGone('#verify-user-email-modal');

        $data_path = $this->envUtils()->getDataPath();
        $last_email_file = "{$data_path}last_email.txt";
        $this->assertFileExists($last_email_file);
        $email_text = file_get_contents($last_email_file);
        $this->assertMatchesRegularExpression(
            "/To\\: \"Be Nutzer\" \\<nutzer\\@staging\\.olzimmerberg\\.ch\\>/",
            $email_text,
        );
        $this->assertMatchesRegularExpression(
            "/Subject\\: \\[OLZ\\] E\\-Mail bestätigen/",
            $email_text,
        );
        $res = preg_match('/(\/email_reaktion\?token\=\S+)\n/', $email_text, $matches);
        $this->assertSame(1, $res);
        $link = "{$this->getTargetUrl()}{$matches[1]}";

        $browser->get($link);
        $this->click('#execute-reaction-button');
        usleep(100 * 1000); // Wait until executed

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());
        $this->assertNull($this->getBrowserElement('#verify-user-email-link'));

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/benutzer/ich";
    }
}
