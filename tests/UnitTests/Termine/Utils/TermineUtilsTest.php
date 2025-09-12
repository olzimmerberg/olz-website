<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Utils;

use Doctrine\Common\Collections\Expr\Comparison;
use Olz\Entity\Termine\Termin;
use Olz\Termine\Utils\TermineUtils;
use Olz\Tests\Fake\Entity\FakeSolvEvent;
use Olz\Tests\Fake\Entity\Termine\FakeTermin;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Termine\Utils\TermineUtils
 */
final class TermineUtilsTest extends UnitTestCase {
    public function testGetDefaultFilter(): void {
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertSame([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
        ], $termine_utils->getDefaultFilter());
    }

    public function testIsValidFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertFalse($termine_utils->isValidFilter(null));
        $this->assertFalse($termine_utils->isValidFilter([]));
        $this->assertFalse($termine_utils->isValidFilter(['foo' => 'bar']));
        $this->assertTrue($termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2020',
        ]));
        $this->assertFalse($termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2011',
        ]));
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => true];
        $this->assertTrue($termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2011',
        ]));
    }

    public function testGetValidFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertSame([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
        ], $termine_utils->getValidFilter(null));
        $this->assertSame([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
        ], $termine_utils->getValidFilter([]));
        $this->assertSame([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
        ], $termine_utils->getValidFilter(['foo' => 'bar']));
        $this->assertSame([
            'typ' => 'alle',
            'datum' => '2020',
        ], $termine_utils->getValidFilter([
            'typ' => 'alle',
            'datum' => '2020',
        ]));
        $this->assertSame([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
        ], $termine_utils->getValidFilter([
            'typ' => 'alle',
            'datum' => '2011',
        ]));
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => true];
        $this->assertSame([
            'typ' => 'alle',
            'datum' => '2011',
        ], $termine_utils->getValidFilter([
            'typ' => 'alle',
            'datum' => '2011',
        ]));
    }

    public function testDefaultFilterIsValid(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertTrue($termine_utils->isValidFilter($termine_utils->getDefaultFilter()));
    }

    public function testGetAllValidFiltersForSitemap(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertSame([
            [
                'typ' => 'alle',
                'datum' => 'bevorstehend',
            ],
            [
                'typ' => 'alle',
                'datum' => '2021',
            ],
            [
                'typ' => 'alle',
                'datum' => '2020',
            ],
            [
                'typ' => 'alle',
                'datum' => '2019',
            ],
            [
                'typ' => 'alle',
                'datum' => '2018',
            ],
            [
                'typ' => 'alle',
                'datum' => '2017',
            ],
            [
                'typ' => 'alle',
                'datum' => '2016',
            ],
            [
                'typ' => 'programm',
                'datum' => 'bevorstehend',
            ],
            [
                'typ' => 'programm',
                'datum' => '2021',
            ],
            [
                'typ' => 'programm',
                'datum' => '2020',
            ],
            [
                'typ' => 'programm',
                'datum' => '2019',
            ],
            [
                'typ' => 'programm',
                'datum' => '2018',
            ],
            [
                'typ' => 'programm',
                'datum' => '2017',
            ],
            [
                'typ' => 'programm',
                'datum' => '2016',
            ],
            [
                'typ' => 'weekend',
                'datum' => 'bevorstehend',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2021',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2020',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2019',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2018',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2017',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2016',
            ],
            [
                'typ' => 'training',
                'datum' => 'bevorstehend',
            ],
            [
                'typ' => 'training',
                'datum' => '2021',
            ],
            [
                'typ' => 'training',
                'datum' => '2020',
            ],
            [
                'typ' => 'training',
                'datum' => '2019',
            ],
            [
                'typ' => 'training',
                'datum' => '2018',
            ],
            [
                'typ' => 'training',
                'datum' => '2017',
            ],
            [
                'typ' => 'training',
                'datum' => '2016',
            ],
            [
                'typ' => 'ol',
                'datum' => 'bevorstehend',
            ],
            [
                'typ' => 'ol',
                'datum' => '2021',
            ],
            [
                'typ' => 'ol',
                'datum' => '2020',
            ],
            [
                'typ' => 'ol',
                'datum' => '2019',
            ],
            [
                'typ' => 'ol',
                'datum' => '2018',
            ],
            [
                'typ' => 'ol',
                'datum' => '2017',
            ],
            [
                'typ' => 'ol',
                'datum' => '2016',
            ],
            [
                'typ' => 'club',
                'datum' => 'bevorstehend',
            ],
            [
                'typ' => 'club',
                'datum' => '2021',
            ],
            [
                'typ' => 'club',
                'datum' => '2020',
            ],
            [
                'typ' => 'club',
                'datum' => '2019',
            ],
            [
                'typ' => 'club',
                'datum' => '2018',
            ],
            [
                'typ' => 'club',
                'datum' => '2017',
            ],
            [
                'typ' => 'club',
                'datum' => '2016',
            ],
            [
                'typ' => 'meldeschluss',
                'datum' => 'bevorstehend',
            ],
            [
                'typ' => 'meldeschluss',
                'datum' => '2021',
            ],
            [
                'typ' => 'meldeschluss',
                'datum' => '2020',
            ],
            [
                'typ' => 'meldeschluss',
                'datum' => '2019',
            ],
            [
                'typ' => 'meldeschluss',
                'datum' => '2018',
            ],
            [
                'typ' => 'meldeschluss',
                'datum' => '2017',
            ],
            [
                'typ' => 'meldeschluss',
                'datum' => '2016',
            ],
        ], $termine_utils->getAllValidFiltersForSitemap());
    }

    public function testGetUiTypeFilterOptions(): void {
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Alle Termine",
                'icon' => null,
                'ident' => 'alle',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'programm',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Jahresprogramm",
                'icon' => 'termine_type_programm_20.svg',
                'ident' => 'programm',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'weekend',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Weekends",
                'icon' => 'termine_type_weekend_20.svg',
                'ident' => 'weekend',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Trainings",
                'icon' => 'termine_type_training_20.svg',
                'ident' => 'training',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'ol',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Wettkämpfe",
                'icon' => 'termine_type_ol_20.svg',
                'ident' => 'ol',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'club',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Vereinsanlässe",
                'icon' => 'termine_type_club_20.svg',
                'ident' => 'club',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'meldeschluss',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Meldeschlüsse",
                'icon' => 'termine_type_meldeschluss_20.svg',
                'ident' => 'meldeschluss',
            ],
        ], $termine_utils->getUiTypeFilterOptions([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
        ]));
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2020',
                ],
                'name' => "Alle Termine",
                'icon' => null,
                'ident' => 'alle',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'programm',
                    'datum' => '2020',
                ],
                'name' => "Jahresprogramm",
                'icon' => 'termine_type_programm_20.svg',
                'ident' => 'programm',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'weekend',
                    'datum' => '2020',
                ],
                'name' => "Weekends",
                'icon' => 'termine_type_weekend_20.svg',
                'ident' => 'weekend',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2020',
                ],
                'name' => "Trainings",
                'icon' => 'termine_type_training_20.svg',
                'ident' => 'training',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'ol',
                    'datum' => '2020',
                ],
                'name' => "Wettkämpfe",
                'icon' => 'termine_type_ol_20.svg',
                'ident' => 'ol',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'club',
                    'datum' => '2020',
                ],
                'name' => "Vereinsanlässe",
                'icon' => 'termine_type_club_20.svg',
                'ident' => 'club',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'meldeschluss',
                    'datum' => '2020',
                ],
                'name' => "Meldeschlüsse",
                'icon' => 'termine_type_meldeschluss_20.svg',
                'ident' => 'meldeschluss',
            ],
        ], $termine_utils->getUiTypeFilterOptions([
            'typ' => 'training',
            'datum' => '2020',
        ]));
    }

    public function testGetUiDateRangeFilterOptionsExclArchive(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Bevorstehende",
                'icon' => null,
                'ident' => 'bevorstehend',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2021',
                ],
                'name' => "2021",
                'icon' => null,
                'ident' => '2021',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2020',
                ],
                'name' => "2020",
                'icon' => null,
                'ident' => '2020',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2019',
                ],
                'name' => "2019",
                'icon' => null,
                'ident' => '2019',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2018',
                ],
                'name' => "2018",
                'icon' => null,
                'ident' => '2018',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2017',
                ],
                'name' => "2017",
                'icon' => null,
                'ident' => '2017',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2016',
                ],
                'name' => "2016",
                'icon' => null,
                'ident' => '2016',
            ],
        ], $termine_utils->getUiDateRangeFilterOptions([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
        ]));
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Bevorstehende",
                'icon' => null,
                'ident' => 'bevorstehend',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2021',
                ],
                'name' => "2021",
                'icon' => null,
                'ident' => '2021',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2020',
                ],
                'name' => "2020",
                'icon' => null,
                'ident' => '2020',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2019',
                ],
                'name' => "2019",
                'icon' => null,
                'ident' => '2019',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2018',
                ],
                'name' => "2018",
                'icon' => null,
                'ident' => '2018',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2017',
                ],
                'name' => "2017",
                'icon' => null,
                'ident' => '2017',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2016',
                ],
                'name' => "2016",
                'icon' => null,
                'ident' => '2016',
            ],
        ], $termine_utils->getUiDateRangeFilterOptions([
            'typ' => 'training',
            'datum' => '2020',
        ]));
    }

    public function testGetUiDateRangeFilterOptionsInclArchive(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => true];
        $date_utils = new DateUtils('2011-03-13 19:30:00');
        $termine_utils = $this->getTermineUtilsForFilter();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => 'bevorstehend',
                ],
                'name' => "Bevorstehende",
                'icon' => null,
                'ident' => 'bevorstehend',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2012',
                ],
                'name' => "2012",
                'icon' => null,
                'ident' => '2012',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2011',
                ],
                'name' => "2011",
                'icon' => null,
                'ident' => '2011',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2010',
                ],
                'name' => "2010",
                'icon' => null,
                'ident' => '2010',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2009',
                ],
                'name' => "2009",
                'icon' => null,
                'ident' => '2009',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2008',
                ],
                'name' => "2008",
                'icon' => null,
                'ident' => '2008',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2007',
                ],
                'name' => "2007",
                'icon' => null,
                'ident' => '2007',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2006',
                ],
                'name' => "2006",
                'icon' => null,
                'ident' => '2006',
            ],
        ], $termine_utils->getUiDateRangeFilterOptions([
            'typ' => 'alle',
            'datum' => '2009',
        ]));
    }

    public function testGetDateRangeOptionsExclArchive(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $date_utils = new DateUtils('2006-01-13 19:30:00');
        $termine_utils = $this->getTermineUtilsForFilter();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['ident' => 'bevorstehend', 'name' => "Bevorstehende"],
            ['ident' => '2007', 'name' => "2007"],
            ['ident' => '2006', 'name' => "2006"],
            ['ident' => '2005', 'name' => "2005"],
            ['ident' => '2004', 'name' => "2004"],
            ['ident' => '2003', 'name' => "2003"],
            ['ident' => '2002', 'name' => "2002"],
        ], $termine_utils->getDateRangeOptions());
    }

    public function testGetDateRangeOptionsInclArchive(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => true];
        $date_utils = new DateUtils('2020-03-13 19:00:00');
        $termine_utils = $this->getTermineUtilsForFilter();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['ident' => 'bevorstehend', 'name' => "Bevorstehende"],
            ['ident' => '2021', 'name' => "2021"],
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
        ], $termine_utils->getDateRangeOptions());
    }

    public function testGetSqlDateRangeFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertSame(
            "(t.start_date >= '2020-03-13') OR (t.end_date >= '2020-03-13')",
            $termine_utils->getSqlDateRangeFilter([
                'typ' => 'alle',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "(t.start_date >= '2020-03-13') OR (t.end_date >= '2020-03-13')",
            $termine_utils->getSqlDateRangeFilter([
                'typ' => 'programm',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "YEAR(t.start_date) = '2020'",
            $termine_utils->getSqlDateRangeFilter([
                'typ' => 'alle',
                'datum' => '2020',
            ])
        );
    }

    public function testGetSqlTypeFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertSame(
            "'1' = '1'",
            $termine_utils->getSqlTypeFilter([
                'typ' => 'alle',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "t.typ LIKE '%programm%'",
            $termine_utils->getSqlTypeFilter([
                'typ' => 'programm',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "t.typ LIKE '%weekend%'",
            $termine_utils->getSqlTypeFilter([
                'typ' => 'weekend',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "t.typ LIKE '%training%'",
            $termine_utils->getSqlTypeFilter([
                'typ' => 'training',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "t.typ LIKE '%ol%'",
            $termine_utils->getSqlTypeFilter([
                'typ' => 'ol',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "t.typ LIKE '%club%'",
            $termine_utils->getSqlTypeFilter([
                'typ' => 'club',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "t.typ LIKE '%meldeschluss%'",
            $termine_utils->getSqlTypeFilter([
                'typ' => 'meldeschluss',
                'datum' => 'bevorstehend',
            ])
        );
    }

    public function testGetTitleFromFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['verified_email' => false];
        $termine_utils = $this->getTermineUtilsForFilter();
        $this->assertSame(
            "Bevorstehende Termine",
            $termine_utils->getTitleFromFilter([
                'typ' => 'alle',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "Jahresprogramm (bevorstehend)",
            $termine_utils->getTitleFromFilter([
                'typ' => 'programm',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "Weekends (bevorstehend)",
            $termine_utils->getTitleFromFilter([
                'typ' => 'weekend',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "Trainings (bevorstehend)",
            $termine_utils->getTitleFromFilter([
                'typ' => 'training',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "Wettkämpfe (bevorstehend)",
            $termine_utils->getTitleFromFilter([
                'typ' => 'ol',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "Vereinsanlässe (bevorstehend)",
            $termine_utils->getTitleFromFilter([
                'typ' => 'club',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "Bevorstehende Meldeschlüsse",
            $termine_utils->getTitleFromFilter([
                'typ' => 'meldeschluss',
                'datum' => 'bevorstehend',
            ])
        );
        $this->assertSame(
            "Termine 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'alle',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Jahresprogramm 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'programm',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Weekends 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'weekend',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Trainings 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'training',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Wettkämpfe 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'ol',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Vereinsanlässe 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'club',
                'datum' => '2020',
            ])
        );
        $this->assertSame(
            "Meldeschlüsse 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'meldeschluss',
                'datum' => '2020',
            ])
        );
    }

    public function testGetIsNotArchivedCriteria(): void {
        $termine_utils = $this->getTermineUtilsForFilter();
        $criteria_expression = $termine_utils->getIsNotArchivedCriteria();
        $this->assertSame('start_date', $criteria_expression->getField());
        $this->assertSame(
            '2016-01-01',
            $criteria_expression->getValue()->getValue()->format('Y-m-d')
        );
        $this->assertSame(Comparison::GTE, $criteria_expression->getOperator());
    }

    protected function getTermineUtilsForFilter(): TermineUtils {
        $termine_utils = new TermineUtils();
        $termine_utils->allTypeOptions = [
            ['ident' => 'alle', 'name' => "Alle Termine"],
            ['ident' => 'programm', 'name' => "Jahresprogramm", 'icon' => 'termine_type_programm_20.svg'],
            ['ident' => 'weekend', 'name' => "Weekends", 'icon' => 'termine_type_weekend_20.svg'],
            ['ident' => 'training', 'name' => "Trainings", 'icon' => 'termine_type_training_20.svg'],
            ['ident' => 'ol', 'name' => "Wettkämpfe", 'icon' => 'termine_type_ol_20.svg'],
            ['ident' => 'club', 'name' => "Vereinsanlässe", 'icon' => 'termine_type_club_20.svg'],
            ['ident' => 'meldeschluss', 'name' => "Meldeschlüsse", 'icon' => 'termine_type_meldeschluss_20.svg'],
        ];
        return $termine_utils;
    }

    public function testMinimalSolvEvent(): void {
        $termine_utils = new TermineUtils();
        $solv_event = FakeSolvEvent::minimal();
        $termin = new Termin();
        $termin->setSolvId($solv_event->getSolvUid());

        $termine_utils->updateTerminFromSolvEvent($termin, $solv_event);

        $this->assertSame('1970-01-01', $termin->getStartDate()->format('Y-m-d'));
        $this->assertNull($termin->getStartTime());
        $this->assertSame('1969-12-30', $termin->getEndDate()?->format('Y-m-d'));
        $this->assertNull($termin->getEndTime());
        $this->assertNull($termin->getDeadline());
        $this->assertSame('', $termin->getTitle());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Link: -

            Organisator: -

            Karte: -

            Ort: -
            ZZZZZZZZZZ, $termin->getText());
        $this->assertFalse($termin->getNewsletter());
        $this->assertNull($termin->getLocation());
        $this->assertSame(-1, $termin->getCoordinateX());
        $this->assertSame(-1, $termin->getCoordinateY());
    }

    public function testEmptySolvEvent(): void {
        $termine_utils = new TermineUtils();
        $solv_event = FakeSolvEvent::empty();
        $termin = new Termin();
        $termin->setSolvId($solv_event->getSolvUid());

        $termine_utils->updateTerminFromSolvEvent($termin, $solv_event);

        $this->assertSame('1970-01-01', $termin->getStartDate()->format('Y-m-d'));
        $this->assertNull($termin->getStartTime());
        $this->assertSame('1969-12-31', $termin->getEndDate()?->format('Y-m-d'));
        $this->assertNull($termin->getEndTime());
        $this->assertSame('1970-01-01 23:59:59', $termin->getDeadline()?->format('Y-m-d H:i:s'));
        $this->assertSame('', $termin->getTitle());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Link: -

            Organisator: -

            Karte: -

            Ort: -
            ZZZZZZZZZZ, $termin->getText());
        $this->assertFalse($termin->getNewsletter());
        $this->assertNull($termin->getLocation());
        $this->assertSame(0, $termin->getCoordinateX());
        $this->assertSame(0, $termin->getCoordinateY());
    }

    public function testMaximalSolvEvent(): void {
        $termine_utils = new TermineUtils();
        $solv_event = FakeSolvEvent::maximal();
        $termin = new Termin();
        $termin->setSolvId($solv_event->getSolvUid());

        $termine_utils->updateTerminFromSolvEvent($termin, $solv_event);

        $this->assertSame('2020-03-13', $termin->getStartDate()->format('Y-m-d'));
        $this->assertNull($termin->getStartTime());
        $this->assertSame('2020-03-15', $termin->getEndDate()?->format('Y-m-d'));
        $this->assertNull($termin->getEndTime());
        $this->assertSame('2020-03-13 23:59:59', $termin->getDeadline()?->format('Y-m-d H:i:s'));
        $this->assertSame('Fake Event', $termin->getTitle());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Link: https://staging.olzimmerberg.ch/

            Organisator: OL Zimmerberg

            Karte: Landforst

            Ort: Pumpispitz
            ZZZZZZZZZZ, $termin->getText());
        $this->assertFalse($termin->getNewsletter());
        $this->assertNull($termin->getLocation());
        $this->assertSame(684376, $termin->getCoordinateX());
        $this->assertSame(236945, $termin->getCoordinateY());
    }

    public function testMaximalTermin(): void {
        $termine_utils = new TermineUtils();
        $termin = FakeTermin::maximal();

        $termine_utils->updateTerminFromSolvEvent($termin);

        $this->assertSame('2020-03-13', $termin->getStartDate()->format('Y-m-d'));
        $this->assertNull($termin->getStartTime());
        $this->assertSame('2020-03-15', $termin->getEndDate()?->format('Y-m-d'));
        $this->assertNull($termin->getEndTime());
        $this->assertSame('2020-03-13 23:59:59', $termin->getDeadline()?->format('Y-m-d H:i:s'));
        $this->assertSame('Fake Event', $termin->getTitle());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Link: https://staging.olzimmerberg.ch/

            Organisator: OL Zimmerberg

            Karte: Landforst

            Ort: Pumpispitz
            ZZZZZZZZZZ, $termin->getText());
        $this->assertFalse($termin->getNewsletter());
        $this->assertNull($termin->getLocation());
        $this->assertSame(684376, $termin->getCoordinateX());
        $this->assertSame(236945, $termin->getCoordinateY());
    }
}
