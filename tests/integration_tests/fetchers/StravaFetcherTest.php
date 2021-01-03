<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/fetchers/StravaFetcher.php';

/**
 * @internal
 * @covers \StravaFetcher
 */
final class StravaFetcherTest extends TestCase {
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
