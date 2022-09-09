<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetManagedUsersEndpoint;
use Olz\Entity\User;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\Fake\FakeUserRepository;
use Olz\Tests\Fake\FakeUsers;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;

class FakeGetManagedUsersEndpointUserRepository extends FakeUserRepository {
    public function findBy($query) {
        if ($query == ['parent_user' => 2]) {
            return [
                FakeUsers::vorstandUser(),
                FakeUsers::defaultUser(),
            ];
        }
        $json_query = json_encode($query);
        throw new \Exception("Query no mocked: {$json_query}");
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Anmelden\Endpoints\GetManagedUsersEndpoint
 */
final class GetManagedUsersEndpointTest extends UnitTestCase {
    public function testGetManagedUsersEndpointIdent(): void {
        $endpoint = new GetManagedUsersEndpoint();
        $this->assertSame('GetManagedUsersEndpoint', $endpoint->getIdent());
    }

    public function testGetManagedUsersEndpointNoAccess(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $logger = FakeLogger::create();
        $endpoint = new GetManagedUsersEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

        try {
            $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetManagedUsersEndpoint(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeGetManagedUsersEndpointUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new GetManagedUsersEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
            'managedUsers' => [
                [
                    'id' => 3,
                    'firstName' => 'Vorstand',
                    'lastName' => 'Mitglied',
                ],
                [
                    'id' => 1,
                    'firstName' => 'Default',
                    'lastName' => 'User',
                ],
            ],
        ], $result);
    }
}
