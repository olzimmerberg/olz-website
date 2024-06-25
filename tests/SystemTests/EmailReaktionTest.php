<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;
use Olz\Utils\EmailUtils;

/**
 * @internal
 *
 * @coversNothing
 */
final class EmailReaktionTest extends SystemTestCase {
    public function testEmailReaktionScreenshots(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('email_reaktion_no_token');
        $email_utils = EmailUtils::fromEnv();
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
}
