<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\UserAgentUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\UserAgentUtils
 */
final class UserAgentUtilsTest extends UnitTestCase {
    public function testIPhone(): void {
        $user_agent_utils = new UserAgentUtils();
        $user_agent_utils->setServer(['HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1]']);
        $this->assertSame(false, $user_agent_utils->isAndroidDevice());
        $this->assertSame(true, $user_agent_utils->isIOsDevice());
        $this->assertSame(true, $user_agent_utils->isIPhone());
        $this->assertSame(false, $user_agent_utils->isIPad());
    }

    public function testIPad(): void {
        $user_agent_utils = new UserAgentUtils();
        $user_agent_utils->setServer(['HTTP_USER_AGENT' => 'Mozilla/5.0 (iPad; CPU OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148']);
        $this->assertSame(false, $user_agent_utils->isAndroidDevice());
        $this->assertSame(true, $user_agent_utils->isIOsDevice());
        $this->assertSame(false, $user_agent_utils->isIPhone());
        $this->assertSame(true, $user_agent_utils->isIPad());
    }

    public function testAndroid(): void {
        $user_agent_utils = new UserAgentUtils();
        $user_agent_utils->setServer(['HTTP_USER_AGENT' => 'Mozilla/5.0 (Linux; U; Android 2.2) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1']);
        $this->assertSame(true, $user_agent_utils->isAndroidDevice());
        $this->assertSame(false, $user_agent_utils->isIOsDevice());
        $this->assertSame(false, $user_agent_utils->isIPhone());
        $this->assertSame(false, $user_agent_utils->isIPad());
    }
}
