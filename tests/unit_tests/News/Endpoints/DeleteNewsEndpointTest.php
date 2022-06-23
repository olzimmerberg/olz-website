<?php

declare(strict_types=1);

use Olz\News\Endpoints\DeleteNewsEndpoint;

require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \Olz\News\Endpoints\DeleteNewsEndpoint
 */
final class DeleteNewsEndpointTest extends UnitTestCase {
    public function testDeleteNewsEndpointIdent(): void {
        $endpoint = new DeleteNewsEndpoint();
        $this->assertSame('DeleteNewsEndpoint', $endpoint->getIdent());
    }
}
