<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Common\Utils\AuthUtils;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @covers \Olz\EventListener\SessionRefreshListener
 */
final class SessionRefreshListenerIntegrationTest extends IntegrationTestCase {
    public function testRestoresSessionOnInitialPageLoad(): void {
        $this->withLockedDb(function () {
            $container = self::getContainer();
            /** @var EntityManagerInterface $entity_manager */
            $entity_manager = $container->get(EntityManagerInterface::class);
            // Insert the refresh token directly, so that the full token lookup
            // (`getRefreshToken`) is exercised.
            $raw_token = 'integration-test-refresh-token';
            $entity_manager->getConnection()->executeStatement(
                'INSERT INTO access_tokens (user_id, purpose, token, created_at, expires_at)'
                .' VALUES (:user_id, :purpose, :token, :created_at, :expires_at)',
                [
                    'user_id' => 1,
                    'purpose' => 'refresh',
                    'token' => hash('sha256', $raw_token),
                    'created_at' => '2020-01-01 00:00:00',
                    'expires_at' => '2099-01-01 00:00:00',
                ],
            );

            $request = Request::create('/');
            $request->cookies->set(AuthUtils::REFRESH_TOKEN_COOKIE, $raw_token);
            $response = self::bootKernel()->handle($request);

            $this->assertSame(200, $response->getStatusCode());
            $body = $response->getContent() ?: '';
            // The very first page load must already be rendered as logged in.
            $this->assertStringContainsString('"username":"admin"', $body);
            $cookie_names = array_map(
                fn (Cookie $cookie): string => $cookie->getName(),
                $response->headers->getCookies(),
            );
            $this->assertContains(AuthUtils::REFRESH_TOKEN_COOKIE, $cookie_names);

            // The refresh token must have been rotated in the database.
            $token_hash = hash('sha256', $raw_token);
            $rotated_row = $entity_manager->getConnection()->fetchAssociative(
                'SELECT token, rotated_at FROM access_tokens WHERE old_token = :old_token',
                ['old_token' => $token_hash],
            );
            $this->assertIsArray($rotated_row);
            $this->assertNotSame($token_hash, $rotated_row['token']);
            $this->assertNotNull($rotated_row['rotated_at']);
        });
    }

    public function testDoesNotRestoreSessionWithoutRefreshToken(): void {
        $this->withLockedDb(function () {
            $request = Request::create('/');
            $response = self::bootKernel()->handle($request);

            $this->assertSame(200, $response->getStatusCode());
            $body = $response->getContent() ?: '';
            $this->assertStringContainsString('"username":null', $body);
            $cookie_names = array_map(
                fn (Cookie $cookie): string => $cookie->getName(),
                $response->headers->getCookies(),
            );
            $this->assertNotContains(AuthUtils::REFRESH_TOKEN_COOKIE, $cookie_names);
        });
    }
}
