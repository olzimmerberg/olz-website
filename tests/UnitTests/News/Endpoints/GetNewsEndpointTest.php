<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\News\Endpoints\GetNewsEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

require_once __DIR__.'/../../../Fake/fake_role.php';

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\GetNewsEndpoint
 */
final class GetNewsEndpointTest extends UnitTestCase {
    public function testGetNewsEndpointIdent(): void {
        $endpoint = new GetNewsEndpoint();
        $this->assertSame('GetNewsEndpoint', $endpoint->getIdent());
    }
}
