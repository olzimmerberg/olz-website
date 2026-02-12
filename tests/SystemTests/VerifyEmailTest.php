<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;
use Olz\Utils\EnvUtils;

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
        $this->waitForModal('#verify-user-email-modal');
        $this->screenshot('profil_verify_email');
        $this->click('#verify-user-email-modal #submit-button');
        $this->waitUntilGone('#verify-user-email-modal');

        $dev_env_data_path = __DIR__.'/../../public/';
        $last_email_file = "{$dev_env_data_path}last_email.txt";
        $this->assertFileExists($last_email_file);
        $email_text = file_get_contents($last_email_file) ?: '';
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
        $this->waitABit(); // Wait until executed

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());
        $this->assertNull($this->getBrowserElement('#verify-user-email-link'));

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/benutzer/ich";
    }

    protected function getEnvUtils(): EnvUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EnvUtils::class);
    }
}
