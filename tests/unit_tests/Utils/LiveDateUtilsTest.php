<?php

declare(strict_types=1);

use Olz\Utils\LiveDateUtils;

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \Olz\Utils\LiveDateUtils
 */
final class LiveDateUtilsTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(LiveDateUtils::class));
    }
}
