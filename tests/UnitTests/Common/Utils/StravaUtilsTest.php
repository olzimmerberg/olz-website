<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\StravaUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\StravaUtils
 */
final class StravaUtilsTest extends UnitTestCase {
    public function testgGetRegistrationUrl(): void {
        $utils = new StravaUtils();

        $this->assertSame('fake-strava-client-id', $utils->getClientId());
        $this->assertSame(
            'https://www.strava.com/oauth/authorize?client_id=fake-strava-client-id&response_type=code&redirect_uri=http%3A%2F%2Ffake-base-url%2F_%2Fstrava_redirect&approval_prompt=force&scope=read',
            $utils->getRegistrationUrl(),
        );
        $this->assertSame(
            'https://www.strava.com/oauth/authorize?client_id=fake-strava-client-id&response_type=code&redirect_uri=http%3A%2F%2Ffake-base-url%2F_%2Fstrava_redirect%3Fredirect_url%3Dhttp%253A%252F%252Ffake-redirect&approval_prompt=force&scope=read%2Cactivity%3Aread',
            $utils->getRegistrationUrl(['read', 'activity:read'], 'http://fake-redirect'),
        );
        $this->assertSame([], $this->getLogs());
    }
}
