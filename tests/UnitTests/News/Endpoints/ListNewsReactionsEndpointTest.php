<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\News\Endpoints\ListNewsReactionsEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\ListNewsReactionsEndpoint
 */
final class ListNewsReactionsEndpointTest extends UnitTestCase {
    public function testListNewsReactionsEndpointAnonymous(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        WithUtilsCache::get('authUtils')->current_user = null;
        $endpoint = new ListNewsReactionsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'filter' => ['newsEntryId' => 123],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'result' => [
                [
                    'userId' => 123,
                    'name' => null,
                    'emoji' => '⭕',
                ],
            ],
        ], $result);
    }

    public function testListNewsReactionsEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::minimal();
        $endpoint = new ListNewsReactionsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'filter' => ['newsEntryId' => 12],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'result' => [
                [
                    'userId' => 12,
                    'name' => 'Required Non-empty',
                    'emoji' => '🚫',
                ],
            ],
        ], $result);
    }
}
