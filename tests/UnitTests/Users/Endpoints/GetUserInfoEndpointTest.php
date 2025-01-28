<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Users\Endpoints;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Users\Endpoints\GetUserInfoEndpoint;
use Olz\Utils\GeneralUtils;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Users\Endpoints\GetUserInfoEndpoint
 */
final class GetUserInfoEndpointTest extends UnitTestCase {
    public function testGetUserInfoEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
        ];
        $endpoint = new GetUserInfoEndpoint();
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

    public function testGetUserInfoEndpointAccessWithToken(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
            'user_email' => false,
        ];
        $endpoint = new GetUserInfoEndpoint();
        $endpoint->runtimeSetup();
        $general_utils = new GeneralUtils();

        $result = $endpoint->call([
            'id' => $id,
            'custom' => ['recaptchaToken' => 'valid'],
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
                'firstName' => 'Maximal',
                'lastName' => 'User',
                'email' => $general_utils->base64EncodeUrl('maximal-user@staging.olzimmerberg.ch'),
                'avatarImageId' => [
                    '2x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$256.jpg',
                    '1x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$128.jpg',
                ],
            ],
        ], $result);
    }

    public function testGetUserInfoEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => true,
        ];
        $endpoint = new GetUserInfoEndpoint();
        $endpoint->runtimeSetup();
        $general_utils = new GeneralUtils();

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
                'firstName' => 'Required',
                'lastName' => 'Non-empty',
                // Official email (from username)
                'email' => $general_utils->base64EncodeUrl('minimal-user@staging.olzimmerberg.ch'),
                'avatarImageId' => [
                    '1x' => '/_/assets/user_initials_RN.svg',
                ],
            ],
        ], $result);
    }

    public function testGetUserInfoEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => false,
        ];
        $endpoint = new GetUserInfoEndpoint();
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
                'firstName' => 'Required',
                'lastName' => 'Non-empty',
                'email' => null,
                'avatarImageId' => [
                    '1x' => '/_/assets/user_initials_RN.svg',
                ],
            ],
        ], $result);
    }

    public function testGetUserInfoEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => true,
        ];
        $endpoint = new GetUserInfoEndpoint();
        $endpoint->runtimeSetup();
        $general_utils = new GeneralUtils();

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
                'firstName' => 'Maximal',
                'lastName' => 'User',
                'email' => $general_utils->base64EncodeUrl('maximal-user@staging.olzimmerberg.ch'),
                'avatarImageId' => [
                    '2x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$256.jpg',
                    '1x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$128.jpg',
                ],
            ],
        ], $result);
    }
}
