<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Endpoints;

use Olz\Common\Endpoints\RefreshEndpoint;
use Olz\Entity\AccessToken;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AuthUtils;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 *
 * @covers \Olz\Common\Endpoints\RefreshEndpoint
 */
final class RefreshEndpointTest extends UnitTestCase {
    public function testRefreshEndpoint(): void {
        $auth_utils = WithUtilsCache::get('authUtils');
        $access_token = $this->createRefreshToken('raw-token', FakeUser::adminUser(), '2021-01-01 00:00:00');
        $auth_utils->refresh_token = $access_token;
        $request = new Request(cookies: [AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token']);
        $response = new Response();
        $endpoint = new RefreshEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setHttpContext($request, $response);

        $result = $endpoint->call([]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame('admin', WithUtilsCache::get('session')->get('user'));
        $this->assertSame('2021-03-13 19:30:00', $access_token->getExpiresAt()?->format('Y-m-d H:i:s'));
        $cookies = $response->headers->getCookies();
        $this->assertCount(1, $cookies);
        $this->assertSame('raw-token', $cookies[0]->getValue());
    }

    public function testRefreshEndpointWithExistingSession(): void {
        WithUtilsCache::get('session')->session_storage = ['user' => 'admin'];
        $auth_utils = WithUtilsCache::get('authUtils');
        $auth_utils->refresh_token = null;
        $response = new Response();
        $endpoint = new RefreshEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setHttpContext(new Request(), $response);

        $result = $endpoint->call([]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertCount(0, $response->headers->getCookies());
    }

    public function testRefreshEndpointWithoutCookie(): void {
        $auth_utils = WithUtilsCache::get('authUtils');
        $auth_utils->refresh_token = null;
        $response = new Response();
        $endpoint = new RefreshEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setHttpContext(new Request(), $response);

        try {
            $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(401, $httperr->getCode());
        }
        $cookies = $response->headers->getCookies();
        $this->assertCount(1, $cookies);
        $this->assertSame('', $cookies[0]->getValue());
    }

    public function testRefreshEndpointWithExpiredToken(): void {
        $auth_utils = WithUtilsCache::get('authUtils');
        $access_token = $this->createRefreshToken('raw-token', FakeUser::adminUser(), '2020-01-01 00:00:00');
        $auth_utils->refresh_token = $access_token;
        $request = new Request(cookies: [AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token']);
        $response = new Response();
        $entity_manager = WithUtilsCache::get('entityManager');
        $endpoint = new RefreshEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setHttpContext($request, $response);

        try {
            $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(401, $httperr->getCode());
        }
        $this->assertSame([$access_token], $entity_manager->flushed_removed);
        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertCount(1, $response->headers->getCookies());
    }

    protected function createRefreshToken(
        string $raw_token,
        User $user,
        string $expires_at,
    ): AccessToken {
        $access_token = new AccessToken();
        $access_token->setUser($user);
        $access_token->setPurpose('refresh');
        $access_token->setToken(hash('sha256', $raw_token));
        $access_token->setCreatedAt(new \DateTime('2020-01-01 00:00:00'));
        $access_token->setExpiresAt(new \DateTime($expires_at));
        return $access_token;
    }
}
