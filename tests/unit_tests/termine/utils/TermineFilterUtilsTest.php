<?php

declare(strict_types=1);

use Doctrine\Common\Collections\Expr\Comparison;

require_once __DIR__.'/../../../../public/_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../../public/_/termine/utils/TermineFilterUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \TermineFilterUtils
 */
final class TermineFilterUtilsTest extends UnitTestCase {
    public function testGetDefaultFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
            'archiv' => 'ohne',
        ], $termine_utils->getDefaultFilter());
    }

    public function testIsValidFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame(false, $termine_utils->isValidFilter([]));
        $this->assertSame(false, $termine_utils->isValidFilter(['foo' => 'bar']));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(false, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2011',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2011',
            'archiv' => 'mit',
        ]));
        $this->assertSame(false, $termine_utils->isValidFilter([
            'typ' => 'some',
            'datum' => 'silly',
            'archiv' => 'rubbish',
        ]));
    }

    public function testDefaultFilterIsValid(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame(true, $termine_utils->isValidFilter($termine_utils->getDefaultFilter()));
    }

    public function testGetAllValidFiltersForSitemap(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'typ' => 'alle',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'alle',
                'datum' => '2021',
                'archiv' => 'ohne',
            ],
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
                'typ' => 'programm',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'programm',
                'datum' => '2021',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'programm',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'programm',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'programm',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'programm',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'programm',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'weekend',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2021',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'weekend',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'training',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'training',
                'datum' => '2021',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'training',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'training',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'training',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'training',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'training',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'ol',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'ol',
                'datum' => '2021',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'ol',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'ol',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'ol',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'ol',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'ol',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'club',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'club',
                'datum' => '2021',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'club',
                'datum' => '2020',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'club',
                'datum' => '2019',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'club',
                'datum' => '2018',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'club',
                'datum' => '2017',
                'archiv' => 'ohne',
            ],
            [
                'typ' => 'club',
                'datum' => '2016',
                'archiv' => 'ohne',
            ],
        ], $termine_utils->getAllValidFiltersForSitemap());
    }

    public function testGetUiTypeFilterOptions(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => 'bevorstehend',
                    'archiv' => 'ohne',
                ],
                'name' => "Alle Termine",
                'ident' => 'alle',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'programm',
                    'datum' => 'bevorstehend',
                    'archiv' => 'ohne',
                ],
                'name' => "Jahresprogramm",
                'ident' => 'programm',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'weekend',
                    'datum' => 'bevorstehend',
                    'archiv' => 'ohne',
                ],
                'name' => "Weekends",
                'ident' => 'weekend',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => 'bevorstehend',
                    'archiv' => 'ohne',
                ],
                'name' => "Trainings",
                'ident' => 'training',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'ol',
                    'datum' => 'bevorstehend',
                    'archiv' => 'ohne',
                ],
                'name' => "Wettkämpfe",
                'ident' => 'ol',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'club',
                    'datum' => 'bevorstehend',
                    'archiv' => 'ohne',
                ],
                'name' => "Vereinsanlässe",
                'ident' => 'club',
            ],
        ], $termine_utils->getUiTypeFilterOptions([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
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
                'name' => "Alle Termine",
                'ident' => 'alle',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'programm',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Jahresprogramm",
                'ident' => 'programm',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'weekend',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Weekends",
                'ident' => 'weekend',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Trainings",
                'ident' => 'training',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'ol',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Wettkämpfe",
                'ident' => 'ol',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'club',
                    'datum' => '2020',
                    'archiv' => 'mit',
                ],
                'name' => "Vereinsanlässe",
                'ident' => 'club',
            ],
        ], $termine_utils->getUiTypeFilterOptions([
            'typ' => 'training',
            'datum' => '2020',
            'archiv' => 'mit',
        ]));
    }

    public function testGetUiDateRangeFilterOptionsExclArchive(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => 'bevorstehend',
                    'archiv' => 'ohne',
                ],
                'name' => "Bevorstehende",
                'ident' => 'bevorstehend',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2021',
                    'archiv' => 'ohne',
                ],
                'name' => "2021",
                'ident' => '2021',
            ],
            [
                'selected' => false,
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
        ], $termine_utils->getUiDateRangeFilterOptions([
            'typ' => 'alle',
            'datum' => 'bevorstehend',
            'archiv' => 'ohne',
        ]));
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => 'bevorstehend',
                    'archiv' => 'ohne',
                ],
                'name' => "Bevorstehende",
                'ident' => 'bevorstehend',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2021',
                    'archiv' => 'ohne',
                ],
                'name' => "2021",
                'ident' => '2021',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2020',
                    'archiv' => 'ohne',
                ],
                'name' => "2020",
                'ident' => '2020',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2019',
                    'archiv' => 'ohne',
                ],
                'name' => "2019",
                'ident' => '2019',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2018',
                    'archiv' => 'ohne',
                ],
                'name' => "2018",
                'ident' => '2018',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2017',
                    'archiv' => 'ohne',
                ],
                'name' => "2017",
                'ident' => '2017',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'training',
                    'datum' => '2016',
                    'archiv' => 'ohne',
                ],
                'name' => "2016",
                'ident' => '2016',
            ],
        ], $termine_utils->getUiDateRangeFilterOptions([
            'typ' => 'training',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
    }

    public function testGetUiDateRangeFilterOptionsInclArchive(): void {
        $date_utils = new FixedDateUtils('2011-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => 'bevorstehend',
                    'archiv' => 'mit',
                ],
                'name' => "Bevorstehende",
                'ident' => 'bevorstehend',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2012',
                    'archiv' => 'mit',
                ],
                'name' => "2012",
                'ident' => '2012',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2011',
                    'archiv' => 'mit',
                ],
                'name' => "2011",
                'ident' => '2011',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2010',
                    'archiv' => 'mit',
                ],
                'name' => "2010",
                'ident' => '2010',
            ],
            [
                'selected' => true,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2009',
                    'archiv' => 'mit',
                ],
                'name' => "2009",
                'ident' => '2009',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2008',
                    'archiv' => 'mit',
                ],
                'name' => "2008",
                'ident' => '2008',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2007',
                    'archiv' => 'mit',
                ],
                'name' => "2007",
                'ident' => '2007',
            ],
            [
                'selected' => false,
                'new_filter' => [
                    'typ' => 'alle',
                    'datum' => '2006',
                    'archiv' => 'mit',
                ],
                'name' => "2006",
                'ident' => '2006',
            ],
        ], $termine_utils->getUiDateRangeFilterOptions([
            'typ' => 'alle',
            'datum' => '2009',
            'archiv' => 'mit',
        ]));
    }

    public function testGetUiArchiveFilterOptions(): void {
        $termine_utils = new TermineFilterUtils();
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
        ], $termine_utils->getUiArchiveFilterOptions([
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
        ], $termine_utils->getUiArchiveFilterOptions([
            'typ' => 'aktuell',
            'datum' => '2018',
            'archiv' => 'mit',
        ]));
    }

    public function testGetDateRangeOptionsExclArchive(): void {
        $date_utils = new FixedDateUtils('2006-01-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['ident' => 'bevorstehend', 'name' => "Bevorstehende"],
            ['ident' => '2007', 'name' => "2007"],
            ['ident' => '2006', 'name' => "2006"],
            ['ident' => '2005', 'name' => "2005"],
            ['ident' => '2004', 'name' => "2004"],
            ['ident' => '2003', 'name' => "2003"],
            ['ident' => '2002', 'name' => "2002"],
        ], $termine_utils->getDateRangeOptions(['archiv' => 'ohne']));
    }

    public function testGetDateRangeOptionsInclArchive(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:00:00');
        $termine_utils = new TermineFilterUtils();
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
        ], $termine_utils->getDateRangeOptions(['archiv' => 'mit']));
    }

    public function testGetSqlFromFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame("'1'='0'", $termine_utils->getSqlFromFilter([]));
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND ('1' = '1')",
            $termine_utils->getSqlFromFilter([
                'typ' => 'alle',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND (t.typ LIKE '%programm%')",
            $termine_utils->getSqlFromFilter([
                'typ' => 'programm',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND (t.typ LIKE '%weekend%')",
            $termine_utils->getSqlFromFilter([
                'typ' => 'weekend',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND (t.typ LIKE '%training%')",
            $termine_utils->getSqlFromFilter([
                'typ' => 'training',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND (t.typ LIKE '%ol%')",
            $termine_utils->getSqlFromFilter([
                'typ' => 'ol',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND (t.typ LIKE '%club%')",
            $termine_utils->getSqlFromFilter([
                'typ' => 'club',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "(YEAR(t.datum) = '2020') AND ('1' = '1')",
            $termine_utils->getSqlFromFilter([
                'typ' => 'alle',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
    }

    public function testGetTitleFromFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame("Termine", $termine_utils->getTitleFromFilter([]));
        $this->assertSame(
            "Bevorstehende Termine",
            $termine_utils->getTitleFromFilter([
                'typ' => 'alle',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Bevorstehendes Jahresprogramm",
            $termine_utils->getTitleFromFilter([
                'typ' => 'programm',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Bevorstehende Weekends",
            $termine_utils->getTitleFromFilter([
                'typ' => 'weekend',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Bevorstehende Trainings",
            $termine_utils->getTitleFromFilter([
                'typ' => 'training',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Bevorstehende Wettkämpfe",
            $termine_utils->getTitleFromFilter([
                'typ' => 'ol',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Bevorstehende Vereinsanlässe",
            $termine_utils->getTitleFromFilter([
                'typ' => 'club',
                'datum' => 'bevorstehend',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Termine 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'alle',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Jahresprogramm 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'programm',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Weekends 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'weekend',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Trainingsplan 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'training',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Wettkämpfe 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'ol',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Vereinsanlässe 2020",
            $termine_utils->getTitleFromFilter([
                'typ' => 'club',
                'datum' => '2020',
                'archiv' => 'ohne',
            ])
        );
        $this->assertSame(
            "Trainingsplan 2020 (Archiv)",
            $termine_utils->getTitleFromFilter([
                'typ' => 'training',
                'datum' => '2020',
                'archiv' => 'mit',
            ])
        );
    }

    public function testIsFilterNotArchived(): void {
        $termine_utils = new TermineFilterUtils();
        $this->assertSame(false, $termine_utils->isFilterNotArchived([]));
        $this->assertSame(true, $termine_utils->isFilterNotArchived(['archiv' => 'ohne']));
        $this->assertSame(false, $termine_utils->isFilterNotArchived(['archiv' => 'mit']));
    }

    public function testGetIsNotArchivedCriteria(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineFilterUtils();
        $termine_utils->setDateUtils($date_utils);
        $criteria_expression = $termine_utils->getIsNotArchivedCriteria();
        $this->assertSame('datum', $criteria_expression->getField());
        $this->assertSame(
            '2016-01-01',
            $criteria_expression->getValue()->getValue()->format('Y-m-d')
        );
        $this->assertSame(Comparison::GTE, $criteria_expression->getOperator());
    }
}
