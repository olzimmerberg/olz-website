<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/endpoints/LogoutEndpoint.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

/**
 * @internal
 * @covers \LogoutEndpoint
 */
final class LogoutEndpointTest extends TestCase {
    public function testLogoutEndpoint(): void {
        $endpoint = new LogoutEndpoint();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call([]);

        $this->assertSame([], $session->session_storage);
    }
}
