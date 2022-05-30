<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/utils/client/HttpUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \HttpUtils
 */
final class HttpUtilsTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(HttpUtils::class));
    }
}
