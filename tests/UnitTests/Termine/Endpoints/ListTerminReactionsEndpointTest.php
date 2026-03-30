<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\ListTerminReactionsEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\ListTerminReactionsEndpoint
 */
final class ListTerminReactionsEndpointTest extends UnitTestCase {
    public function testListTerminReactionsEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        WithUtilsCache::get('authUtils')->current_user = null;
        $endpoint = new ListTerminReactionsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'filter' => ['terminId' => 123],
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

    public function testListTerminReactionsEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::minimal();
        $endpoint = new ListTerminReactionsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'filter' => ['terminId' => 1234],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'result' => [
                [
                    'userId' => 1234,
                    'name' => 'Maximal User',
                    'emoji' => '❎',
                ],
            ],
        ], $result);
    }
}
