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
final class AppFilesTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testAppFilesScreenshots(): void {
        $this->login('admin', 'adm1n');
        $this->loadUrl($this->getUrl());
        $this->screenshot('app_files_admin');
        $this->logout();

        $this->login('vorstand', 'v0r57and');
        $this->loadUrl($this->getUrl());
        $this->screenshot('app_files_vorstand');
        $this->logout();

        $this->login('karten', 'kar73n');
        $this->loadUrl($this->getUrl());
        $this->screenshot('app_files_karten');
        $this->logout();

        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getUrl());
        $this->screenshot('app_files_benutzer');
        $this->logout();

        $this->loadUrl($this->getUrl());
        $this->screenshot('app_files_anonym');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/files/";
    }
}
