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
        $utils = $this->getSut();

        $this->assertMatchesRegularExpression('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $utils->getCurrentDateInFormat('Y-m-d H:i:s'));
        $this->assertGreaterThanOrEqual('2020', $utils->getCurrentDateInFormat('Y'));
        $this->assertGreaterThanOrEqual('01', $utils->getCurrentDateInFormat('m'));
        $this->assertLessThanOrEqual('12', $utils->getCurrentDateInFormat('m'));
        $this->assertGreaterThanOrEqual('01', $utils->getCurrentDateInFormat('d'));
        $this->assertLessThanOrEqual('31', $utils->getCurrentDateInFormat('d'));
        $this->assertGreaterThanOrEqual('00', $utils->getCurrentDateInFormat('H'));
        $this->assertLessThanOrEqual('23', $utils->getCurrentDateInFormat('H'));
        $this->assertGreaterThanOrEqual('00', $utils->getCurrentDateInFormat('i'));
        $this->assertLessThanOrEqual('59', $utils->getCurrentDateInFormat('i'));
        $this->assertGreaterThanOrEqual('00', $utils->getCurrentDateInFormat('s'));
        $this->assertLessThanOrEqual('59', $utils->getCurrentDateInFormat('s'));
    }

    protected function getSut(): DateUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(DateUtils::class);
    }
}
