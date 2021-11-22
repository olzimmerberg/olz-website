<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fetchers/ContinueAsyncTaskFetcher.php';
require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \ContinueAsyncTaskFetcher
 */
final class ContinueAsyncTaskFetcherTest extends IntegrationTestCase {
    public function __construct() {
        parent::__construct();
        $this->continueAsyncTaskFetcher = new ContinueAsyncTaskFetcher();
    }

    public function testCallTransportApi(): void {
        $result = $this->continueAsyncTaskFetcher->continueAsyncTask('https://olzimmerberg.ch');
        $this->assertSame('', $result);
    }
}
