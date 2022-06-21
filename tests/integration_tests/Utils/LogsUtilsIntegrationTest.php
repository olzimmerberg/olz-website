<?php

declare(strict_types=1);

use Olz\Utils\LogsUtils;

require_once __DIR__.'/../common/IntegrationTestCase.php';

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
