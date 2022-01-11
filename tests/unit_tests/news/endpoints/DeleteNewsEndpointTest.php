<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/news/endpoints/DeleteNewsEndpoint.php';
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
 * @covers \DeleteNewsEndpoint
 */
final class DeleteNewsEndpointTest extends UnitTestCase {
    public function testDeleteNewsEndpointIdent(): void {
        $endpoint = new DeleteNewsEndpoint();
        $this->assertSame('DeleteNewsEndpoint', $endpoint->getIdent());
    }
}
