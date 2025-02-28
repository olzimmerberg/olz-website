<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\LinkTelegramEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\LinkTelegramEndpoint
 */
final class LinkTelegramEndpointTest extends UnitTestCase {
    public function testLinkTelegramEndpoint(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new LinkTelegramEndpoint();
        $endpoint->runtimeSetup();

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

    public function testLinkTelegramEndpointUnauthenticated(): void {
        $endpoint = new LinkTelegramEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (\Throwable $th) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $this->getLogs());
            $this->assertSame('Kein Zugriff!', $th->getMessage());
        }
    }
}
