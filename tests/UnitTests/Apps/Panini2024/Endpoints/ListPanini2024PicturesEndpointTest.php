<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Panini2024\Endpoints;

use Olz\Apps\Panini2024\Endpoints\ListPanini2024PicturesEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Panini2024\Endpoints\ListPanini2024PicturesEndpoint
 */
final class ListPanini2024PicturesEndpointTest extends UnitTestCase {
    public function testListPanini2024PicturesEndpointIdent(): void {
        $endpoint = new ListPanini2024PicturesEndpoint();
        $this->assertSame('ListPanini2024PicturesEndpoint', $endpoint->getIdent());
    }

    // TODO: Tests
}
