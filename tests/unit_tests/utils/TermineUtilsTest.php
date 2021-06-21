<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../src/utils/TermineUtils.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \TermineUtils
 */
final class TermineUtilsTest extends UnitTestCase {
    public function testGetDefaultFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame(['typ' => 'alle', 'datum' => 'bevorstehend'], $termine_utils->getDefaultFilter());
    }

    public function testIsValidFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame(false, $termine_utils->isValidFilter([]));
        $this->assertSame(false, $termine_utils->isValidFilter(['foo' => 'bar']));
        $this->assertSame(true, $termine_utils->isValidFilter(['typ' => 'alle', 'datum' => 'bevorstehend']));
        $this->assertSame(false, $termine_utils->isValidFilter(['typ' => 'some', 'datum' => 'rubbish']));
    }

    public function testDefaultFilterIsValid(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame(true, $termine_utils->isValidFilter($termine_utils->getDefaultFilter()));
    }

    public function testGetAllValidFilters(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['typ' => 'alle', 'datum' => 'bevorstehend'],
            ['typ' => 'alle', 'datum' => '2019'],
            ['typ' => 'alle', 'datum' => '2020'],
            ['typ' => 'alle', 'datum' => '2021'],
            ['typ' => 'training', 'datum' => 'bevorstehend'],
            ['typ' => 'training', 'datum' => '2019'],
            ['typ' => 'training', 'datum' => '2020'],
            ['typ' => 'training', 'datum' => '2021'],
            ['typ' => 'ol', 'datum' => 'bevorstehend'],
            ['typ' => 'ol', 'datum' => '2019'],
            ['typ' => 'ol', 'datum' => '2020'],
            ['typ' => 'ol', 'datum' => '2021'],
            ['typ' => 'club', 'datum' => 'bevorstehend'],
            ['typ' => 'club', 'datum' => '2019'],
            ['typ' => 'club', 'datum' => '2020'],
            ['typ' => 'club', 'datum' => '2021'],
        ], $termine_utils->getAllValidFilters());
    }

    public function testGetUiTypeFilterOptions(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['selected' => true, 'new_filter' => ['typ' => 'alle', 'datum' => 'bevorstehend'], 'name' => "Alle Termine", 'ident' => 'alle'],
            ['selected' => false, 'new_filter' => ['typ' => 'training', 'datum' => 'bevorstehend'], 'name' => "Trainings", 'ident' => 'training'],
            ['selected' => false, 'new_filter' => ['typ' => 'ol', 'datum' => 'bevorstehend'], 'name' => "Wettkämpfe", 'ident' => 'ol'],
            ['selected' => false, 'new_filter' => ['typ' => 'club', 'datum' => 'bevorstehend'], 'name' => "Vereinsanlässe", 'ident' => 'club'],
        ], $termine_utils->getUiTypeFilterOptions(['typ' => 'alle', 'datum' => 'bevorstehend']));
        $this->assertSame([
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2020'], 'name' => "Alle Termine", 'ident' => 'alle'],
            ['selected' => true, 'new_filter' => ['typ' => 'training', 'datum' => '2020'], 'name' => "Trainings", 'ident' => 'training'],
            ['selected' => false, 'new_filter' => ['typ' => 'ol', 'datum' => '2020'], 'name' => "Wettkämpfe", 'ident' => 'ol'],
            ['selected' => false, 'new_filter' => ['typ' => 'club', 'datum' => '2020'], 'name' => "Vereinsanlässe", 'ident' => 'club'],
        ], $termine_utils->getUiTypeFilterOptions(['typ' => 'training', 'datum' => '2020']));
    }

    public function testGetUiDateRangeFilterOptions(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['selected' => true, 'new_filter' => ['typ' => 'alle', 'datum' => 'bevorstehend'], 'name' => "Bevorstehende", 'ident' => 'bevorstehend'],
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2019'], 'name' => "2019", 'ident' => '2019'],
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2020'], 'name' => "2020", 'ident' => '2020'],
            ['selected' => false, 'new_filter' => ['typ' => 'alle', 'datum' => '2021'], 'name' => "2021", 'ident' => '2021'],
        ], $termine_utils->getUiDateRangeFilterOptions(['typ' => 'alle', 'datum' => 'bevorstehend']));
        $this->assertSame([
            ['selected' => false, 'new_filter' => ['typ' => 'training', 'datum' => 'bevorstehend'], 'name' => "Bevorstehende", 'ident' => 'bevorstehend'],
            ['selected' => false, 'new_filter' => ['typ' => 'training', 'datum' => '2019'], 'name' => "2019", 'ident' => '2019'],
            ['selected' => true, 'new_filter' => ['typ' => 'training', 'datum' => '2020'], 'name' => "2020", 'ident' => '2020'],
            ['selected' => false, 'new_filter' => ['typ' => 'training', 'datum' => '2021'], 'name' => "2021", 'ident' => '2021'],
        ], $termine_utils->getUiDateRangeFilterOptions(['typ' => 'training', 'datum' => '2020']));
    }

    public function testGetDateRangeOptions(): void {
        $date_utils = new FixedDateUtils('2006-01-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame([
            ['ident' => 'bevorstehend', 'name' => "Bevorstehende"],
            ['ident' => '2005', 'name' => "2005"],
            ['ident' => '2006', 'name' => "2006"],
            ['ident' => '2007', 'name' => "2007"],
        ], $termine_utils->getDateRangeOptions());
    }

    public function testGetSqlFromFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame("'1'='0'", $termine_utils->getSqlFromFilter([]));
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND ('1' = '1')",
            $termine_utils->getSqlFromFilter(['typ' => 'alle', 'datum' => 'bevorstehend'])
        );
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND (t.typ LIKE '%training%')",
            $termine_utils->getSqlFromFilter(['typ' => 'training', 'datum' => 'bevorstehend'])
        );
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND (t.typ LIKE '%ol%')",
            $termine_utils->getSqlFromFilter(['typ' => 'ol', 'datum' => 'bevorstehend'])
        );
        $this->assertSame(
            "((t.datum >= '2020-03-13') OR (t.datum_end >= '2020-03-13')) AND (t.typ LIKE '%club%')",
            $termine_utils->getSqlFromFilter(['typ' => 'club', 'datum' => 'bevorstehend'])
        );
        $this->assertSame(
            "(YEAR(t.datum) = '2020') AND ('1' = '1')",
            $termine_utils->getSqlFromFilter(['typ' => 'alle', 'datum' => '2020'])
        );
    }

    public function testGetTitleFromFilter(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $termine_utils = new TermineUtils();
        $termine_utils->setDateUtils($date_utils);
        $this->assertSame("Termine", $termine_utils->getTitleFromFilter([]));
        $this->assertSame(
            "Bevorstehende Termine",
            $termine_utils->getTitleFromFilter(['typ' => 'alle', 'datum' => 'bevorstehend'])
        );
        $this->assertSame(
            "Bevorstehende Trainings",
            $termine_utils->getTitleFromFilter(['typ' => 'training', 'datum' => 'bevorstehend'])
        );
        $this->assertSame(
            "Bevorstehende Wettkämpfe",
            $termine_utils->getTitleFromFilter(['typ' => 'ol', 'datum' => 'bevorstehend'])
        );
        $this->assertSame(
            "Bevorstehende Vereinsanlässe",
            $termine_utils->getTitleFromFilter(['typ' => 'club', 'datum' => 'bevorstehend'])
        );
        $this->assertSame(
            "Termine 2020",
            $termine_utils->getTitleFromFilter(['typ' => 'alle', 'datum' => '2020'])
        );
        $this->assertSame(
            "Trainings 2020",
            $termine_utils->getTitleFromFilter(['typ' => 'training', 'datum' => '2020'])
        );
    }
}
