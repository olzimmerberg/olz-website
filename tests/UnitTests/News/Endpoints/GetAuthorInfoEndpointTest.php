<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\News\Endpoints\GetAuthorInfoEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\GetAuthorInfoEndpoint
 */
final class GetAuthorInfoEndpointTest extends UnitTestCase {
    public function testGetAuthorInfoEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
        ];
        $endpoint = new GetAuthorInfoEndpoint();
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

    public function testGetAuthorInfoEndpointAccessWithToken(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
            'user_email' => false,
        ];
        $endpoint = new GetAuthorInfoEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
            'recaptchaToken' => 'valid',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'roleName' => 'Administrator',
            'roleUsername' => 'admin_role',
            'firstName' => 'Admin',
            'lastName' => 'Istrator',
            'email' => $this->emailUtils()->obfuscateEmail('admin-user@staging.olzimmerberg.ch'),
            'avatarImageId' => [
                '1x' => '/_/assets/user_initials_AI.svg',
            ],
        ], $result);
    }

    public function testGetAuthorInfoEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => true,
        ];
        $endpoint = new GetAuthorInfoEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'roleName' => null,
            'roleUsername' => null,
            'firstName' => '-',
            'lastName' => '',
            'email' => null,
            'avatarImageId' => null,
        ], $result);
    }

    public function testGetAuthorInfoEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => false,
        ];
        $endpoint = new GetAuthorInfoEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'roleName' => null,
            'roleUsername' => null,
            'firstName' => '-',
            'lastName' => '',
            'email' => null,
            'avatarImageId' => null,
        ], $result);
    }

    public function testGetAuthorInfoEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'user_email' => true,
        ];
        $endpoint = new GetAuthorInfoEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'roleName' => 'Administrator',
            'roleUsername' => 'admin_role',
            'firstName' => 'Admin',
            'lastName' => 'Istrator',
            'email' => $this->emailUtils()->obfuscateEmail('admin@staging.olzimmerberg.ch'),
            'avatarImageId' => [
                '1x' => '/_/assets/user_initials_AI.svg',
            ],
        ], $result);
    }
}
