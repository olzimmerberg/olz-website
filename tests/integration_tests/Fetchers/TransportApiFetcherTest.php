<?php

declare(strict_types=1);

use Olz\Fetchers\TransportApiFetcher;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \Olz\Fetchers\TransportApiFetcher
 */
final class TransportApiFetcherTest extends IntegrationTestCase {
    public function __construct() {
        parent::__construct();
        $this->transportApiFetcher = new TransportApiFetcher();
    }

    public function testCallTransportApi(): void {
        $content = $this->transportApiFetcher->fetchConnection([
            'from' => 'Zürich, Altes Krematorium',
            'to' => 'Zürich HB',
            'date' => date('Y-m-d'),
            'time' => '12:00:00',
            'isArrivalTime' => 1,
        ]);

        $content_keys = array_keys($content);
        sort($content_keys);
        if (isset($content['errors'])) {
            $this->assertSame(['errors'], $content_keys);
        } else {
            $this->assertSame(['connections', 'from', 'stations', 'to'], $content_keys);
            $this->assertGreaterThan(0, count($content['connections']));
        }
    }
}
