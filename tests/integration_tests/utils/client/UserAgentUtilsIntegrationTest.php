<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/client/UserAgentUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \UserAgentUtils
 */
final class UserAgentUtilsIntegrationTest extends IntegrationTestCase {
    public function testUserAgentUtilsFromEnv(): void {
        $user_agent_utils = getUserAgentUtilsFromEnv();
        $this->assertSame(false, $user_agent_utils->isAndroidDevice());
        $this->assertSame(false, $user_agent_utils->isIOsDevice());
        $this->assertSame(false, $user_agent_utils->isIPhone());
        $this->assertSame(false, $user_agent_utils->isIPad());
    }
}
