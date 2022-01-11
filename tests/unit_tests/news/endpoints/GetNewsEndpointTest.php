<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/news/endpoints/GetNewsEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \GetNewsEndpoint
 */
final class GetNewsEndpointTest extends UnitTestCase {
    public function testGetNewsEndpointIdent(): void {
        $endpoint = new GetNewsEndpoint();
        $this->assertSame('GetNewsEndpoint', $endpoint->getIdent());
    }
}
