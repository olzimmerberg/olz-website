<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EnvUtils;
use Olz\Utils\GeneralUtils;

/**
 * @internal
 *
 * @coversNothing
 */
final class LogsIntegrationTest extends IntegrationTestCase {
    public function testLogs(): void {
        $general_utils = $this->getSut();
        $env_utils = new EnvUtils();
        $general_utils->setEnvUtils($env_utils);

        $private_path = $env_utils->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        if (is_dir($logs_path)) {
            $general_utils->removeRecursive($logs_path);
        }
        $this->assertFalse(is_dir($logs_path));

        try {
            /* @phpstan-ignore method.impossibleType */
            $general_utils->checkNotNull(null, 'provoked message');
            $this->fail('error expected');
        } catch (\Throwable $th) {
            $this->assertSame('LogsIntegrationTest.php:*** provoked message', $th->getMessage());
        }

        $this->assertTrue(is_dir($private_path ?? ''));
        $this->assertTrue(is_dir($logs_path));
        $this->assertSame('test.log', (scandir($logs_path) ?: [])[2]);
    }

    protected function getSut(): GeneralUtils {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(GeneralUtils::class);
    }
}
