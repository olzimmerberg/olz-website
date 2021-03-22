<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/date/DateUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeDateUtils extends DateUtils {
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
 * @covers \DateUtils
 */
final class DateUtilsTest extends UnitTestCase {
    public function testGetIsoNow(): void {
        $date_utils = new FakeDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13 19:30:00', $date_utils->getIsoNow());
    }

    public function testGetIsoToday(): void {
        $date_utils = new FakeDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13', $date_utils->getIsoToday());
    }

    public function testOlzDate(): void {
        $date_utils = new FakeDateUtils('2020-03-13 19:30:00');
        $this->assertSame('Fr, 13. März. 2020', $date_utils->olzDate('W, t. M. jjjj'));
    }

    public function testGetYearsForAccordion(): void {
        $date_utils = new FakeDateUtils('2020-03-13 19:30:00');
        $this->assertSame([2020, 2019, 2018, 2017, 2016, 2015], $date_utils->getYearsForAccordion());
    }
}
