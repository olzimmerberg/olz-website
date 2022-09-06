<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Fetchers;

use Olz\Fetchers\StravaFetcher;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
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
