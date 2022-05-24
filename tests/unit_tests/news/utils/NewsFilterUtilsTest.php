<?php

declare(strict_types=1);

use Doctrine\Common\Collections\Expr\Comparison;

require_once __DIR__.'/../../../../_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../../_/news/utils/NewsFilterUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \NewsFilterUtils
 */
final class NewsFilterUtilsTest extends UnitTestCase {
    public function testGetDefaultFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            'typ' => 'aktuell',
            'datum' => '2020',
            'archiv' => 'ohne',
        ], $news_utils->getDefaultFilter());
    }

    public function testIsValidFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame(false, $news_utils->isValidFilter([]));
        $this->assertSame(false, $news_utils->isValidFilter(['foo' => 'bar']));
        $this->assertSame(true, $news_utils->isValidFilter([
            'typ' => 'aktuell',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(false, $news_utils->isValidFilter([
            'typ' => 'aktuell',
            'datum' => '2011',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'typ' => 'aktuell',
            'datum' => '2011',
            'archiv' => 'mit',
        ]));
        $this->assertSame(false, $news_utils->isValidFilter([
            'typ' => 'some',
            'datum' => 'silly',
            'archiv' => 'rubbish',
        ]));
    }

    public function testDefaultFilterIsValid(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame(true, $news_utils->isValidFilter($news_utils->getDefaultFilter()));
    }

    public function testGetAllValidFiltersForSitemap(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'typ' => 'alle',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'alle',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'alle',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'alle',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'alle',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'aktuell',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'aktuell',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'aktuell',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'aktuell',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'aktuell',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
        ], $news_utils->getAllValidFiltersForSitemap());
    }

    public function testGetUiTypeFilterOptions(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "Alle News",
                'ident' => 'alle',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "Aktuell",
                'ident' => 'aktuell',
            ],
        ], $news_utils->getUiTypeFilterOptions([
            'typ' => 'alle',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Alle News",
                'ident' => 'alle',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Aktuell",
                'ident' => 'aktuell',
            ],
        ], $news_utils->getUiTypeFilterOptions([
            'typ' => 'aktuell',
            'datum' => '2020',
            'archiv' => 'mit',
        ]));
    }

    public function testGetUiDateRangeFilterOptionsExclArchive(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "2020",
                'ident' => '2020',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2019',
                    'archiv' => 'ohne',
                ],
                'name' => "2019",
                'ident' => '2019',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2018',
                    'archiv' => 'ohne',
                ],
                'name' => "2018",
                'ident' => '2018',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2017',
                    'archiv' => 'ohne',
                ],
                'name' => "2017",
                'ident' => '2017',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2016',
                    'archiv' => 'ohne',
                ],
                'name' => "2016",
                'ident' => '2016',
            ],
        ], $news_utils->getUiDateRangeFilterOptions([
            'typ' => 'alle',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
    }

    public function testGetUiDateRangeFilterOptionsInclArchive(): void {
        $date_utils = new FixedDateUtils('2011-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2011',
                    'archiv' => 'mit',
                ],
                'name' => "2011",
                'ident' => '2011',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2010',
                    'archiv' => 'mit',
                ],
                'name' => "2010",
                'ident' => '2010',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2009',
                    'archiv' => 'mit',
                ],
                'name' => "2009",
                'ident' => '2009',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2008',
                    'archiv' => 'mit',
                ],
                'name' => "2008",
                'ident' => '2008',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2007',
                    'archiv' => 'mit',
                ],
                'name' => "2007",
                'ident' => '2007',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2006',
                    'archiv' => 'mit',
                ],
                'name' => "2006",
                'ident' => '2006',
            ],
        ], $news_utils->getUiDateRangeFilterOptions([
            'typ' => 'aktuell',
            'datum' => '2009',
            'archiv' => 'mit',
        ]));
    }

    public function testGetUiArchiveFilterOptions(): void {
        $news_utils = new NewsFilterUtils();
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "ohne Archiv",
                'ident' => 'ohne',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "mit Archiv",
                'ident' => 'mit',
            ],
        ], $news_utils->getUiArchiveFilterOptions([
            'typ' => 'alle',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2018',
                    'archiv' => 'ohne',
                ],
                'name' => "ohne Archiv",
                'ident' => 'ohne',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'aktuell',
                    'datum' => '2018',
                    'archiv' => 'mit',
                ],
                'name' => "mit Archiv",
                'ident' => 'mit',
            ],
        ], $news_utils->getUiArchiveFilterOptions([
            'typ' => 'aktuell',
            'datum' => '2018',
            'archiv' => 'mit',
        ]));
    }

    public function testGetDateRangeOptionsExclArchive(): void {
        $date_utils = new FixedDateUtils('2006-01-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['ident' => '2006', 'name' => "2006"],
            ['ident' => '2005', 'name' => "2005"],
            ['ident' => '2004', 'name' => "2004"],
            ['ident' => '2003', 'name' => "2003"],
            ['ident' => '2002', 'name' => "2002"],
        ], $news_utils->getDateRangeOptions(['archiv' => 'ohne']));
    }

    public function testGetDateRangeOptionsInclArchive(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:00:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['ident' => '2020', 'name' => "2020"],
            ['ident' => '2019', 'name' => "2019"],
            ['ident' => '2018', 'name' => "2018"],
            ['ident' => '2017', 'name' => "2017"],
            ['ident' => '2016', 'name' => "2016"],
            ['ident' => '2015', 'name' => "2015"],
            ['ident' => '2014', 'name' => "2014"],
            ['ident' => '2013', 'name' => "2013"],
            ['ident' => '2012', 'name' => "2012"],
            ['ident' => '2011', 'name' => "2011"],
            ['ident' => '2010', 'name' => "2010"],
            ['ident' => '2009', 'name' => "2009"],
            ['ident' => '2008', 'name' => "2008"],
            ['ident' => '2007', 'name' => "2007"],
            ['ident' => '2006', 'name' => "2006"],
        ], $news_utils->getDateRangeOptions(['archiv' => 'mit']));
    }

    public function testGetSqlFromFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame("'1'='0'", $news_utils->getSqlFromFilter([]));
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND ('1' = '1')",
            $news_utils->getSqlFromFilter([
                'typ' => 'alle',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND (n.typ LIKE '%aktuell%')",
            $news_utils->getSqlFromFilter([
                'typ' => 'aktuell',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
    }

    public function testGetTitleFromFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame("News", $news_utils->getTitleFromFilter([]));
        $this->assertSame(
            "News 2020",
            $news_utils->getTitleFromFilter([
                'typ' => 'alle',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Aktuell 2020",
            $news_utils->getTitleFromFilter([
                'typ' => 'aktuell',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "News 2011 (Archiv)",
            $news_utils->getTitleFromFilter([
                'typ' => 'alle',
                'datum' => '2011',
                'archiv' => 'mit',
            ])
        );
        $this->assertSame(
            "Aktuell 2018",
            $news_utils->getTitleFromFilter([
                'typ' => 'aktuell',
                'datum' => '2018',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "News", // 2011 excl. archive is INVALID!
            $news_utils->getTitleFromFilter([
                'typ' => 'alle',
                'datum' => '2011',
                'archiv' => 'ohne',
            ])
        );
    }

    public function testIsFilterNotArchived(): void {
        $news_utils = new NewsFilterUtils();
        $this->assertSame(false, $news_utils->isFilterNotArchived([]));
        $this->assertSame(true, $news_utils->isFilterNotArchived(['archiv' => 'ohne']));
        $this->assertSame(false, $news_utils->isFilterNotArchived(['archiv' => 'mit']));
    }

    public function testGetIsNotArchivedCriteria(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $criteria_expression = $news_utils->getIsNotArchivedCriteria();
        $this->assertSame('datum', $criteria_expression->getField());
        $this->assertSame(
            '2016-01-01',
            $criteria_expression->getValue()->getValue()->format('Y-m-d')
        );
        $this->assertSame(Comparison::GTE, $criteria_expression->getOperator());
    }
}
