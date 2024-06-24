<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Apps\OlzApps;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AppsTest extends SystemTestCase {
    public function testAppsScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doAppsReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testAppsScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doAppsReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doAppsReadOnly(RemoteWebDriver $browser): void {
        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->screenshot('apps_admin');
        $this->logout();

        $this->login('vorstand', 'v0r57and');
        $browser->get($this->getUrl());
        $this->screenshot('apps_vorstand');
        $this->logout();

        $this->login('karten', 'kar73n');
        $browser->get($this->getUrl());
        $this->screenshot('apps_karten');
        $this->logout();

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());
        $this->screenshot('apps_benutzer');
        $this->logout();

        $browser->get($this->getUrl());
        $this->screenshot('apps_anonym');

        $this->login('admin', 'adm1n');
        $apps = OlzApps::getApps();
        foreach ($apps as $app) {
            $app_href = "/{$app->getHref()}";
            $app_basename = $app->getBasename();
            $browser->get("{$this->getTargetUrl()}{$app_href}");
            $this->screenshot("app_{$app_basename}");
        }
        $this->logout();
    }

    protected function doAppsReadWrite(RemoteWebDriver $browser): void {
        $this->doAppsReadOnly($browser);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service/";
    }
}
