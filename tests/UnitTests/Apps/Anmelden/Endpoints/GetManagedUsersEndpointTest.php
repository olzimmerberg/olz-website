<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetManagedUsersEndpoint;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeGetManagedUsersEndpointUserRepository extends Fake\FakeUserRepository {
    public function findBy($query) {
        if ($query == ['parent_user' => 2]) {
            return [
                Fake\FakeUsers::vorstandUser(),
                Fake\FakeUsers::defaultUser(),
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
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetManagedUsersEndpoint();

        try {
            $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetManagedUsersEndpoint(): void {
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::adminUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $user_repo = new FakeGetManagedUsersEndpointUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint = new GetManagedUsersEndpoint();

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
