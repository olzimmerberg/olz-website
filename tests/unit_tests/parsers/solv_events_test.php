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
    private $fixtures_2018_path = __DIR__.'/data/fixtures-2018.csv';

    public function testParseFixtures2006(): void {
        $fixtures_2006 = file_get_contents($this->fixtures_2006_path);

        $solv_events_2006 = parse_solv_events_csv($fixtures_2006);

        $this->assertSame(200, count($solv_events_2006));

        $first_solv_event_2006 = $solv_events_2006[0];
        $this->assertSame('2973', $first_solv_event_2006->getSolvUid());
        $this->assertSame('2006-01-07', $first_solv_event_2006->getDate()->format('Y-m-d'));
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
        $this->assertSame(null, $first_solv_event_2006->getDeadline());
        $this->assertSame('0', $first_solv_event_2006->getEntryportal());
        $this->assertSame('2005-08-08 15:55:01', $first_solv_event_2006->getLastModification()->format('Y-m-d H:i:s'));

        $second_solv_event_2006 = $solv_events_2006[1];
        $this->assertSame('OLG Balsthal-Gäu', $second_solv_event_2006->getClub());
    }

    public function testParseFixtures2018(): void {
        $fixtures_2018 = file_get_contents($this->fixtures_2018_path);

        $solv_events_2018 = parse_solv_events_csv($fixtures_2018);

        $this->assertSame(204, count($solv_events_2018));

        $first_solv_event_2018 = $solv_events_2018[0];
        $this->assertSame('8270', $first_solv_event_2018->getSolvUid());
        $this->assertSame('2018-01-13', $first_solv_event_2018->getDate()->format('Y-m-d'));
        $this->assertSame('1', $first_solv_event_2018->getDuration());
        $this->assertSame('foot', $first_solv_event_2018->getKind());
        $this->assertSame('day', $first_solv_event_2018->getDayNight());
        $this->assertSame('0', $first_solv_event_2018->getNational());
        $this->assertSame('', $first_solv_event_2018->getRegion());
        $this->assertSame('', $first_solv_event_2018->getType());
        $this->assertSame('K�rtelertagung', $first_solv_event_2018->getName());
        $this->assertSame('https://www.swiss-orienteering.ch/php_includes/pages/courseware/?id=25', $first_solv_event_2018->getLink());
        $this->assertSame('Swiss Orienteering', $first_solv_event_2018->getClub());
        $this->assertSame('Olten', $first_solv_event_2018->getMap());
        $this->assertSame('', $first_solv_event_2018->getLocation());
        $this->assertSame('', $first_solv_event_2018->getCoordX());
        $this->assertSame('', $first_solv_event_2018->getCoordY());
        $this->assertSame(null, $first_solv_event_2018->getDeadline());
        $this->assertSame('0', $first_solv_event_2018->getEntryportal());
        $this->assertSame('2018-12-13 09:07:27', $first_solv_event_2018->getLastModification()->format('Y-m-d H:i:s'));

        $second_solv_event_2018 = $solv_events_2018[71];
        $this->assertSame('Z�rcher s\'COOL-Cup', $second_solv_event_2018->getName());
    }
}
