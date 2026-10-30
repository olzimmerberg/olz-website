<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Entity;

use Olz\Entity\SolvEvent;
use Olz\Tests\Fake\Entity\Common\ExpressionEvaluationVisitor;
use Olz\Tests\Fake\Entity\FakeSolvEvent;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Entity\SolvEvent
 */
final class SolvEventTest extends UnitTestCase {
    public function testSolvEventGetCriteriaForQuery(): void {
        $solv_event = FakeSolvEvent::maximal();
        $this->assertTrue($this->matchesSolvEvent('2020-03-13', $solv_event), 'ISO date, same day');
        $this->assertTrue($this->matchesSolvEvent('13.3.2020', $solv_event), 'Swiss date, same day');
        $this->assertTrue($this->matchesSolvEvent('2020', $solv_event), 'Year');
        $this->assertFalse($this->matchesSolvEvent('2020-03-14', $solv_event), 'Day after');
        $this->assertFalse($this->matchesSolvEvent('2021', $solv_event), 'Other year');
        $this->assertTrue($this->matchesSolvEvent('Fake Event', $solv_event), 'Name');
        $this->assertFalse($this->matchesSolvEvent('No such event', $solv_event), 'No name match');

        $other_event = FakeSolvEvent::withResults();
        $this->assertTrue($this->matchesSolvEvent('Event with results', $other_event), 'Name, other event');
        $this->assertFalse($this->matchesSolvEvent('Fake Event', $other_event), 'No name match, other event');
        $this->assertTrue($this->matchesSolvEvent('2020-03-13', $other_event), 'Date, other event');
    }

    public function testSolvEventGetCriteriaForQueryWithoutWarmedUtilsCache(): void {
        WithUtilsCache::reset();
        try {
            SolvEvent::getCriteriaForQuery('2020-03-13');
            $this->fail('Exception expected.');
        } catch (\RuntimeException $th) {
            $this->assertSame(
                'SolvEvent::getCriteriaForQuery() requires a warmed WithUtilsCache.',
                $th->getMessage(),
            );
        }
    }

    /**
     * Does the criteria built for the search `$query` match `$solv_event`?
     */
    private function matchesSolvEvent(string $query, SolvEvent $solv_event): bool {
        $visitor = new ExpressionEvaluationVisitor($solv_event);
        SolvEvent::getCriteriaForQuery($query)->visit($visitor);
        return $visitor->isMatching;
    }
}
