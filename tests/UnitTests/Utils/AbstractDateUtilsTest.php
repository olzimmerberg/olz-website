<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AbstractDateUtils;

class FakeDateUtils extends AbstractDateUtils {
    private $fixed_date;

    public function __construct($fixed_date) {
        $this->fixed_date = is_numeric($fixed_date) ? $fixed_date : strtotime($fixed_date);
    }

    public function getCurrentDateInFormat($format) {
        return date($format, $this->fixed_date);
    }
}

/**
 * @internal
 * @covers \Olz\Utils\AbstractDateUtils
 */
final class AbstractDateUtilsTest extends UnitTestCase {
    public function testGetIsoNow(): void {
        $date_utils = new FakeDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13 19:30:00', $date_utils->getIsoNow());
    }

    public function testGetIsoToday(): void {
        $date_utils = new FakeDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13', $date_utils->getIsoToday());
    }

    public function testOlzDateShort(): void {
        $date_utils = new FakeDateUtils('2020-08-13 19:30:00');
        $this->assertSame('Do., 13. Aug. 2020', $date_utils->olzDate('W., t. M jjjj'));
    }

    public function testOlzDateLong(): void {
        $date_utils = new FakeDateUtils('2020-08-13 19:30:00');
        $this->assertSame('Donnerstag, 13. August 2020', $date_utils->olzDate('WW, t. MM jjjj'));
    }

    public function testOlzDateFromDateTime(): void {
        $date_utils = new FakeDateUtils('2020-08-13 19:30:00');
        $datetime = new \DateTime('2020-03-13 19:30:00');
        $this->assertSame('Freitag, 13. März 2020', $date_utils->olzDate('WW, t. MM jjjj', $datetime));
    }

    public function testGetYearsForAccordion(): void {
        $date_utils = new FakeDateUtils('2020-03-13 19:30:00');
        $this->assertSame([2020, 2019, 2018, 2017, 2016, 2015], $date_utils->getYearsForAccordion());
    }
}
