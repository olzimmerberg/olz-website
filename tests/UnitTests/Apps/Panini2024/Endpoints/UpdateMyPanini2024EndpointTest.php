<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Panini2024\Endpoints;

use Olz\Apps\Panini2024\Endpoints\UpdateMyPanini2024Endpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Panini2024\Endpoints\UpdateMyPanini2024Endpoint
 */
final class UpdateMyPanini2024EndpointTest extends UnitTestCase {
    public function testUpdateMyPanini2024EndpointIdent(): void {
        $endpoint = new UpdateMyPanini2024Endpoint();
        $this->assertTrue((bool) $endpoint);
    }

    // TODO: Tests
}
