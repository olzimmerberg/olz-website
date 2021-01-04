<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/fetchers/GoogleFetcher.php';

/**
 * @internal
 * @covers \GoogleFetcher
 */
final class GoogleFetcherTest extends TestCase {
    public function __construct() {
        parent::__construct();
        $this->google_fetcher = new GoogleFetcher();
    }

    public function testFetchTokenDataForCode(): void {
        $content = $this->google_fetcher->fetchTokenDataForCode([]);
        $this->assertSame([
            'error' => 'unsupported_grant_type',
            'error_description' => 'Invalid grant_type: ',
        ], $content);
    }

    public function testFetchUserData(): void {
        $content = $this->google_fetcher->fetchUserData([], []);
        $this->assertSame([
            'error' => [
                'code' => 401,
                'message' => 'Request is missing required authentication credential. Expected OAuth 2 access token, login cookie or other valid authentication credential. See https://developers.google.com/identity/sign-in/web/devconsole-project.',
                'status' => 'UNAUTHENTICATED',
            ],
        ], $content);
    }

    public function testFetchPeopleApiData(): void {
        $content = $this->google_fetcher->fetchPeopleApiData([], []);
        $this->assertSame([
            'error' => [
                'code' => 403,
                'message' => 'The request is missing a valid API key.',
                'status' => 'PERMISSION_DENIED',
            ],
        ], $content);
    }
}
