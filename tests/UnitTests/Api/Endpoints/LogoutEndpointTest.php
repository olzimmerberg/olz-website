<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\LogoutEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\LogoutEndpoint
 */
final class LogoutEndpointTest extends UnitTestCase {
    public function testLogoutEndpoint(): void {
        $endpoint = new LogoutEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'all verified_email',
            'root' => 'karten',
            'user_id' => '2',
            'user' => 'admin',
            'user_name' => 'Admin Istrator',
            'user_permissions' => '{"all":true,"verified_email":true}',
            'user_root' => 'karten',
            'user_children' => '[{"id":3,"permissions":{"aktuell":true,"ftp":true,"vorstand_user":true},"name":"Vorstand Mitglied","username":"vorstand","root":"vorstand"},{"id":1,"permissions":{"default":true},"name":"Default User","username":"default","root":null}]',
            'auth_user' => 'admin',
            'auth_user_id' => '2',
        ];

        $result = $endpoint->call([]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], WithUtilsCache::get('session')->session_storage);
        $this->assertTrue(WithUtilsCache::get('session')->cleared);
    }
}
