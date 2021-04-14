<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/auth/AuthUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \AuthUtils
 */
final class AuthUtilsIntegrationTest extends IntegrationTestCase {
    public function testAuthUtilsFromEnv(): void {
        $auth_utils = AuthUtils::fromEnv();

        $this->assertSame(false, !$auth_utils);
    }
}
