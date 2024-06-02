<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Fetchers;

use Olz\Fetchers\GoogleFetcher;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\Fetchers\GoogleFetcher
 */
final class GoogleFetcherTest extends IntegrationTestCase {
    protected GoogleFetcher $google_fetcher;

    public function setUp(): void {
        parent::setUp();
        $this->google_fetcher = new GoogleFetcher();
    }

    public function testFetchRecaptchaVerification(): void {
        $content = $this->google_fetcher->fetchRecaptchaVerification([
            'secret' => 'test',
            'response' => 'test',
        ]);
        $this->assertSame([
            'success' => false,
            'error-codes' => ['invalid-input-secret'],
        ], $content);
    }
}
