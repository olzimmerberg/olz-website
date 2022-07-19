<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\News\Endpoints\UpdateNewsEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

require_once __DIR__.'/../../../Fake/fake_role.php';

/**
 * @internal
 * @covers \Olz\News\Endpoints\UpdateNewsEndpoint
 */
final class UpdateNewsEndpointTest extends UnitTestCase {
    public function testUpdateNewsEndpointIdent(): void {
        $endpoint = new UpdateNewsEndpoint();
        $this->assertSame('UpdateNewsEndpoint', $endpoint->getIdent());
    }
}
