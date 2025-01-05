<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'userId' => null,
        ]);

        // TODO: Remove
        // @phpstan-ignore method.impossibleType
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetPrefillValuesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'userId' => 1,
        ]);

        // TODO: Remove
        // @phpstan-ignore method.impossibleType
        $this->assertSame([
            'firstName' => 'Default',
            'lastName' => 'User',
            'username' => 'default',
            'email' => 'default-user@staging.olzimmerberg.ch',
            'phone' => '+0815',
            'gender' => 'F',
            'birthdate' => '1970-01-01',
            'street' => 'Hauptstrasse 1',
            'postalCode' => '0815',
            'city' => 'Muster',
            'region' => 'XX',
            'countryCode' => 'CH',
            'siCardNumber' => 8150815,
            'solvNumber' => 'D3F4UL7',
        ], $result);
    }

    public function testGetPrefillValuesEndpointOtherUser(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
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
