<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Import\Endpoints;

use Olz\Apps\Import\Endpoints\ImportTermineEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Import\Endpoints\ImportTermineEndpoint
 */
final class ImportTermineEndpointTest extends UnitTestCase {
    public function testImportTermineEndpointIdent(): void {
        $endpoint = new ImportTermineEndpoint();
        $this->assertSame('ImportTermineEndpoint', $endpoint->getIdent());
    }
}
