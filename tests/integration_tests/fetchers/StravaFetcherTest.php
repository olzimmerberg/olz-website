<?php

declare(strict_types=1);

require_once __DIR__.'/../../../public/_/fetchers/StravaFetcher.php';
require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \StravaFetcher
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
