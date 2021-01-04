<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/utils/auth/FacebookUtils.php';

$sample_facebook_token_response = [
    "token_type" => "Bearer",
    "expires_in" => 21600,
    "refresh_token" => "e5n567567...",
    "access_token" => "a4b945687g...",
];

$sample_facebook_user_data_response = [
    "id" => "fake-id",
    "first_name" => "Max",
    "last_name" => "Muster",
    "email" => "max@muster.ch",
    "picture" => ["data" => ["url" => "http://fake-url"]],
];

class FakeFacebookUtilsFacebookFetcher {
    private $facebook_token_response;
    private $facebook_user_data_response;

    public function __construct($facebook_token_response, $facebook_user_data_response) {
        $this->facebook_token_response = $facebook_token_response;
        $this->facebook_user_data_response = $facebook_user_data_response;
    }

    public function fetchTokenDataForCode($request_data) {
        return $this->facebook_token_response;
    }

    public function fetchUserData($request_data) {
        return $this->facebook_user_data_response;
    }
}

/**
 * @internal
 * @covers \FacebookUtils
 */
final class FacebookUtilsTest extends TestCase {
    private $facebook_utils;
    private $facebook_fetcher;
    private $date_utils;

    public function __construct() {
        global $sample_facebook_token_response, $sample_facebook_user_data_response;
        parent::__construct();
        $this->date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $this->facebook_fetcher = new FakeFacebookUtilsFacebookFetcher($sample_facebook_token_response, $sample_facebook_user_data_response);
        $this->facebook_utils = new FacebookUtils('fake-app-id', 'fake-app-secret', 'fake-redirect-url', $this->facebook_fetcher, $this->date_utils);
    }

    public function testModifyFacebookUtils(): void {
        global $sample_facebook_token_response, $sample_facebook_user_data_response;
        $facebook_fetcher = new FakeFacebookUtilsFacebookFetcher($sample_facebook_token_response, $sample_facebook_user_data_response);

        $facebook_utils = new FacebookUtils('fake-app-id', 'fake-app-secret', 'fake-redirect-url', $facebook_fetcher, $this->date_utils);
        $facebook_utils->setAppId('new-app-id');
        $facebook_utils->setAppSecret('new-app-secret');

        $this->assertSame(
            'https://www.facebook.com/v8.0/dialog/oauth'.
                '?client_id=new-app-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&scope=email,public_profile',
            urldecode($facebook_utils->getAuthUrl())
        );
    }

    public function testGetAuthUrl(): void {
        $this->assertSame(
            'https://www.facebook.com/v8.0/dialog/oauth'.
                '?client_id=fake-app-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&scope=email,public_profile',
                urldecode($this->facebook_utils->getAuthUrl())
        );
    }

    public function testGetTokenDataForCode(): void {
        $this->assertSame([
            'token_type' => 'Bearer',
            'expires_at' => 1584149400,
            'refresh_token' => null,
            'access_token' => 'a4b945687g...',
            'user_identifier' => 'fake-id',
            'first_name' => 'Max',
            'last_name' => 'Muster',
            'email' => 'max@muster.ch',
            'verified_email' => true,
            'profile_picture_url' => 'http://fake-url',
        ], $this->facebook_utils->getTokenDataForCode('fake-code'));
    }

    public function testGetUserData(): void {
        $this->assertSame('fake-code', $this->facebook_utils->getUserData('fake-code'));
    }
}
