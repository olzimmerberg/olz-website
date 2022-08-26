<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Files\Endpoints;

use Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint;
use Olz\Entity\AccessToken;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\Fake\FakeUsers;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\GeneralUtils;

require_once __DIR__.'/../../../../Fake/fake_role.php';

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
 * @covers \Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint
 */
final class GetWebdavAccessTokenEndpointTest extends UnitTestCase {
    public function testGetWebdavAccessTokenEndpointIdent(): void {
        $endpoint = new GetWebdavAccessTokenEndpoint();
        $this->assertSame('GetWebdavAccessTokenEndpoint', $endpoint->getIdent());
    }

    public function testGetWebdavAccessTokenEndpointNoAccess(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['webdav' => false];
        $logger = FakeLogger::create();
        $endpoint = new GetWebdavAccessTokenEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame(['status' => 'ERROR', 'token' => null], $result);
    }

    public function testGetWebdavAccessTokenEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $access_token_repo = new FakeGetWebdavAccessTokenEndpointAccessTokenRepository();
        $entity_manager->repositories[AccessToken::class] = $access_token_repo;
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['webdav' => true];
        $general_utils = GeneralUtils::fromEnv();
        $logger = FakeLogger::create();
        $endpoint = new DeterministicGetWebdavAccessTokenEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setGeneralUtils($general_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
            'token' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $access_token = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $access_token->getId());
        $this->assertSame(FakeUsers::adminUser(), $access_token->getUser());
        $this->assertSame('WebDAV', $access_token->getPurpose());
        $this->assertSame('AAAAAAAAAAAAAAAAAAAAAAAA', $access_token->getToken());
        $this->assertSame('2020-03-13 19:30:00', $access_token->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame(null, $access_token->getExpiresAt());
    }
}