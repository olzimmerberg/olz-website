<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Anniversary\Utils;

use Olz\Anniversary\Utils\AnniversaryUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Anniversary\Utils\AnniversaryUtils
 */
final class AnniversaryUtilsTest extends UnitTestCase {
    public function testGetElevationStats(): void {
        $this->assertSame([
            'completion' => 0.0,
            'diffMeters' => 0.0,
            'diffDays' => 0.0,
            'diffKind' => 'ahead',
        ], (new AnniversaryUtils())->getElevationStats());
    }
}
