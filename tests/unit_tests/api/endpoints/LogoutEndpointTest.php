<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/endpoints/LogoutEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

/**
 * @internal
 * @covers \LogoutEndpoint
 */
final class LogoutEndpointTest extends TestCase {
    public function testLogoutEndpointIdent(): void {
        $endpoint = new LogoutEndpoint();
        $this->assertSame('LogoutEndpoint', $endpoint->getIdent());
    }

    public function testLogoutEndpoint(): void {
        $logger = new Logger('LogoutEndpointTest');
        $endpoint = new LogoutEndpoint();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame([], $session->session_storage);
    }
}
