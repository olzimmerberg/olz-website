<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Files\Endpoints;

use Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint;
use Olz\Entity\AccessToken;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeGetWebdavAccessTokenEndpointAccessTokenRepository {
    public function findOneBy($where) {
        return null;
    }
}

class DeterministicGetWebdavAccessTokenEndpoint extends GetWebdavAccessTokenEndpoint {
    protected function generateRandomAccessToken() {
        return 'AAAAAAAAAAAAAAAAAAAAAAAA';
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint
 */
final class GetWebdavAccessTokenEndpointTest extends UnitTestCase {
    public function testGetWebdavAccessTokenEndpointIdent(): void {
        $endpoint = new GetWebdavAccessTokenEndpoint();
        $this->assertSame('GetWebdavAccessTokenEndpoint', $endpoint->getIdent());
    }

    public function testGetWebdavAccessTokenEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => false];
        $endpoint = new GetWebdavAccessTokenEndpoint();

        $result = $endpoint->call([]);

        $this->assertSame(['status' => 'ERROR', 'token' => null], $result);
    }

    public function testGetWebdavAccessTokenEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $access_token_repo = new FakeGetWebdavAccessTokenEndpointAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => true];
        $endpoint = new DeterministicGetWebdavAccessTokenEndpoint();
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
            'token' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $access_token = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $access_token->getId());
        $this->assertSame(Fake\FakeUsers::defaultUser(), $access_token->getUser());
        $this->assertSame('WebDAV', $access_token->getPurpose());
        $this->assertSame('AAAAAAAAAAAAAAAAAAAAAAAA', $access_token->getToken());
        $this->assertSame('2020-03-13 19:30:00', $access_token->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame(null, $access_token->getExpiresAt());
    }
}
