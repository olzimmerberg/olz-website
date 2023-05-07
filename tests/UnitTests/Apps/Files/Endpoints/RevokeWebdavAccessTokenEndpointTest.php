<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Files\Endpoints;

use Olz\Apps\Files\Endpoints\RevokeWebdavAccessTokenEndpoint;
use Olz\Entity\AccessToken;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeRevokeWebdavAccessTokenEndpointAccessTokenRepository {
    public function findOneBy($where) {
        return new AccessToken();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Files\Endpoints\RevokeWebdavAccessTokenEndpoint
 */
final class RevokeWebdavAccessTokenEndpointTest extends UnitTestCase {
    public function testRevokeWebdavAccessTokenEndpointIdent(): void {
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $this->assertSame('RevokeWebdavAccessTokenEndpoint', $endpoint->getIdent());
    }

    public function testRevokeWebdavAccessTokenEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => false];
        $logger = Fake\FakeLogger::create();
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $endpoint->setLog($logger);

        $result = $endpoint->call([]);

        $this->assertSame(['status' => 'ERROR'], $result);
    }

    public function testRevokeWebdavAccessTokenEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeRevokeWebdavAccessTokenEndpointAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => true];
        $logger = Fake\FakeLogger::create();
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame(1, count($entity_manager->removed));
        $this->assertSame(1, count($entity_manager->flushed_removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }
}
