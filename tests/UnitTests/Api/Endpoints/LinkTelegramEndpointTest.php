<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\LinkTelegramEndpoint;
use Olz\Tests\Fake;
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
        $entity_manager = new Fake\FakeEntityManager();
        $endpoint = new LinkTelegramEndpoint();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setEntityManager($entity_manager);

        $result = $endpoint->call([]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'botName' => 'bot-name',
            'pin' => 'correct-pin',
        ], $result);
    }
}
