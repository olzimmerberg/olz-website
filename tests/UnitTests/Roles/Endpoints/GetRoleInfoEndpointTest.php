<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Roles\Endpoints\GetRoleInfoEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\GetRoleInfoEndpoint
 */
final class GetRoleInfoEndpointTest extends UnitTestCase {
    public function testGetRoleInfoEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
        ];
        $endpoint = new GetRoleInfoEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::MINIMAL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetRoleInfoEndpointAccessWithToken(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
            'user_email' => false,
        ];
        $endpoint = new GetRoleInfoEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
            'captchaToken' => 'valid',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'name' => 'Test Role',
            'username' => 'test-role',
            'assignees' => [
                [
                    'firstName' => 'Maximal',
                    'lastName' => 'User',
                    'email' => $this->emailUtils()->obfuscateEmail('maximal-user@staging.olzimmerberg.ch'),
                    'avatarImageId' => [
                        '2x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$256.jpg',
                        '1x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$128.jpg',
                    ],
                ],
                [
                    'firstName' => 'Required',
                    'lastName' => 'Non-empty',
                    'email' => null,
                    'avatarImageId' => [
                        '1x' => '/_/assets/user_initials_RN.svg',
                    ],
                ],
                [
                    'firstName' => 'Required',
                    'lastName' => 'Non-empty',
                    'email' => null,
                    'avatarImageId' => [
                        '1x' => '/_/assets/user_initials_RN.svg',
                    ],
                ],
            ],
        ], $result);
    }

    public function testGetRoleInfoEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => true,
        ];
        $endpoint = new GetRoleInfoEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'name' => null,
            'username' => null,
            'assignees' => [],
        ], $result);
    }

    public function testGetRoleInfoEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => false,
        ];
        $endpoint = new GetRoleInfoEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'name' => null,
            'username' => null,
            'assignees' => [],
        ], $result);
    }

    public function testGetRoleInfoEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => true,
        ];
        $endpoint = new GetRoleInfoEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'name' => 'Test Role',
            'username' => 'test-role',
            'assignees' => [
                [
                    'firstName' => 'Maximal',
                    'lastName' => 'User',
                    'email' => $this->emailUtils()->obfuscateEmail('maximal-user@staging.olzimmerberg.ch'),
                    'avatarImageId' => [
                        '2x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$256.jpg',
                        '1x' => '/data-href/img/users/1234/thumb/image__________________1.jpg$128.jpg',
                    ],
                ],
                [
                    'firstName' => 'Required',
                    'lastName' => 'Non-empty',
                    // Official email (from username)
                    'email' => $this->emailUtils()->obfuscateEmail('empty-user@staging.olzimmerberg.ch'),
                    'avatarImageId' => [
                        '1x' => '/_/assets/user_initials_RN.svg',
                    ],
                ],
                [
                    'firstName' => 'Required',
                    'lastName' => 'Non-empty',
                    // Official email (from username)
                    'email' => $this->emailUtils()->obfuscateEmail('minimal-user@staging.olzimmerberg.ch'),
                    'avatarImageId' => [
                        '1x' => '/_/assets/user_initials_RN.svg',
                    ],
                ],
            ],
        ], $result);
    }
}
