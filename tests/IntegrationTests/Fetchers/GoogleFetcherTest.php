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
    protected $google_fetcher;

    public function __construct() {
        parent::__construct();
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
