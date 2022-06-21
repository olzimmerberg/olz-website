<?php

declare(strict_types=1);

use Olz\Fetchers\StravaFetcher;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \Olz\Fetchers\StravaFetcher
 */
final class StravaFetcherTest extends IntegrationTestCase {
    public function __construct() {
        parent::__construct();
        $this->strava_fetcher = new StravaFetcher();
    }

    public function testFetchTokenDataForCode(): void {
        $content = $this->strava_fetcher->fetchTokenDataForCode([]);
        $this->assertSame([
            'message' => 'Bad Request',
            'errors' => [
                [
                    'resource' => 'Application',
                    'field' => 'client_id',
                    'code' => 'invalid',
                ],
            ],
        ], $content);
    }
}
