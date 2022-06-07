<?php

declare(strict_types=1);

use App\Entity\AccessToken;

require_once __DIR__.'/../../../../_/webdav/endpoints/RevokeWebdavAccessTokenEndpoint.php';
require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeRevokeWebdavAccessTokenEndpointAccessTokenRepository {
    public function findOneBy($where) {
        return new AccessToken();
    }
}

/**
 * @internal
 * @covers \RevokeWebdavAccessTokenEndpoint
 */
final class RevokeWebdavAccessTokenEndpointTest extends UnitTestCase {
    public function testRevokeWebdavAccessTokenEndpointIdent(): void {
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $this->assertSame('RevokeWebdavAccessTokenEndpoint', $endpoint->getIdent());
    }

    public function testRevokeWebdavAccessTokenEndpointNoAccess(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['webdav' => false];
        $logger = FakeLogger::create();
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame(['status' => 'ERROR'], $result);
    }

    public function testRevokeWebdavAccessTokenEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeRevokeWebdavAccessTokenEndpointAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['webdav' => true];
        $logger = FakeLogger::create();
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame(1, count($entity_manager->removed));
        $this->assertSame(1, count($entity_manager->flushed_removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }
}
