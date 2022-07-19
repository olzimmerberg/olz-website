<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Results\Endpoints;

use Olz\Apps\Results\Endpoints\UpdateResultsEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 * @covers \Olz\Apps\Results\Endpoints\UpdateResultsEndpoint
 */
final class UpdateResultsEndpointTest extends UnitTestCase {
    public function testUpdateResultsEndpointIdent(): void {
        $endpoint = new UpdateResultsEndpoint();
        $this->assertSame('UpdateResultsEndpoint', $endpoint->getIdent());
    }
}
