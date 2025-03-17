<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EnvUtils;
use Olz\Utils\GeneralUtils;
use Olz\Utils\LogsUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\LogsUtils
 */
final class LogsUtilsIntegrationTest extends IntegrationTestCase {
    public function testLogsUtilsFromEnv(): void {
        $utils = $this->getSut();
        $env_utils = new EnvUtils();
        $utils->setEnvUtils($env_utils);

        $private_path = $env_utils->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        if (is_dir($logs_path)) {
            $general_utils = GeneralUtils::fromEnv();
            $general_utils->removeRecursive($logs_path);
        }
        $this->assertFalse(is_dir($logs_path));

        $logger = $utils->getLogger('test');
        $logger->debug('just for test');

        $this->assertSame('test', $logger->getName());
        $this->assertTrue(is_dir($private_path ?? ''));
        $this->assertTrue(is_dir($logs_path));
        $this->assertMatchesRegularExpression(
            '/^merged\-[0-9]{4}\-[0-9]{2}\-[0-9]{2}\.log$/',
            (scandir($logs_path) ?: [])[2]
        );
    }

    protected function getSut(): LogsUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(LogsUtils::class);
    }
}
