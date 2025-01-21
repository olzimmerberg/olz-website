<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HttpParams;

/**
 * @internal
 *
 * @coversNothing
 */
final class HttpParamsTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(HttpParams::class));
    }
}
