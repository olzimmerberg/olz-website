<?php

declare(strict_types=1);

use Monolog\Logger;
use Olz\Utils\MemorySession;

require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeTelegramUtils.php';
require_once __DIR__.'/../../../../_/api/endpoints/LinkTelegramEndpoint.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \LinkTelegramEndpoint
 */
final class LinkTelegramEndpointTest extends UnitTestCase {
    public function testLinkTelegramEndpointIdent(): void {
        $endpoint = new LinkTelegramEndpoint();
        $this->assertSame('LinkTelegramEndpoint', $endpoint->getIdent());
    }

    public function testLinkTelegramEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $telegram_utils = new FakeTelegramUtils();
        $logger = new Logger('LinkTelegramEndpointTest');
        $endpoint = new LinkTelegramEndpoint();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame([
            'botName' => 'bot-name',
            'pin' => 'correct-pin',
        ], $result);
    }
}
