<?php

declare(strict_types=1);

use Olz\Utils\UserAgentUtils;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \Olz\Utils\UserAgentUtils
 */
final class UserAgentUtilsIntegrationTest extends IntegrationTestCase {
    public function testUserAgentUtilsFromEnv(): void {
        $user_agent_utils = UserAgentUtils::fromEnv();
        $this->assertSame(false, $user_agent_utils->isAndroidDevice());
        $this->assertSame(false, $user_agent_utils->isIOsDevice());
        $this->assertSame(false, $user_agent_utils->isIPhone());
        $this->assertSame(false, $user_agent_utils->isIPad());
    }
}
