<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/env/LogsUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \LogsUtils
 */
final class LogsUtilsIntegrationTest extends IntegrationTestCase {
    public function testLogsUtilsFromEnv(): void {
        $logs_utils = LogsUtils::fromEnv();

        $this->assertSame(false, !$logs_utils);
    }
}
