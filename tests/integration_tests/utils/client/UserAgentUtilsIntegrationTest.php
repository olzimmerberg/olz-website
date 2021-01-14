<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/utils/client/UserAgentUtils.php';

/**
 * @internal
 * @covers \UserAgentUtils
 */
final class UserAgentUtilsIntegrationTest extends TestCase {
    public function testUserAgentUtilsFromEnv(): void {
        global $_SERVER;
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (cloud; RiscV) Selenium (like Gecko)';
        $user_agent_utils = getUserAgentUtilsFromEnv();
        $this->assertSame(false, $user_agent_utils->isAndroidDevice());
        $this->assertSame(false, $user_agent_utils->isIOsDevice());
        $this->assertSame(false, $user_agent_utils->isIPhone());
        $this->assertSame(false, $user_agent_utils->isIPad());
    }
}
