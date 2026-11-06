<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\EventListener;

use Olz\Common\Utils\AuthUtils;
use Olz\Common\Utils\WithUtilsCache;
use Olz\Entity\AccessToken;
use Olz\EventListener\SessionRefreshListener;
use Olz\Kernel;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TestOnlyThrowingAuthUtils extends FakeAuthUtils {
    public function refreshSessionFromRefreshToken(?string $raw_token): ?string {
        throw new \Error('Fake refresh error');
    }
}

/**
 * @internal
 *
 * @covers \Olz\EventListener\SessionRefreshListener
 */
final class SessionRefreshListenerTest extends UnitTestCase {
    public function testHandlesRefreshErrorGracefully(): void {
        WithUtilsCache::set('authUtils', new TestOnlyThrowingAuthUtils());
        $request = $this->createRequest('/termine', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ]);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
        $this->assertSame(
            ['WARNING Could not restore session from refresh token: Fake refresh error'],
            $this->getLogs(),
        );
    }

    public function testRestoresSessionFromRefreshTokenOnPageLoad(): void {
        $access_token = $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $request = $this->createRequest('/termine', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ]);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertSame('admin', WithUtilsCache::get('session')->get('user'));
        $this->assertSame('admin', WithUtilsCache::get('session')->get('auth_user'));
        $new_raw_token = $request->attributes->get('_olz_refresh_cookie');
        $this->assertIsString($new_raw_token);
        $this->assertNotSame('raw-token', $new_raw_token);
        $this->assertSame(hash('sha256', $new_raw_token), $access_token->getToken());
        $this->assertSame(hash('sha256', 'raw-token'), $access_token->getOldToken());
        $this->assertSame('2020-03-13 19:30:00', $access_token->getRotatedAt()?->format('Y-m-d H:i:s'));
        $this->assertSame('2021-03-13 19:30:00', $access_token->getExpiresAt()?->format('Y-m-d H:i:s'));
        $this->assertSame(['INFO Restored session and rotated refresh token on page load.'], $this->getLogs());

        $response = new Response();
        $listener->onKernelResponse($this->createResponseEvent($request, $response));

        $cookies = $response->headers->getCookies();
        $this->assertCount(1, $cookies);
        $this->assertSame($new_raw_token, $cookies[0]->getValue());
    }

    public function testDoesNotIssueCookieForPreviousTokenWithinGrace(): void {
        $access_token = $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $access_token->setOldToken(hash('sha256', 'old-token'));
        // Fake "now" is 2020-03-13 19:30:00; the grace period is one minute.
        $access_token->setRotatedAt(new \DateTime('2020-03-13 19:29:30'));
        $request = $this->createRequest('/termine', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'old-token',
        ]);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertSame('admin', WithUtilsCache::get('session')->get('user'));
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
        $response = new Response();
        $listener->onKernelResponse($this->createResponseEvent($request, $response));
        $this->assertCount(0, $response->headers->getCookies());
    }

    public function testDoesNothingWithoutRefreshTokenCookie(): void {
        $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $request = $this->createRequest('/termine');
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
        $response = new Response();
        $listener->onKernelResponse($this->createResponseEvent($request, $response));
        $this->assertCount(0, $response->headers->getCookies());
    }

    public function testDoesNothingIfSessionIsAlreadyAuthenticated(): void {
        WithUtilsCache::get('session')->session_storage = ['user' => 'admin'];
        $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $request = $this->createRequest('/termine', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ]);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertSame(['user' => 'admin'], WithUtilsCache::get('session')->session_storage);
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
        $this->assertSame([], $this->getLogs());
    }

    public function testDoesNothingForApiRequests(): void {
        $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $request = $this->createRequest('/api/getAuthenticatedUser', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ]);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
    }

    public function testDoesNothingForNonSafeRequests(): void {
        $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $request = $this->createRequest('/termine', method: 'POST', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ]);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
    }

    public function testDoesNothingForTokenBasedRequests(): void {
        $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $request = $this->createRequest('/termine?access_token=some-token', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ]);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
    }

    public function testDoesNothingWithoutSession(): void {
        $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $request = $this->createRequest('/termine', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ], with_session: false);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $this->assertFalse($request->hasSession());
        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
    }

    public function testRemovesExpiredRefreshToken(): void {
        $access_token = $this->mockRefreshToken('raw-token', '2020-01-01 00:00:00');
        $request = $this->createRequest('/termine', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ]);
        $listener = new SessionRefreshListener();

        $listener->onKernelRequest($this->createRequestEvent($request));

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([$access_token], $entity_manager->flushed_removed);
        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
        $response = new Response();
        $listener->onKernelResponse($this->createResponseEvent($request, $response));
        $this->assertCount(0, $response->headers->getCookies());
    }

    public function testDoesNothingForSubRequests(): void {
        $this->mockRefreshToken('raw-token', '2021-01-01 00:00:00');
        $request = $this->createRequest('/termine', cookies: [
            AuthUtils::REFRESH_TOKEN_COOKIE => 'raw-token',
        ]);
        $listener = new SessionRefreshListener();

        $event = new RequestEvent(
            new Kernel('test', true),
            $request,
            HttpKernelInterface::SUB_REQUEST,
        );
        $listener->onKernelRequest($event);

        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertFalse($request->attributes->has('_olz_refresh_cookie'));
    }

    /** @param array<string, string> $cookies */
    protected function createRequest(
        string $uri,
        string $method = 'GET',
        array $cookies = [],
        bool $with_session = true,
    ): Request {
        $request = Request::create($uri, $method, [], $cookies);
        if ($with_session) {
            $request->setSession(new SymfonySession(new MockArraySessionStorage()));
        }
        return $request;
    }

    protected function createRequestEvent(Request $request): RequestEvent {
        return new RequestEvent(
            new Kernel('test', true),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
    }

    protected function createResponseEvent(Request $request, Response $response): ResponseEvent {
        return new ResponseEvent(
            new Kernel('test', true),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response,
        );
    }

    protected function mockRefreshToken(string $raw_token, string $expires_at): AccessToken {
        $access_token = new AccessToken();
        $access_token->setUser(FakeUser::adminUser());
        $access_token->setPurpose('refresh');
        $access_token->setToken(hash('sha256', $raw_token));
        $access_token->setCreatedAt(new \DateTime('2020-01-01 00:00:00'));
        $access_token->setExpiresAt(new \DateTime($expires_at));
        /** @var FakeAuthUtils $auth_utils */
        $auth_utils = WithUtilsCache::get('authUtils');
        $auth_utils->refresh_token = $access_token;
        return $access_token;
    }
}
