<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Apps\OlzApps;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AppsTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testAppsScreenshots(): void {
        $this->login('admin', 'adm1n');
        $this->loadUrl($this->getUrl());
        $this->screenshot('apps_admin');
        $this->logout();

        $this->login('vorstand', 'v0r57and');
        $this->loadUrl($this->getUrl());
        $this->screenshot('apps_vorstand');
        $this->logout();

        $this->login('karten', 'kar73n');
        $this->loadUrl($this->getUrl());
        $this->screenshot('apps_karten');
        $this->logout();

        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getUrl());
        $this->screenshot('apps_benutzer');
        $this->logout();

        $this->loadUrl($this->getUrl());
        $this->screenshot('apps_anonym');

        $this->login('admin', 'adm1n');
        $apps = OlzApps::getApps();
        foreach ($apps as $app) {
            $app_href = "/{$app->getHref()}";
            $app_basename = $app->getBasename();
            $this->loadUrl("{$this->getTargetUrl()}{$app_href}");
            $this->screenshot("app_{$app_basename}");
        }
        $this->logout();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service/";
    }
}
