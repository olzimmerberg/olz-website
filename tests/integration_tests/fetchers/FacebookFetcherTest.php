<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/fetchers/FacebookFetcher.php';

/**
 * @internal
 * @covers \FacebookFetcher
 */
final class FacebookFetcherTest extends TestCase {
    public function __construct() {
        parent::__construct();
        $this->facebook_fetcher = new FacebookFetcher();
    }

    public function testFetchTokenDataForCode(): void {
        $content = $this->facebook_fetcher->fetchTokenDataForCode([]);
        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey('message', $content['error']);
        $this->assertArrayHasKey('type', $content['error']);
        $this->assertArrayHasKey('code', $content['error']);
        $this->assertArrayHasKey('fbtrace_id', $content['error']);
        $this->assertSame('Missing redirect_uri parameter.', $content['error']['message']);
        $this->assertSame('OAuthException', $content['error']['type']);
        $this->assertSame(191, $content['error']['code']);
    }

    public function testFetchUserData(): void {
        $content = $this->facebook_fetcher->fetchUserData([]);
        $this->assertSame(null, $content);
    }
}
