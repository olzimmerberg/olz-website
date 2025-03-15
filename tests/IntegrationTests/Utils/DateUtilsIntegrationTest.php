<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\DateUtils
 */
final class DateUtilsIntegrationTest extends IntegrationTestCase {
    public function testCurrentDateInFormat(): void {
        $live_date_utils = new DateUtils();
        $this->assertMatchesRegularExpression('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $live_date_utils->getCurrentDateInFormat('Y-m-d H:i:s'));
        $this->assertGreaterThanOrEqual('2020', $live_date_utils->getCurrentDateInFormat('Y'));
        $this->assertGreaterThanOrEqual('01', $live_date_utils->getCurrentDateInFormat('m'));
        $this->assertLessThanOrEqual('12', $live_date_utils->getCurrentDateInFormat('m'));
        $this->assertGreaterThanOrEqual('01', $live_date_utils->getCurrentDateInFormat('d'));
        $this->assertLessThanOrEqual('31', $live_date_utils->getCurrentDateInFormat('d'));
        $this->assertGreaterThanOrEqual('00', $live_date_utils->getCurrentDateInFormat('H'));
        $this->assertLessThanOrEqual('23', $live_date_utils->getCurrentDateInFormat('H'));
        $this->assertGreaterThanOrEqual('00', $live_date_utils->getCurrentDateInFormat('i'));
        $this->assertLessThanOrEqual('59', $live_date_utils->getCurrentDateInFormat('i'));
        $this->assertGreaterThanOrEqual('00', $live_date_utils->getCurrentDateInFormat('s'));
        $this->assertLessThanOrEqual('59', $live_date_utils->getCurrentDateInFormat('s'));
    }
}
