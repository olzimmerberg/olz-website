<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Captcha\Endpoints;

use Olz\Captcha\Endpoints\StartCaptchaEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Captcha\Endpoints\StartCaptchaEndpoint
 */
final class StartCaptchaEndpointTest extends UnitTestCase {
    public function testStartCaptchaEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
        ];
        $endpoint = new StartCaptchaEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'config' => [
                'rand' => 'YWFh',
                'date' => '2020-03-13 19:30:00',
                'mac' => 'd_UA-cRirxdks0oTwV9Q7ox2Wcf6NhpHbexQejsDQZ4',
            ],
        ], $result);
    }
}
