<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Fetchers;

use Olz\Fetchers\SolvFetcher;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\Fetchers\SolvFetcher
 */
final class SolvFetcherTest extends IntegrationTestCase {
    public function __construct() {
        parent::__construct();
        $this->solv_fetcher = new SolvFetcher();
        $this->year_to_fetch = date('m') < 4 ? date('Y') - 1 : date('Y');
    }

    public function testFetchEventsCsvForYear(): void {
        $content = $this->solv_fetcher->fetchEventsCsvForYear(date('Y'));
        $lines = explode("\n", $content);
        $expected_first_line = 'unique_id;date;duration;kind;day_night;national;region;type;event_name;event_link;club;map;location;coord_x;coord_y;deadline;entryportal;last_modification';
        $this->assertSame($expected_first_line, $lines[0]);
        $this->assertGreaterThan(1, count($lines));
    }

    public function testFetchYearlyResultsJson(): void {
        $content = $this->solv_fetcher->fetchYearlyResultsJson($this->year_to_fetch);
        $data = json_decode($content, true);
        $result_lists = $data['ResultLists'];
        $this->assertGreaterThan(0, count($result_lists));
        $this->assertArrayHasKey('UniqueID', $result_lists[0]);
        $this->assertArrayHasKey('EventDate', $result_lists[0]);
        $this->assertArrayHasKey('EventName', $result_lists[0]);
        $this->assertArrayHasKey('EventCity', $result_lists[0]);
        $this->assertArrayHasKey('EventMap', $result_lists[0]);
        $this->assertArrayHasKey('EventClub', $result_lists[0]);
        $this->assertArrayHasKey('EventType', $result_lists[0]);
        $this->assertArrayHasKey('SubTitle', $result_lists[0]);
        $this->assertArrayHasKey('ResultListID', $result_lists[0]);
        $this->assertArrayHasKey('ResultType', $result_lists[0]);
        $this->assertArrayHasKey('ResultModTime', $result_lists[0]);
    }

    public function testFetchEventResultsHtml(): void {
        $rank_id = $this->getLatestRankId();
        $content = $this->solv_fetcher->fetchEventResultsHtml($rank_id);
        $this->assertMatchesRegularExpression('/<p>Total: [0-9]+ Teilnehmer des Clubs\\./', $content);
    }

    private function getLatestRankId() {
        $content = $this->solv_fetcher->fetchYearlyResultsJson($this->year_to_fetch);
        $data = json_decode($content, true);
        $result_lists = $data['ResultLists'];
        return $result_lists[count($result_lists) - 1]['ResultListID'];
    }
}
