<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Parsers;

use Olz\Parsers\StravaActivityParser;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Parsers\StravaActivityParser
 */
final class StravaActivityParserTest extends UnitTestCase {
    private string $strava_activity_de_path = __DIR__.'/data/strava-activity-de.html';
    private string $strava_activity_en_path = __DIR__.'/data/strava-activity-en.html';
    private string $strava_activity_bike_path = __DIR__.'/data/strava-activity-bike.html';

    public function testParseStravaActivityDe(): void {
        $plaintext = file_get_contents($this->strava_activity_de_path) ?: '';
        $parser = new StravaActivityParser();

        $data = $parser->parse_strava_activity_html($plaintext);

        $this->assertEquals([
            'name' => 'Simon Hatt',
            'sportType' => 'Laufen',
            'runAt' => new \DateTime('2026-06-29 19:00:00'),
            'distanceMeters' => 9710,
            'elevationMeters' => 232,
        ], $data);
    }

    public function testParseStravaActivityEn(): void {
        $plaintext = file_get_contents($this->strava_activity_en_path) ?: '';
        $parser = new StravaActivityParser();

        $data = $parser->parse_strava_activity_html($plaintext);

        $this->assertEquals([
            'name' => 'Simon Hatt',
            'sportType' => 'Run',
            'runAt' => new \DateTime('2026-06-25 19:22:00'),
            'distanceMeters' => 12240,
            'elevationMeters' => 241,
        ], $data);
    }

    public function testParseStravaActivityBike(): void {
        $plaintext = file_get_contents($this->strava_activity_bike_path) ?: '';
        $parser = new StravaActivityParser();

        $data = $parser->parse_strava_activity_html($plaintext);

        $this->assertEquals([
            'name' => 'Simon Hatt',
            'sportType' => 'Radfahren',
            'runAt' => new \DateTime('2026-06-06 17:29:00'),
            'distanceMeters' => 23900,
            'elevationMeters' => 56,
        ], $data);
    }
}
