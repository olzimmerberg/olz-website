<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps;

use Olz\Apps\OlzApps;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\OlzApps
 */
final class OlzAppsTest extends UnitTestCase {
    public const BASENAMES = [
        'Anmelden',
        'Files',
        'GoogleSearch',
        'Logs',
        'Monitoring',
        'Newsletter',
        'Oev',
        'Quiz',
        'Results',
        'Statistics',
        'Youtube',
    ];

    public function testGetAppPaths(): void {
        $app_paths = OlzApps::getAppPaths();
        $this->assertSame(array_map(function ($basename) {
            return realpath(__DIR__."/../../../src/Apps/{$basename}");
        }, self::BASENAMES), $app_paths);
    }

    public function testAppsSameAsAppPaths(): void {
        $app_paths = OlzApps::getAppPaths();
        $apps = OlzApps::getApps();
        $this->assertSame($app_paths, array_map(function ($app) {
            return $app->getPath();
        }, $apps));
    }

    public function testAppsSameAsBasenames(): void {
        $apps = OlzApps::getApps();
        $this->assertSame(self::BASENAMES, array_map(function ($app) {
            return $app->getBasename();
        }, $apps));
    }

    public function testGetApps(): void {
        $apps = OlzApps::getApps();

        foreach ($apps as $app) {
            $display_name = $app->getDisplayName();
            $this->assertTrue(is_string($display_name));
            $this->assertTrue(strlen($display_name) > 0);

            $href = $app->getHref();
            $this->assertTrue(is_string($href));
            $this->assertTrue(strlen($href) > 0);

            $icon_path = $app->getIconPath();
            $this->assertTrue(is_string($icon_path));
            $this->assertTrue(strlen($icon_path) > 0);

            $icon = $app->getIcon();
            $this->assertTrue(is_string($icon));
            $this->assertTrue(strlen($icon) > 0);
        }
    }
}
