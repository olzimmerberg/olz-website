<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../public/_/api/endpoints/LogoutEndpoint.php';
require_once __DIR__.'/../../../../public/_/config/vendor/autoload.php';
require_once __DIR__.'/../../../../public/_/utils/session/MemorySession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \LogoutEndpoint
 */
final class LogoutEndpointTest extends UnitTestCase {
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
        $this->assertSame(true, $session->cleared);
    }
}
