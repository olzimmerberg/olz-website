<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;

class FakeGetPrefillValuesEndpointUserRepository extends Fake\FakeUserRepository {
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint
 */
final class GetPrefillValuesEndpointTest extends UnitTestCase {
    public function testGetPrefillValuesEndpointIdent(): void {
        $endpoint = new GetPrefillValuesEndpoint();
        $this->assertSame('GetPrefillValuesEndpoint', $endpoint->getIdent());
    }

    public function testGetPrefillValuesEndpointNoAccess(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([
                'userId' => null,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetPrefillValuesEndpoint(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->current_user = Fake\FakeUsers::adminUser();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new FakeGetPrefillValuesEndpointUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'userId' => null,
        ]);

        $this->assertSame([
            'firstName' => 'Admin',
            'lastName' => 'Istrator',
            'username' => 'admin',
            'email' => 'admin-user@test.olzimmerberg.ch',
            'phone' => '+410123456',
            'gender' => 'M',
            'birthdate' => '2000-01-01 00:00:00',
            'street' => 'Data Hwy. 42',
            'postalCode' => '19216811',
            'city' => 'Test',
            'region' => 'XX',
            'countryCode' => 'CH',
            'siCardNumber' => 127001,
            'solvNumber' => '000ADM',
        ], $result);
    }

    public function testGetPrefillValuesEndpointManagedUser(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->current_user = Fake\FakeUsers::adminUser();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new FakeGetPrefillValuesEndpointUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'userId' => 1,
        ]);

        $this->assertSame([
            'firstName' => 'Default',
            'lastName' => 'User',
            'username' => 'user',
            'email' => 'default-user@olzimmerberg.ch',
            'phone' => null,
            'gender' => null,
            'birthdate' => null,
            'street' => null,
            'postalCode' => null,
            'city' => null,
            'region' => null,
            'countryCode' => null,
            'siCardNumber' => null,
            'solvNumber' => null,
        ], $result);
    }

    public function testGetPrefillValuesEndpointOtherUser(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new FakeGetPrefillValuesEndpointUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([
                'userId' => 3,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }
}
