<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetManagedUsersEndpoint;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

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
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetManagedUsersEndpoint(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetManagedUsersEndpoint();
        $endpoint->runtimeSetup();

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
