<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\LogsUtils;

/**
 * @internal
 * @covers \Olz\Utils\LogsUtils
 */
final class LogsUtilsIntegrationTest extends IntegrationTestCase {
    public function testLogsUtilsFromEnv(): void {
        $logs_utils = LogsUtils::fromEnv();

        $this->assertSame(false, !$logs_utils);
    }
}
