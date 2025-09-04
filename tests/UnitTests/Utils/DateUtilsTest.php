<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;

class TestOnlyDateUtils extends DateUtils {
    private int $fixed_date;

    public function __construct(string $fixed_date) {
        $this->fixed_date = strtotime($fixed_date) ?: 0;
    }

    public function getCurrentDateInFormat(string $format): string {
        return date($format, $this->fixed_date);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\DateUtils
 */
final class DateUtilsTest extends UnitTestCase {
    public function testGetIsoArchiveThreshold(): void {
        $date_utils = new TestOnlyDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2016-01-01', $date_utils->getIsoArchiveThreshold());
    }

    public function testGetIsoNow(): void {
        $date_utils = new TestOnlyDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13 19:30:00', $date_utils->getIsoNow());
    }

    public function testGetIsoToday(): void {
        $date_utils = new TestOnlyDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13', $date_utils->getIsoToday());
    }

    public function testIsoDateTime(): void {
        $date_utils = new TestOnlyDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13 19:30:00', $date_utils->isoDateTime());
        $this->assertSame('2020-03-13 19:30:00', $date_utils->isoDateTime(null));
        $this->assertSame('2020-08-15 00:00:00', $date_utils->isoDateTime('2020-08-15'));
        $this->assertSame('2020-08-15 00:00:00', $date_utils->isoDateTime(new \DateTime('2020-08-15')));
        $this->assertSame('2020-08-15 19:30:00', $date_utils->isoDateTime('2020-08-15 19:30:00'));
        $this->assertSame('2020-08-15 19:30:00', $date_utils->isoDateTime(new \DateTime('2020-08-15 19:30:00')));
    }

    public function testIsoDate(): void {
        $date_utils = new TestOnlyDateUtils('2020-03-13 19:30:00');
        $this->assertSame('2020-03-13', $date_utils->isoDate());
        $this->assertSame('2020-03-13', $date_utils->isoDate(null));
        $this->assertSame('2020-08-15', $date_utils->isoDate('2020-08-15'));
        $this->assertSame('2020-08-15', $date_utils->isoDate(new \DateTime('2020-08-15')));
        $this->assertSame('2020-08-15', $date_utils->isoDate('2020-08-15 19:30:00'));
        $this->assertSame('2020-08-15', $date_utils->isoDate(new \DateTime('2020-08-15 19:30:00')));
    }

    public function testCompactDate(): void {
        $date_utils = new TestOnlyDateUtils('2020-03-13 19:30:00');
        $this->assertSame('Fr, 13.03.', $date_utils->compactDate());
        $this->assertSame('Fr, 13.03.', $date_utils->compactDate(null));
        $this->assertSame('Sa, 15.08.', $date_utils->compactDate('2020-08-15'));
        $this->assertSame('Sa, 15.08.', $date_utils->compactDate(new \DateTime('2020-08-15')));
        $this->assertSame('Sa, 15.08.', $date_utils->compactDate('2020-08-15 19:30:00'));
        $this->assertSame('Sa, 15.08.', $date_utils->compactDate(new \DateTime('2020-08-15 19:30:00')));
    }

    public function testCompactTime(): void {
        $date_utils = new TestOnlyDateUtils('2020-03-13 19:30:00');
        $this->assertSame('19:30', $date_utils->compactTime());
        $this->assertSame('19:30', $date_utils->compactTime(null));
        $this->assertSame('00:00', $date_utils->compactTime('2020-08-15'));
        $this->assertSame('00:00', $date_utils->compactTime(new \DateTime('2020-08-15')));
        $this->assertSame('19:30', $date_utils->compactTime('2020-08-15 19:30:00'));
        $this->assertSame('19:30', $date_utils->compactTime(new \DateTime('2020-08-15 19:30:00')));
    }

    public function testOlzDateShort(): void {
        $date_utils = new TestOnlyDateUtils('2020-08-13 19:30:00');
        $this->assertSame('Do., 13. Aug. 2020', $date_utils->olzDate('W., t. M jjjj'));
    }

    public function testOlzDateLong(): void {
        $date_utils = new TestOnlyDateUtils('2020-08-13 19:30:00');
        $this->assertSame('Donnerstag, 13. August 2020', $date_utils->olzDate('WW, t. MM jjjj'));
    }

    public function testOlzDateFromDateTime(): void {
        $date_utils = new TestOnlyDateUtils('2020-08-13 19:30:00');
        $datetime = new \DateTime('2020-03-13 19:30:00');
        $this->assertSame('Freitag, 13. März 2020', $date_utils->olzDate('WW, t. MM jjjj', $datetime));
    }

    public function testFormatDateTimeRange(): void {
        $date_utils = new TestOnlyDateUtils('2020-03-13 19:30:00');

        $date = ['2020-03-13', null, null, null];
        $time = ['2020-03-13', '18:00:00', null, null];
        $time_range_within_day_1 = ['2020-03-13', '18:00:00', null, '19:30:00'];
        $time_range_within_day_2 = ['2020-03-13', '18:00:00', '2020-03-13', '19:30:00'];
        $date_range_within_month = ['2020-03-13', null, '2020-03-16', null];
        $time_range_within_month_1 = ['2020-03-13', '15:00:00', '2020-03-16', null];
        $time_range_within_month_2 = ['2020-03-13', '15:00:00', '2020-03-16', '09:00:00'];
        $date_range_within_year = ['2020-03-13', null, '2020-05-11', null];
        $time_range_within_year_1 = ['2020-03-13', '15:00:00', '2020-05-11', null];
        $time_range_within_year_2 = ['2020-03-13', '15:00:00', '2020-05-11', '09:00:00'];
        $date_range_across_years = ['2020-03-16', null, '2021-03-16', null];
        $time_range_across_years_1 = ['2020-03-16', '12:34:56', '2021-03-16', null];
        $time_range_across_years_2 = ['2020-03-16', '12:34:56', '2021-03-16', '23:59:59'];

        $this->assertSame(
            'Freitag, 13. März 2020',
            $date_utils->formatDateTimeRange(...$date),
        );
        $this->assertSame(
            'Freitag, 13. März 2020 18:00',
            $date_utils->formatDateTimeRange(...$time),
        );
        $this->assertSame(
            'Freitag, 13. März 2020 18:00 – 19:30',
            $date_utils->formatDateTimeRange(...$time_range_within_day_1),
        );
        $this->assertSame(
            'Freitag, 13. März 2020 18:00 – 19:30',
            $date_utils->formatDateTimeRange(...$time_range_within_day_2),
        );
        $this->assertSame(
            'Freitag – Montag, 13. – 16. März 2020',
            $date_utils->formatDateTimeRange(...$date_range_within_month),
        );
        $this->assertSame(
            'Freitag – Montag, 13. – 16. März 2020 15:00',
            $date_utils->formatDateTimeRange(...$time_range_within_month_1),
        );
        $this->assertSame(
            'Freitag – Montag, 13. – 16. März 2020 15:00 – 09:00',
            $date_utils->formatDateTimeRange(...$time_range_within_month_2),
        );
        $this->assertSame(
            'Freitag – Montag, 13. März – 11. Mai 2020',
            $date_utils->formatDateTimeRange(...$date_range_within_year),
        );
        $this->assertSame(
            'Freitag – Montag, 13. März – 11. Mai 2020 15:00',
            $date_utils->formatDateTimeRange(...$time_range_within_year_1),
        );
        $this->assertSame(
            'Freitag – Montag, 13. März – 11. Mai 2020 15:00 – 09:00',
            $date_utils->formatDateTimeRange(...$time_range_within_year_2),
        );
        $this->assertSame(
            'Montag – Dienstag, 16. März 2020 – 16. März 2021',
            $date_utils->formatDateTimeRange(...$date_range_across_years),
        );
        $this->assertSame(
            'Montag – Dienstag, 16. März 2020 – 16. März 2021 12:34',
            $date_utils->formatDateTimeRange(...$time_range_across_years_1),
        );
        $this->assertSame(
            'Montag – Dienstag, 16. März 2020 – 16. März 2021 12:34 – 23:59',
            $date_utils->formatDateTimeRange(...$time_range_across_years_2),
        );
    }

    public function testParseDateTimeRange(): void {
        $date_utils = new DateUtils('2020-08-13 19:30:00');
        $this->assertEquals([
            'start' => new \DateTime('2020-01-01'),
            'end' => new \DateTime('2021-01-01'),
        ], $date_utils->parseDateTimeRange('2020'));
        $this->assertEquals([
            'start' => new \DateTime('2020-03-13'),
            'end' => new \DateTime('2020-03-14'),
        ], $date_utils->parseDateTimeRange('2020-03-13'));
        $this->assertEquals([
            'start' => new \DateTime('2020-08-05'),
            'end' => new \DateTime('2020-08-06'),
        ], $date_utils->parseDateTimeRange('5.8.2020'));
        $this->assertNull($date_utils->parseDateTimeRange('not-a-date'));
    }
}
