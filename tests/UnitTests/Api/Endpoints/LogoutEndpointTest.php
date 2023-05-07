<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\LogoutEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\LogoutEndpoint
 */
final class LogoutEndpointTest extends UnitTestCase {
    public function testLogoutEndpointIdent(): void {
        $endpoint = new LogoutEndpoint();
        $this->assertSame('LogoutEndpoint', $endpoint->getIdent());
    }

    public function testLogoutEndpoint(): void {
        $endpoint = new LogoutEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call([]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], $session->session_storage);
        $this->assertSame(true, $session->cleared);
    }
}
