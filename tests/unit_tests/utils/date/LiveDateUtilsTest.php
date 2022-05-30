<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/utils/date/LiveDateUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \LiveDateUtils
 */
final class LiveDateUtilsTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(LiveDateUtils::class));
    }
}
