<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HttpUtils;

/**
 * @internal
 * @covers \Olz\Utils\HttpUtils
 */
final class HttpUtilsTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(HttpUtils::class));
    }
}
