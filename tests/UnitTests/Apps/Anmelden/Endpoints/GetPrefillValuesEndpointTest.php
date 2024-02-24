<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

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
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->runtimeSetup();

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
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::adminUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'userId' => null,
        ]);

        $this->assertSame([
            'firstName' => 'Admin',
            'lastName' => 'Istrator',
            'username' => 'admin',
            'email' => 'admin-user@staging.olzimmerberg.ch',
            'phone' => '+410123456',
            'gender' => 'M',
            'birthdate' => '2000-01-01',
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
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::adminUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'userId' => 1,
        ]);

        $this->assertSame([
            'firstName' => 'Default',
            'lastName' => 'User',
            'username' => 'user',
            'email' => 'default-user@staging.olzimmerberg.ch',
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
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::defaultUser();
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->runtimeSetup();

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
