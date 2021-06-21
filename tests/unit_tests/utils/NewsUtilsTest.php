<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../src/utils/NewsUtils.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \NewsUtils
 */
final class NewsUtilsTest extends UnitTestCase {
    public function testGetDefaultFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame(['typ' => 'aktuell', 'datum' => '2020'], $news_utils->getDefaultFilter());
    }

    public function testIsValidFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame(false, $news_utils->isValidFilter([]));
        $this->assertSame(false, $news_utils->isValidFilter(['foo' => 'bar']));
        $this->assertSame(true, $news_utils->isValidFilter(['typ' => 'aktuell', 'datum' => '2020']));
        $this->assertSame(false, $news_utils->isValidFilter(['typ' => 'some', 'datum' => 'rubbish']));
    }

    public function testDefaultFilterIsValid(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame(true, $news_utils->isValidFilter($news_utils->getDefaultFilter()));
    }

    public function testGetAllValidFilters(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['typ' => 'alle', 'datum' => '2020'],
            ['typ' => 'alle', 'datum' => '2019'],
            ['typ' => 'alle', 'datum' => '2018'],
            ['typ' => 'alle', 'datum' => '2017'],
            ['typ' => 'alle', 'datum' => '2016'],
            ['typ' => 'aktuell', 'datum' => '2020'],
            ['typ' => 'aktuell', 'datum' => '2019'],
            ['typ' => 'aktuell', 'datum' => '2018'],
            ['typ' => 'aktuell', 'datum' => '2017'],
            ['typ' => 'aktuell', 'datum' => '2016'],
        ], $news_utils->getAllValidFilters());
    }

    public function testGetUiTypeFilterOptions(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['selected' => true, 'new_filter' => ['typ' => 'alle', 'datum' => '2020'], 'name' => "Alle News", 'ident' => 'alle'],
            ['selected' => false, 'new_filter' => ['typ' => 'aktuell', 'datum' => '2020'], 'name' => "Aktuell", 'ident' => 'aktuell'],
        ], $news_utils->getUiTypeFilterOptions(['typ' => 'alle', 'datum' => '2020']));
        $this->assertSame([
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2020'], 'name' => "Alle News", 'ident' => 'alle'],
            ['selected' => true, 'new_filter' => ['typ' => 'aktuell', 'datum' => '2020'], 'name' => "Aktuell", 'ident' => 'aktuell'],
        ], $news_utils->getUiTypeFilterOptions(['typ' => 'aktuell', 'datum' => '2020']));
    }

    public function testGetUiDateRangeFilterOptions(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['selected' => true, 'new_filter' => ['typ' => 'alle', 'datum' => '2020'], 'name' => "2020", 'ident' => '2020'],
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2019'], 'name' => "2019", 'ident' => '2019'],
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2018'], 'name' => "2018", 'ident' => '2018'],
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2017'], 'name' => "2017", 'ident' => '2017'],
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2016'], 'name' => "2016", 'ident' => '2016'],
        ], $news_utils->getUiDateRangeFilterOptions(['typ' => 'alle', 'datum' => '2020']));
        $this->assertSame([
            ['selected' => false, 'new_filter' => ['typ' => 'aktuell', 'datum' => '2020'], 'name' => "2020", 'ident' => '2020'],
            ['selected' => false, 'new_filter' => ['typ' => 'aktuell', 'datum' => '2019'], 'name' => "2019", 'ident' => '2019'],
            ['selected' => true, 'new_filter' => ['typ' => 'aktuell', 'datum' => '2018'], 'name' => "2018", 'ident' => '2018'],
            ['selected' => false, 'new_filter' => ['typ' => 'aktuell', 'datum' => '2017'], 'name' => "2017", 'ident' => '2017'],
            ['selected' => false, 'new_filter' => ['typ' => 'aktuell', 'datum' => '2016'], 'name' => "2016", 'ident' => '2016'],
        ], $news_utils->getUiDateRangeFilterOptions(['typ' => 'aktuell', 'datum' => '2018']));
    }

    public function testGetDateRangeOptions(): void {
        $date_utils = new FixedDateUtils('2006-01-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['ident' => '2006', 'name' => "2006"],
            ['ident' => '2005', 'name' => "2005"],
            ['ident' => '2004', 'name' => "2004"],
            ['ident' => '2003', 'name' => "2003"],
            ['ident' => '2002', 'name' => "2002"],
        ], $news_utils->getDateRangeOptions());
    }

    public function testGetSqlFromFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame("'1'='0'", $news_utils->getSqlFromFilter([]));
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND ('1' = '1')",
            $news_utils->getSqlFromFilter(['typ' => 'alle', 'datum' => '2020'])
        );
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND (n.typ LIKE '%aktuell%')",
            $news_utils->getSqlFromFilter(['typ' => 'aktuell', 'datum' => '2020'])
        );
    }

    public function testGetTitleFromFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame("News", $news_utils->getTitleFromFilter([]));
        $this->assertSame(
            "News 2020",
            $news_utils->getTitleFromFilter(['typ' => 'alle', 'datum' => '2020'])
        );
        $this->assertSame(
            "Aktuell 2020",
            $news_utils->getTitleFromFilter(['typ' => 'aktuell', 'datum' => '2020'])
        );
        $this->assertSame(
            "News 2018",
            $news_utils->getTitleFromFilter(['typ' => 'alle', 'datum' => '2018'])
        );
        $this->assertSame(
            "Aktuell 2018",
            $news_utils->getTitleFromFilter(['typ' => 'aktuell', 'datum' => '2018'])
        );
    }
}
