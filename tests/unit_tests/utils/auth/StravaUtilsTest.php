<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/auth/StravaUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

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

class FakeStravaUtilsStravaFetcher {
    private $strava_fetcher_response;

    public function __construct($strava_fetcher_response) {
        $this->strava_fetcher_response = $strava_fetcher_response;
    }

    public function fetchTokenDataForCode($request_data) {
        return $this->strava_fetcher_response;
    }
}

/**
 * @internal
 * @covers \StravaUtils
 */
final class StravaUtilsTest extends UnitTestCase {
    private $strava_utils;

    public function __construct() {
        global $sample_strava_fetcher_response;
        parent::__construct();
        $this->fake_strava_fetcher = new FakeStravaUtilsStravaFetcher($sample_strava_fetcher_response);
        $strava_utils = new StravaUtils();
        $strava_utils->setClientId('fake-client-id');
        $strava_utils->setClientSecret('fake-client-secret');
        $strava_utils->setRedirectUrl('fake-redirect-url');
        $strava_utils->setStravaFetcher($this->fake_strava_fetcher);
        $this->strava_utils = $strava_utils;
    }

    public function testModifyStravaUtils(): void {
        global $sample_strava_fetcher_response;
        $fake_strava_fetcher = new FakeStravaUtilsStravaFetcher($sample_strava_fetcher_response);
        $strava_utils = new StravaUtils();
        $strava_utils->setClientId('fake-client-id');
        $strava_utils->setClientSecret('fake-client-secret');
        $strava_utils->setRedirectUrl('fake-redirect-url');
        $strava_utils->setStravaFetcher($fake_strava_fetcher);

        $strava_utils->setClientId('new-client-id');
        $strava_utils->setClientSecret('new-client-secret');

        $this->assertSame(
            'https://www.strava.com/oauth/authorize'.
                '?client_id=new-client-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&approval_prompt=auto'.
                '&scope=profile:read_all',
            str_replace('&amp;', '&', urldecode($strava_utils->getAuthUrl()))
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
                str_replace('&amp;', '&', urldecode($this->strava_utils->getAuthUrl()))
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
        ], $this->strava_utils->getTokenDataForCode('fake-code'));
    }

    public function testGetTokenDataForInvalidCode(): void {
        global $empty_people_api_response;
        $fake_strava_fetcher = new FakeStravaUtilsStravaFetcher($empty_people_api_response);
        $strava_utils = new StravaUtils();
        $strava_utils->setClientId('fake-client-id');
        $strava_utils->setClientSecret('fake-client-secret');
        $strava_utils->setRedirectUrl('fake-redirect-url');
        $strava_utils->setStravaFetcher($fake_strava_fetcher);

        $this->assertSame(null, $strava_utils->getTokenDataForCode('fake-code'));
    }

    public function testGetUserData(): void {
        $this->assertSame('fake-code', $this->strava_utils->getUserData('fake-code'));
    }
}
