<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/endpoints/GetLogsEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

/**
 * @internal
 * @covers \GetLogsEndpoint
 */
final class GetLogsEndpointTest extends TestCase {
    public function testGetLogsEndpointIdent(): void {
        $endpoint = new GetLogsEndpoint();
        $this->assertSame('GetLogsEndpoint', $endpoint->getIdent());
    }

    public function testGetLogsEndpoint(): void {
        $logger = new Logger('GetLogsEndpointTest');
        $endpoint = new GetLogsEndpoint();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call(['logType' => 'ERROR']);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(
                'Es ist ein Fehler aufgetreten. Bitte später nochmals versuchen.',
                $httperr->getMessage(),
            );
        }
    }

    public function testGetLogsEndpointNotAuthorized(): void {
        $logger = new Logger('GetLogsEndpointTest');
        $endpoint = new GetLogsEndpoint();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call(['logType' => 'ERROR']);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
        }
    }

    public function testGetLogsEndpointNotAuthenticated(): void {
        $logger = new Logger('GetLogsEndpointTest');
        $endpoint = new GetLogsEndpoint();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call(['logType' => 'ERROR']);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
        }
    }
}
