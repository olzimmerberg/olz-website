<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Tests\SystemTests\Common\SystemTestCase;
use Olz\Utils\EmailUtils;

/**
 * @internal
 *
 * @coversNothing
 */
final class EmailReaktionTest extends SystemTestCase {
    public function testEmailReaktionScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doEmailReaktionReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testEmailReaktionScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doEmailReaktionReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doEmailReaktionReadOnly(RemoteWebDriver $browser): void {
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
    }

    protected function doEmailReaktionReadWrite(RemoteWebDriver $browser): void {
        $this->doEmailReaktionReadOnly($browser);
        // TODO: Actually click reaction, check modification in DB, reset
        // $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/email_reaktion";
    }
}
