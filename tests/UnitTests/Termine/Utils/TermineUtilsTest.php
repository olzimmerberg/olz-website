<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Utils;

use Olz\Entity\Termine\Termin;
use Olz\Termine\Utils\TermineUtils;
use Olz\Tests\Fake\Entity\FakeSolvEvent;
use Olz\Tests\Fake\Entity\Termine\FakeTermin;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Termine\Utils\TermineUtils
 */
final class TermineUtilsTest extends UnitTestCase {
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
