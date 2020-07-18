<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/parsers/solv_events.php';

/**
 * @internal
 * @coversNothing
 */
final class SolvEventsParserTest extends TestCase {
    private $fixtures_2006_path = __DIR__.'/data/fixtures-2006.csv';

    public function testParseFixtures2006(): void {
        $fixtures_2006 = file_get_contents($this->fixtures_2006_path);

        $solv_events_2006 = parse_solv_events_csv($fixtures_2006);

        $this->assertSame(200, count($solv_events_2006));

        $first_solv_event_2006 = $solv_events_2006[0];
        $this->assertSame('2973', $first_solv_event_2006->getSolvUid());
        $this->assertSame('2006-01-07', $first_solv_event_2006->getDate());
        $this->assertSame('1', $first_solv_event_2006->getDuration());
        $this->assertSame('foot', $first_solv_event_2006->getKind());
        $this->assertSame('day', $first_solv_event_2006->getDayNight());
        $this->assertSame('0', $first_solv_event_2006->getNational());
        $this->assertSame('GL/GR', $first_solv_event_2006->getRegion());
        $this->assertSame('*1', $first_solv_event_2006->getType());
        $this->assertSame('4. Churer Stadt-OL', $first_solv_event_2006->getName());
        $this->assertSame('http://www.solv.ch/olg-chur/index.php', $first_solv_event_2006->getLink());
        $this->assertSame('OLG Chur', $first_solv_event_2006->getClub());
        $this->assertSame('Stadt Chur', $first_solv_event_2006->getMap());
        $this->assertSame('', $first_solv_event_2006->getLocation());
        $this->assertSame('', $first_solv_event_2006->getCoordX());
        $this->assertSame('', $first_solv_event_2006->getCoordY());
        $this->assertSame('', $first_solv_event_2006->getDeadline());
        $this->assertSame('0', $first_solv_event_2006->getEntryportal());
        $this->assertSame('2005-08-08 15:55:01', $first_solv_event_2006->getLastModification());

        $second_solv_event_2006 = $solv_events_2006[1];
        $this->assertSame('OLG Balsthal-Gäu', $second_solv_event_2006->getClub());
    }
}
