<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\News\Endpoints\DeleteNewsEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

require_once __DIR__.'/../../../Fake/fake_role.php';

/**
 * @internal
 * @covers \Olz\News\Endpoints\DeleteNewsEndpoint
 */
final class DeleteNewsEndpointTest extends UnitTestCase {
    public function testDeleteNewsEndpointIdent(): void {
        $endpoint = new DeleteNewsEndpoint();
        $this->assertSame('DeleteNewsEndpoint', $endpoint->getIdent());
    }
}
