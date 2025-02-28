<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Users\Endpoints;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Users\Endpoints\EditUserEndpoint;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Users\Endpoints\EditUserEndpoint
 */
final class EditUserEndpointTest extends UnitTestCase {
    public function testEditUserEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditUserEndpoint();
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

            $this->assertSame([
                [FakeUser::minimal(), null, null, null, null, 'users'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditUserEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        $endpoint = new EditUserEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::NULL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testEditUserEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditUserEndpoint();
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

            $this->assertSame([
                [FakeUser::minimal(), null, null, null, null, 'users'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditUserEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditUserEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeUser::minimal(), null, null, null, null, 'users'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

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
                'email' => null,
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

    public function testEditUserEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditUserEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING Upload ID \"\" is invalid.",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeUser::empty(), null, null, null, null, 'users'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

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
                'email' => null,
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

    public function testEditUserEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditUserEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/users/');
        mkdir(__DIR__."/../../tmp/img/users/{$id}/");
        mkdir(__DIR__."/../../tmp/img/users/{$id}/img/");
        mkdir(__DIR__."/../../tmp/img/users/{$id}/thumb/");
        file_put_contents(__DIR__."/../../tmp/img/users/{$id}/img/image__________________1.jpg", '');
        file_put_contents(__DIR__."/../../tmp/img/users/{$id}/thumb/image__________________1\$256.jpg", '');
        file_put_contents(__DIR__."/../../tmp/img/users/{$id}/thumb/image__________________1\$128.jpg", '');

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeUser::maximal(), 'default', 'default', 'role', null, 'users'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

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
