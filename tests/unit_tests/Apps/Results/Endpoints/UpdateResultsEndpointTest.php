<?php

declare(strict_types=1);

use Olz\Apps\Results\Endpoints\UpdateResultsEndpoint;

require_once __DIR__.'/../../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../../fake/FakeIdUtils.php';
require_once __DIR__.'/../../../common/UnitTestCase.php';

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
