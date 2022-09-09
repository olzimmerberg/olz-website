<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint;
use Olz\Entity\User;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\Fake\FakeUserRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;

class FakeGetPrefillValuesEndpointUserRepository extends FakeUserRepository {
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
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $logger = FakeLogger::create();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

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
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeGetPrefillValuesEndpointUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'userId' => null,
        ]);

        $this->assertSame([
            'firstName' => 'Admin',
            'lastName' => 'Istrator',
            'username' => 'admin',
            'email' => 'admin-user@test.olzimmerberg.ch',
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

    public function testGetPrefillValuesEndpointManagedUser(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeGetPrefillValuesEndpointUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLogger($logger);

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
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeGetPrefillValuesEndpointUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLogger($logger);

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
