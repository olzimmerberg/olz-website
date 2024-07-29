<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\GeneralUtils;
use Olz\Utils\LogsUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\LogsUtils
 */
final class LogsUtilsIntegrationTest extends IntegrationTestCase {
    public function testLogsUtilsFromEnv(): void {
        $env_utils = FakeIntegrationTestEnvUtils::fromEnv();
        $logs_utils = LogsUtils::fromEnv();
        $logs_utils->setEnvUtils($env_utils);
        $private_path = $env_utils->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        if (is_dir($logs_path)) {
            $general_utils = GeneralUtils::fromEnv();
            $general_utils->removeRecursive($logs_path);
        }
        $this->assertFalse(is_dir($logs_path));

        $logger = $logs_utils->getLogger('test');
        $logger->debug('just for test');

        $this->assertSame('test', $logger->getName());
        $this->assertTrue(is_dir($private_path));
        $this->assertTrue(is_dir($logs_path));
        $this->assertMatchesRegularExpression(
            '/^merged\-[0-9]{4}\-[0-9]{2}\-[0-9]{2}\.log$/',
            scandir($logs_path)[2]
        );
    }
}
