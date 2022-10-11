<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\LogsUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\LogsUtils
 */
final class LogsUtilsIntegrationTest extends IntegrationTestCase {
    public function testLogsUtilsFromEnvExists(): void {
        $logs_utils = LogsUtils::fromEnv();

        $this->assertSame(false, !$logs_utils);
    }

    public function testLogsUtilsFromEnv(): void {
        $env_utils = FakeIntegrationTestEnvUtils::fromEnv();
        $logs_utils = LogsUtils::fromEnv();
        $logs_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $logs_path = "{$data_path}logs/";
        if (is_dir($logs_path)) {
            foreach (scandir($logs_path) as $entry) {
                if ($entry != '.' && $entry != '..') {
                    unlink("{$logs_path}{$entry}");
                }
            }
            rmdir($logs_path);
        }
        $this->assertSame(false, is_dir($logs_path));

        $logger = $logs_utils->getLogger('test');
        $logger->debug('just for test');

        $this->assertSame('test', $logger->getName());
        $this->assertSame(true, is_dir($data_path));
        $this->assertSame(true, is_dir($logs_path));
        $this->assertMatchesRegularExpression(
            '/^merged\\-[0-9]{4}\\-[0-9]{2}\\-[0-9]{2}\\.log$/',
            scandir($logs_path)[2]
        );
    }
}
