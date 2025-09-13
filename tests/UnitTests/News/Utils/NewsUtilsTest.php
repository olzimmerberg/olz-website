<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Utils;

use Doctrine\Common\Collections\Expr\Comparison;
use Olz\News\Utils\NewsUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\News\Utils\NewsUtils
 */
final class NewsUtilsTest extends UnitTestCase {
    public function testGetDefaultFilter(): void {
        $news_utils = new NewsUtils();
        $this->assertSame([
            'format' => 'alle',
            'datum' => '2020',
        ], $news_utils->getDefaultFilter());
    }

    public function testIsValidFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $news_utils = new NewsUtils();
        $this->assertFalse($news_utils->isValidFilter(null));
        $this->assertFalse($news_utils->isValidFilter([]));
        $this->assertFalse($news_utils->isValidFilter(['foo' => 'bar']));
        $this->assertTrue($news_utils->isValidFilter([
            'format' => 'aktuell',
            'datum' => '2020',
        ]));
        $this->assertFalse($news_utils->isValidFilter([
            'format' => 'aktuell',
            'datum' => '2011',
        ]));
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => true];
        $this->assertTrue($news_utils->isValidFilter([
            'format' => 'aktuell',
            'datum' => '2011',
        ]));
    }

    public function testGetValidFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $news_utils = new NewsUtils();
        $this->assertSame([
            'format' => 'alle',
            'datum' => '2020',
        ], $news_utils->getValidFilter(null));
        $this->assertSame([
            'format' => 'alle',
            'datum' => '2020',
        ], $news_utils->getValidFilter([]));
        $this->assertSame([
            'format' => 'alle',
            'datum' => '2020',
        ], $news_utils->getValidFilter(['foo' => 'bar']));
        $this->assertSame([
            'format' => 'aktuell',
            'datum' => '2020',
        ], $news_utils->getValidFilter([
            'format' => 'aktuell',
            'datum' => '2020',
        ]));
        $this->assertSame([
            'format' => 'alle',
            'datum' => '2020',
        ], $news_utils->getValidFilter([
            'format' => 'aktuell',
            'datum' => '2011',
        ]));
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => true];
        $this->assertSame([
            'format' => 'aktuell',
            'datum' => '2011',
        ], $news_utils->getValidFilter([
            'format' => 'aktuell',
            'datum' => '2011',
        ]));
        $this->assertSame([
            'format' => 'alle',
            'datum' => '2020',
        ], $news_utils->getValidFilter([
            'format' => 'some',
            'datum' => 'silly',
            'archiv' => 'rubbish',
        ]));
    }

    public function testDefaultFilterIsValid(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $news_utils = new NewsUtils();
        $this->assertTrue($news_utils->isValidFilter($news_utils->getDefaultFilter()));
    }

    public function testGetAllValidFiltersForSitemap(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $news_utils = new NewsUtils();
        $this->assertSame([
            [
                'format' => 'alle',
                'datum' => '2020',
            ],
            [
                'format' => 'alle',
                'datum' => '2019',
            ],
            [
                'format' => 'alle',
                'datum' => '2018',
            ],
            [
                'format' => 'alle',
                'datum' => '2017',
            ],
            [
                'format' => 'alle',
                'datum' => '2016',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2020',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2019',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2018',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2017',
            ],
            [
                'format' => 'aktuell',
                'datum' => '2016',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2020',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2019',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2018',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2017',
            ],
            [
                'format' => 'kaderblog',
                'datum' => '2016',
            ],
            [
                'format' => 'forum',
                'datum' => '2020',
            ],
            [
                'format' => 'forum',
                'datum' => '2019',
            ],
            [
                'format' => 'forum',
                'datum' => '2018',
            ],
            [
                'format' => 'forum',
                'datum' => '2017',
            ],
            [
                'format' => 'forum',
                'datum' => '2016',
            ],
            [
                'format' => 'galerie',
                'datum' => '2020',
            ],
            [
                'format' => 'galerie',
                'datum' => '2019',
            ],
            [
                'format' => 'galerie',
                'datum' => '2018',
            ],
            [
                'format' => 'galerie',
                'datum' => '2017',
            ],
            [
                'format' => 'galerie',
                'datum' => '2016',
            ],
            [
                'format' => 'video',
                'datum' => '2020',
            ],
            [
                'format' => 'video',
                'datum' => '2019',
            ],
            [
                'format' => 'video',
                'datum' => '2018',
            ],
            [
                'format' => 'video',
                'datum' => '2017',
            ],
            [
                'format' => 'video',
                'datum' => '2016',
            ],
        ], $news_utils->getAllValidFiltersForSitemap());
    }

    public function testGetUiFormatFilterOptions(): void {
        $news_utils = new NewsUtils();
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2020',
                ],
                'name' => "Alle",
                'icon' => null,
                'ident' => 'alle',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2020',
                ],
                'name' => "Aktuell",
                'icon' => 'entry_type_aktuell_20.svg',
                'ident' => 'aktuell',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'kaderblog',
                    'datum' => '2020',
                ],
                'name' => "Kaderblog",
                'icon' => 'entry_type_kaderblog_20.svg',
                'ident' => 'kaderblog',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'forum',
                    'datum' => '2020',
                ],
                'name' => "Forum",
                'icon' => 'entry_type_forum_20.svg',
                'ident' => 'forum',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'galerie',
                    'datum' => '2020',
                ],
                'name' => "Galerien",
                'icon' => 'entry_type_gallery_20.svg',
                'ident' => 'galerie',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'video',
                    'datum' => '2020',
                ],
                'name' => "Videos",
                'icon' => 'entry_type_movie_20.svg',
                'ident' => 'video',
            ],
        ], $news_utils->getUiFormatFilterOptions([
            'format' => 'alle',
            'datum' => '2020',
        ]));
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2020',
                ],
                'name' => "Alle",
                'icon' => null,
                'ident' => 'alle',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2020',
                ],
                'name' => "Aktuell",
                'icon' => 'entry_type_aktuell_20.svg',
                'ident' => 'aktuell',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'kaderblog',
                    'datum' => '2020',
                ],
                'name' => "Kaderblog",
                'icon' => 'entry_type_kaderblog_20.svg',
                'ident' => 'kaderblog',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'forum',
                    'datum' => '2020',
                ],
                'name' => "Forum",
                'icon' => 'entry_type_forum_20.svg',
                'ident' => 'forum',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'galerie',
                    'datum' => '2020',
                ],
                'name' => "Galerien",
                'icon' => 'entry_type_gallery_20.svg',
                'ident' => 'galerie',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'video',
                    'datum' => '2020',
                ],
                'name' => "Videos",
                'icon' => 'entry_type_movie_20.svg',
                'ident' => 'video',
            ],
        ], $news_utils->getUiFormatFilterOptions([
            'format' => 'aktuell',
            'datum' => '2020',
        ]));
    }

    public function testGetUiDateRangeFilterOptionsExclArchive(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $news_utils = new NewsUtils();
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2020',
                ],
                'name' => "2020",
                'ident' => '2020',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2019',
                ],
                'name' => "2019",
                'ident' => '2019',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2018',
                ],
                'name' => "2018",
                'ident' => '2018',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2017',
                ],
                'name' => "2017",
                'ident' => '2017',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'alle',
                    'datum' => '2016',
                ],
                'name' => "2016",
                'ident' => '2016',
            ],
        ], $news_utils->getUiDateRangeFilterOptions([
            'format' => 'alle',
            'datum' => '2020',
        ]));
    }

    public function testGetUiDateRangeFilterOptionsInclArchive(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => true];
        $date_utils = new DateUtils('2011-03-13 19:30:00');
        $news_utils = new NewsUtils();
        $news_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2011',
                ],
                'name' => "2011",
                'ident' => '2011',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2010',
                ],
                'name' => "2010",
                'ident' => '2010',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2009',
                ],
                'name' => "2009",
                'ident' => '2009',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2008',
                ],
                'name' => "2008",
                'ident' => '2008',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2007',
                ],
                'name' => "2007",
                'ident' => '2007',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'format' => 'aktuell',
                    'datum' => '2006',
                ],
                'name' => "2006",
                'ident' => '2006',
            ],
        ], $news_utils->getUiDateRangeFilterOptions([
            'format' => 'aktuell',
            'datum' => '2009',
        ]));
    }

    public function testGetDateRangeOptionsExclArchive(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $date_utils = new DateUtils('2006-01-13 19:30:00');
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

    public function testGetDateRangeOptionsInclArchive(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => true];
        $date_utils = new DateUtils('2020-03-13 19:00:00');
        $news_utils = new NewsUtils();
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
        ], $news_utils->getDateRangeOptions());
    }

    public function testGetSqlFromFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $news_utils = new NewsUtils();
        $this->assertSame(
            "(YEAR(n.published_date) = '2020') AND ('1' = '1')",
            $news_utils->getSqlFromFilter([
                'format' => 'alle',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "(YEAR(n.published_date) = '2020') AND (n.format LIKE '%aktuell%')",
            $news_utils->getSqlFromFilter([
                'format' => 'aktuell',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "(YEAR(n.published_date) = '2020') AND (n.format LIKE '%kaderblog%')",
            $news_utils->getSqlFromFilter([
                'format' => 'kaderblog',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "(YEAR(n.published_date) = '2020') AND (n.format LIKE '%forum%')",
            $news_utils->getSqlFromFilter([
                'format' => 'forum',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "(YEAR(n.published_date) = '2020') AND (n.format LIKE '%galerie%')",
            $news_utils->getSqlFromFilter([
                'format' => 'galerie',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "(YEAR(n.published_date) = '2020') AND (n.format LIKE '%video%')",
            $news_utils->getSqlFromFilter([
                'format' => 'video',
                'datum' => '2020',
            ])
        );
    }

    public function testGetTitleFromFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $news_utils = new NewsUtils();
        $this->assertSame(
            "News",
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Aktuell",
            $news_utils->getTitleFromFilter([
                'format' => 'aktuell',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Kaderblog",
            $news_utils->getTitleFromFilter([
                'format' => 'kaderblog',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Forum",
            $news_utils->getTitleFromFilter([
                'format' => 'forum',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Galerien",
            $news_utils->getTitleFromFilter([
                'format' => 'galerie',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Videos",
            $news_utils->getTitleFromFilter([
                'format' => 'video',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "News von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2019',
            ])
        );
        $this->assertSame(
            "Aktuelles von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'aktuell',
                'datum' => '2019',
            ])
        );
        $this->assertSame(
            "Kaderblog von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'kaderblog',
                'datum' => '2019',
            ])
        );
        $this->assertSame(
            "Forumseinträge von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'forum',
                'datum' => '2019',
            ])
        );
        $this->assertSame(
            "Galerien von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'galerie',
                'datum' => '2019',
            ])
        );
        $this->assertSame(
            "Videos von 2019",
            $news_utils->getTitleFromFilter([
                'format' => 'video',
                'datum' => '2019',
            ])
        );
        $this->assertSame(
            "Aktuelles von 2018",
            $news_utils->getTitleFromFilter([
                'format' => 'aktuell',
                'datum' => '2018',
            ])
        );
        $this->assertSame(
            "News", // 2011 excl. archive is INVALID!
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2011',
            ])
        );
        $this->assertSame(
            "News", // 2000 incl. archive is INVALID!
            $news_utils->getTitleFromFilter([
                'format' => 'alle',
                'datum' => '2000',
            ])
        );
    }

    public function testGetIsNotArchivedCriteria(): void {
        $news_utils = new NewsUtils();
        $criteria_expression = $news_utils->getIsNotArchivedCriteria();
        $this->assertSame('published_date', $criteria_expression->getField());
        $this->assertSame(
            '2016-01-01',
            $criteria_expression->getValue()->getValue()->format('Y-m-d')
        );
        $this->assertSame(Comparison::GTE, $criteria_expression->getOperator());
    }

    public function testGetValidNewsFormatIcon(): void {
        $news_utils = new NewsUtils();
        $this->assertSame('/_/assets/icns/entry_type_aktuell_20.svg', $news_utils->getNewsFormatIcon('aktuell'));
        $this->assertSame('/_/assets/icns/entry_type_forum_20.svg', $news_utils->getNewsFormatIcon('forum'));
        $this->assertSame('/_/assets/icns/entry_type_gallery_20.svg', $news_utils->getNewsFormatIcon('galerie'));
        $this->assertSame('/_/assets/icns/entry_type_kaderblog_20.svg', $news_utils->getNewsFormatIcon('kaderblog'));
        $this->assertSame('/_/assets/icns/entry_type_movie_20.svg', $news_utils->getNewsFormatIcon('video'));
    }

    public function testGetInvalidNewsFormatIcon(): void {
        $news_utils = new NewsUtils();
        $this->assertNull($news_utils->getNewsFormatIcon(''));
        $this->assertNull($news_utils->getNewsFormatIcon('invalid'));
    }
}
