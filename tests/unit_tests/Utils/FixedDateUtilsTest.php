<?php

declare(strict_types=1);

use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \Olz\Utils\FixedDateUtils
 */
final class FixedDateUtilsTest extends UnitTestCase {
    public function testCurrentDateInFormat(): void {
        $fixed_date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13 19:30:00', $fixed_date_utils->getCurrentDateInFormat('Y-m-d H:i:s'));
        $this->assertSame('2020', $fixed_date_utils->getCurrentDateInFormat('Y'));
        $this->assertSame('03', $fixed_date_utils->getCurrentDateInFormat('m'));
        $this->assertSame('13', $fixed_date_utils->getCurrentDateInFormat('d'));
        $this->assertSame('19', $fixed_date_utils->getCurrentDateInFormat('H'));
        $this->assertSame('30', $fixed_date_utils->getCurrentDateInFormat('i'));
        $this->assertSame('00', $fixed_date_utils->getCurrentDateInFormat('s'));
    }
}
