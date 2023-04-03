<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Utils;

use Doctrine\Common\Collections\Expr\Comparison;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\News\Utils\NewsFilterUtils
 */
final class NewsFilterUtilsTest extends UnitTestCase {
    public function testGetDefaultFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            'format' => 'alle',
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
            'format' => 'aktuell',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(false, $news_utils->isValidFilter([
            'format' => 'aktuell',
            'datum' => '2011',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'format' => 'aktuell',
            'datum' => '2011',
            'archiv' => 'mit',
        ]));
        $this->assertSame(false, $news_utils->isValidFilter([
            'format' => 'some',
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
                'format' => 'alle',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'alle',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'alle',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'alle',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'alle',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'forum',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'forum',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'forum',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'forum',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'forum',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'galerie',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'galerie',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'galerie',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'galerie',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'galerie',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'video',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'video',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'video',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'video',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'format' => 'video',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
        ], $news_utils->getAllValidFiltersForSitemap());
    }

    public function testGetUiFormatFilterOptions(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $news_utils = new NewsFilterUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "Alle",
                'ident' => 'alle',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "Aktuell",
                'ident' => 'aktuell',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'kaderblog',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "Kaderblog",
                'ident' => 'kaderblog',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'forum',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "Forum",
                'ident' => 'forum',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'galerie',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "Galerien",
                'ident' => 'galerie',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'video',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "Videos",
                'ident' => 'video',
            ],
        ], $news_utils->getUiFormatFilterOptions([
            'format' => 'alle',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Alle",
                'ident' => 'alle',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Aktuell",
                'ident' => 'aktuell',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'kaderblog',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Kaderblog",
                'ident' => 'kaderblog',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'forum',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Forum",
                'ident' => 'forum',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'galerie',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Galerien",
                'ident' => 'galerie',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'video',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Videos",
                'ident' => 'video',
            ],
        ], $news_utils->getUiFormatFilterOptions([
            'format' => 'aktuell',
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
                    'format' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "2020",
                'ident' => '2020',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2019',
                    'archiv' => 'ohne',
                ],
                'name' => "2019",
                'ident' => '2019',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2018',
                    'archiv' => 'ohne',
                ],
                'name' => "2018",
                'ident' => '2018',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2017',
                    'archiv' => 'ohne',
                ],
                'name' => "2017",
                'ident' => '2017',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2016',
                    'archiv' => 'ohne',
                ],
                'name' => "2016",
                'ident' => '2016',
            ],
        ], $news_utils->getUiDateRangeFilterOptions([
            'format' => 'alle',
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
                    'format' => 'aktuell',
                    'datum' => '2011',
                    'archiv' => 'mit',
                ],
                'name' => "2011",
                'ident' => '2011',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2010',
                    'archiv' => 'mit',
                ],
                'name' => "2010",
                'ident' => '2010',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2009',
                    'archiv' => 'mit',
                ],
                'name' => "2009",
                'ident' => '2009',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2008',
                    'archiv' => 'mit',
                ],
                'name' => "2008",
                'ident' => '2008',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2007',
                    'archiv' => 'mit',
                ],
                'name' => "2007",
                'ident' => '2007',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2006',
                    'archiv' => 'mit',
                ],
                'name' => "2006",
                'ident' => '2006',
            ],
        ], $news_utils->getUiDateRangeFilterOptions([
            'format' => 'aktuell',
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
                    'format' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "ohne Archiv",
                'ident' => 'ohne',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "mit Archiv",
                'ident' => 'mit',
            ],
        ], $news_utils->getUiArchiveFilterOptions([
            'format' => 'alle',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2018',
                    'archiv' => 'ohne',
                ],
                'name' => "ohne Archiv",
                'ident' => 'ohne',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2018',
                    'archiv' => 'mit',
                ],
                'name' => "mit Archiv",
                'ident' => 'mit',
            ],
        ], $news_utils->getUiArchiveFilterOptions([
            'format' => 'aktuell',
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
                'format' => 'alle',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND (n.typ LIKE '%aktuell%')",
            $news_utils->getSqlFromFilter([
                'format' => 'aktuell',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND (n.typ LIKE '%kaderblog%')",
            $news_utils->getSqlFromFilter([
                'format' => 'kaderblog',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND (n.typ LIKE '%forum%')",
            $news_utils->getSqlFromFilter([
                'format' => 'forum',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND (n.typ LIKE '%galerie%')",
            $news_utils->getSqlFromFilter([
                'format' => 'galerie',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "(YEAR(n.datum) = '2020') AND (n.typ LIKE '%video%')",
            $news_utils->getSqlFromFilter([
                'format' => 'video',
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
            "News",
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Aktuell",
            $news_utils->getTitleFromFilter([
                'format' => 'aktuell',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Kaderblog",
            $news_utils->getTitleFromFilter([
                'format' => 'kaderblog',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Forum",
            $news_utils->getTitleFromFilter([
                'format' => 'forum',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Galerien",
            $news_utils->getTitleFromFilter([
                'format' => 'galerie',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Videos",
            $news_utils->getTitleFromFilter([
                'format' => 'video',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "News von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2019',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Aktuelles von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'aktuell',
                'datum' => '2019',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Kaderblog von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'kaderblog',
                'datum' => '2019',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Forumseinträge von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'forum',
                'datum' => '2019',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Galerien von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'galerie',
                'datum' => '2019',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Videos von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'video',
                'datum' => '2019',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "News von 2011 (Archiv)",
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2011',
                'archiv' => 'mit',
            ])
        );
        $this->assertSame(
            "Aktuelles von 2018",
            $news_utils->getTitleFromFilter([
                'format' => 'aktuell',
                'datum' => '2018',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "News", // 2011 excl. archive is INVALID!
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2011',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "News", // 2000 incl. archive is INVALID!
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2000',
                'archiv' => 'mit',
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
