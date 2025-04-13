<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Fetchers\StravaFetcher;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\StravaUtils;

$sample_strava_fetcher_response = [
    "token_type" => "Bearer",
    "expires_at" => 1568775134,
    "expires_in" => 21600,
    "refresh_token" => "e5n567567...",
    "access_token" => "a4b945687g...",
    "athlete" => [
        "id" => "fake-id",
        "firstname" => "Max",
        "lastname" => "Muster",
        "sex" => "M",
        "city" => "Uster",
        "state" => "Zürich",
        "country" => "Switzerland",
        "profile" => "http://fake-url",
    ],
];

$empty_people_api_response = [
    'message' => 'Bad Request',
    'errors' => [
        [
            'resource' => 'Application',
            'field' => 'client_id',
            'code' => 'invalid',
        ],
    ],
];

class TestOnlyStravaFetcher extends StravaFetcher {
    /** @var array<string, mixed> */
    private ?array $strava_fetcher_response;

    /** @param array<string, mixed> $strava_fetcher_response */
    public function __construct(?array $strava_fetcher_response) {
        $this->strava_fetcher_response = $strava_fetcher_response;
    }

    /**
     * @param array<string, mixed> $request_data
     *
     * @return ?array<string, mixed>
     */
    public function fetchTokenDataForCode(array $request_data): ?array {
        return $this->strava_fetcher_response;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\StravaUtils
 */
final class StravaUtilsTest extends UnitTestCase {
    private StravaUtils $stravaUtils;
    protected TestOnlyStravaFetcher $fake_strava_fetcher;

    protected function setUp(): void {
        global $sample_strava_fetcher_response;
        parent::setUp();
        $this->fake_strava_fetcher = new TestOnlyStravaFetcher($sample_strava_fetcher_response);
        $stravaUtils = new StravaUtils();
        $stravaUtils->setClientId('fake-client-id');
        $stravaUtils->setClientSecret('fake-client-secret');
        $stravaUtils->setRedirectUrl('fake-redirect-url');
        $stravaUtils->setStravaFetcher($this->fake_strava_fetcher);
        $this->stravaUtils = $stravaUtils;
    }

    public function testModifyStravaUtils(): void {
        global $sample_strava_fetcher_response;
        $fake_strava_fetcher = new TestOnlyStravaFetcher($sample_strava_fetcher_response);
        $stravaUtils = new StravaUtils();
        $stravaUtils->setClientId('fake-client-id');
        $stravaUtils->setClientSecret('fake-client-secret');
        $stravaUtils->setRedirectUrl('fake-redirect-url');
        $stravaUtils->setStravaFetcher($fake_strava_fetcher);

        $stravaUtils->setClientId('new-client-id');
        $stravaUtils->setClientSecret('new-client-secret');

        $this->assertSame(
            'https://www.strava.com/oauth/authorize'.
                '?client_id=new-client-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&approval_prompt=auto'.
                '&scope=profile:read_all',
            str_replace('&amp;', '&', urldecode($stravaUtils->getAuthUrl()))
        );
    }

    public function testGetAuthUrl(): void {
        $this->assertSame(
            'https://www.strava.com/oauth/authorize'.
                '?client_id=fake-client-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&approval_prompt=auto'.
                '&scope=profile:read_all',
            str_replace('&amp;', '&', urldecode($this->stravaUtils->getAuthUrl()))
        );
    }

    public function testGetTokenDataForCode(): void {
        $this->assertSame([
            'token_type' => 'Bearer',
            'expires_at' => 1568775134,
            'refresh_token' => 'e5n567567...',
            'access_token' => 'a4b945687g...',
            'user_identifier' => 'fake-id',
            'first_name' => 'Max',
            'last_name' => 'Muster',
            'gender' => 'M',
            'city' => 'Uster',
            'region' => 'Zürich',
            'country' => 'Switzerland',
            'profile_picture_url' => 'http://fake-url',
        ], $this->stravaUtils->getTokenDataForCode('fake-code'));
    }

    public function testGetTokenDataForInvalidCode(): void {
        global $empty_people_api_response;
        $fake_strava_fetcher = new TestOnlyStravaFetcher($empty_people_api_response);
        $stravaUtils = new StravaUtils();
        $stravaUtils->setClientId('fake-client-id');
        $stravaUtils->setClientSecret('fake-client-secret');
        $stravaUtils->setRedirectUrl('fake-redirect-url');
        $stravaUtils->setStravaFetcher($fake_strava_fetcher);

        $this->assertNull($stravaUtils->getTokenDataForCode('fake-code'));
    }

    public function testGetUserData(): void {
        $this->assertSame(['fake' => 'code'], $this->stravaUtils->getUserData(['fake' => 'code']));
    }
}
