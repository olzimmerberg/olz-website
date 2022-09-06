<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Monolog\Logger;
use Olz\Api\Endpoints\LinkTelegramEndpoint;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeTelegramUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\LinkTelegramEndpoint
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
