<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;
use Olz\Utils\EmailUtils;

/**
 * @internal
 *
 * @coversNothing
 */
final class EmailReaktionTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testEmailReaktionScreenshots(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('email_reaktion_no_token');
        $email_utils = $this->getEmailUtils();
        $token = $email_utils->encryptEmailReactionToken([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type' => 'monthly_preview',
        ]);
        $browser->get("{$this->getUrl()}?token={$token}");
        $this->screenshot('email_reaktion');
        // TODO: Actually click reaction, check modification in DB, reset
        // $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/email_reaktion";
    }

    protected function getEmailUtils(): EmailUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EmailUtils::class);
    }
}
