<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/news/endpoints/UpdateNewsEndpoint.php';
require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \UpdateNewsEndpoint
 */
final class UpdateNewsEndpointTest extends UnitTestCase {
    public function testUpdateNewsEndpointIdent(): void {
        $endpoint = new UpdateNewsEndpoint();
        $this->assertSame('UpdateNewsEndpoint', $endpoint->getIdent());
    }
}
