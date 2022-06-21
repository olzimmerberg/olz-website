<?php

declare(strict_types=1);

use Olz\Utils\HttpUtils;

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \Olz\Utils\HttpUtils
 */
final class HttpUtilsTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(HttpUtils::class));
    }
}
