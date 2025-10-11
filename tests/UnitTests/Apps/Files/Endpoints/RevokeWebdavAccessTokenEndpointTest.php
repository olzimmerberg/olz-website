<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Files\Endpoints;

use Olz\Apps\Files\Endpoints\RevokeWebdavAccessTokenEndpoint;
use Olz\Tests\Fake\Entity\FakeAccessToken;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Files\Endpoints\RevokeWebdavAccessTokenEndpoint
 */
final class RevokeWebdavAccessTokenEndpointTest extends UnitTestCase {
    public function testRevokeWebdavAccessTokenEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => false];
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
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

    public function testRevokeWebdavAccessTokenEndpointExisting(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => true];
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->removed);
        $this->assertSame(FakeAccessToken::webDav(), $entity_manager->removed[0]);
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    public function testRevokeWebdavAccessTokenInexistent(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['webdav' => true];
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->removed);
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }
}
