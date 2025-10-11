<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Files\Endpoints;

use Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class DeterministicGetWebdavAccessTokenEndpoint extends GetWebdavAccessTokenEndpoint {
    protected function generateRandomAccessToken(): string {
        return 'AAAAAAAAAAAAAAAAAAAAAAAA';
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint
 */
final class GetWebdavAccessTokenEndpointTest extends UnitTestCase {
    public function testGetWebdavAccessTokenEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => false];
        $endpoint = new GetWebdavAccessTokenEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetWebdavAccessTokenEndpointExisting(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => true];
        $endpoint = new DeterministicGetWebdavAccessTokenEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
            'token' => 'webdav-token',
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testGetWebdavAccessTokenEndpointNew(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => true];
        $endpoint = new DeterministicGetWebdavAccessTokenEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
            'token' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $access_token = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $access_token->getId());
        $this->assertSame(FakeUser::defaultUser(), $access_token->getUser());
        $this->assertSame('WebDAV', $access_token->getPurpose());
        $this->assertSame('AAAAAAAAAAAAAAAAAAAAAAAA', $access_token->getToken());
        $this->assertSame('2020-03-13 19:30:00', $access_token->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertNull($access_token->getExpiresAt());
    }
}
