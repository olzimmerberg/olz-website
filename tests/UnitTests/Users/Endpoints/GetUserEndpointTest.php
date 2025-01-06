<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Users\Endpoints;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Users\Endpoints\GetUserEndpoint;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Users\Endpoints\GetUserEndpoint
 */
final class GetUserEndpointTest extends UnitTestCase {
    public function testGetUserEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => false];
        $endpoint = new GetUserEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::MINIMAL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetUserEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        $endpoint = new GetUserEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'parentUserId' => null,
                'firstName' => 'Required',
                'lastName' => 'Non-empty',
                'username' => 'minimal-user',
                'password' => null,
                'email' => 'minimal-user@staging.olzimmerberg.ch',
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
                'avatarImageId' => null,
            ],
        ], $result);
    }

    public function testGetUserEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        $endpoint = new GetUserEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'parentUserId' => null,
                'firstName' => 'Required',
                'lastName' => 'Non-empty',
                'username' => 'empty-user',
                'password' => null,
                'email' => 'empty-user@staging.olzimmerberg.ch',
                'phone' => null,
                'gender' => null,
                'birthdate' => '1970-01-01',
                'street' => null,
                'postalCode' => null,
                'city' => null,
                'region' => null,
                'countryCode' => null,
                'siCardNumber' => null,
                'solvNumber' => null,
                'avatarImageId' => null,
            ],
        ], $result);
    }

    public function testGetUserEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        $endpoint = new GetUserEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/users/');
        mkdir(__DIR__."/../../tmp/img/users/{$id}/");
        file_put_contents(__DIR__."/../../tmp/img/users/{$id}.jpg", '');
        file_put_contents(__DIR__."/../../tmp/img/users/{$id}@2x.jpg", '');

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'parentUserId' => 1,
                'firstName' => 'Maximal',
                'lastName' => 'User',
                'username' => 'maximal-user',
                'password' => null,
                'email' => 'maximal-user@staging.olzimmerberg.ch',
                'phone' => '+410123456',
                'gender' => 'M',
                'birthdate' => '2020-03-13',
                'street' => 'Data Hwy. 42',
                'postalCode' => '19216811',
                'city' => 'Test',
                'region' => 'XX',
                'countryCode' => 'CH',
                'siCardNumber' => 127001,
                'solvNumber' => '000ADM',
                'avatarImageId' => 'image__________________1.jpg',
            ],
        ], $result);
    }
}
