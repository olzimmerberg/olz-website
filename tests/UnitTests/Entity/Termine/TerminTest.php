<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Entity\Termine;

use Olz\Entity\Termine\Termin;
use Olz\Tests\Fake\Entity\Common\ExpressionEvaluationVisitor;
use Olz\Tests\Fake\Entity\Termine\FakeTermin;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Entity\Termine\Termin
 */
final class TerminTest extends UnitTestCase {
    public function testTerminGetCriteriaForQuery(): void {
        $termin = FakeTermin::maximal();
        $this->assertTrue($this->matchesTermin('2020-03-13', $termin), 'ISO date, same day');
        $this->assertTrue($this->matchesTermin('13.3.2020', $termin), 'Swiss date, same day');
        $this->assertTrue($this->matchesTermin('2020', $termin), 'Year');
        $this->assertFalse($this->matchesTermin('2020-03-14', $termin), 'Day after');
        $this->assertFalse($this->matchesTermin('2021', $termin), 'Other year');
        $this->assertTrue($this->matchesTermin('Fake title!', $termin), 'Title');
        $this->assertFalse($this->matchesTermin('No such title', $termin), 'No title match');

        $empty_termin = FakeTermin::empty();
        $this->assertFalse($this->matchesTermin('2020-03-13', $empty_termin), 'Other day, other termin');
        $this->assertTrue($this->matchesTermin('Cannot be empty', $empty_termin), 'Title, other termin');
    }

    public function testTerminGetCriteriaForQueryWithoutWarmedUtilsCache(): void {
        WithUtilsCache::reset();
        try {
            Termin::getCriteriaForQuery('2020-03-13');
            $this->fail('Exception expected.');
        } catch (\RuntimeException $th) {
            $this->assertSame(
                'Termin::getCriteriaForQuery() requires a warmed WithUtilsCache.',
                $th->getMessage(),
            );
        }
    }

    /**
     * Does the criteria built for the search `$query` match `$termin`?
     */
    private function matchesTermin(string $query, Termin $termin): bool {
        $visitor = new ExpressionEvaluationVisitor($termin);
        Termin::getCriteriaForQuery($query)->visit($visitor);
        return $visitor->isMatching;
    }
}
