<?php

declare(strict_types=1);

use Olz\Utils\FacebookUtils;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../common/UnitTestCase.php';

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
 * @covers \Olz\Utils\FacebookUtils
 */
final class FacebookUtilsTest extends UnitTestCase {
    private $facebookUtils;
    private $facebook_fetcher;
    private $dateUtils;

    public function __construct() {
        global $sample_facebook_token_response, $sample_facebook_user_data_response;
        parent::__construct();
        $this->dateUtils = new FixedDateUtils('2020-03-13 19:30:00');
        $this->facebook_fetcher = new FakeFacebookUtilsFacebookFetcher($sample_facebook_token_response, $sample_facebook_user_data_response);
        $facebookUtils = new FacebookUtils();
        $facebookUtils->setAppId('fake-app-id');
        $facebookUtils->setAppSecret('fake-app-secret');
        $facebookUtils->setRedirectUrl('fake-redirect-url');
        $facebookUtils->setDateUtils($this->dateUtils);
        $facebookUtils->setFacebookFetcher($this->facebook_fetcher);
        $this->facebookUtils = $facebookUtils;
    }

    public function testModifyFacebookUtils(): void {
        global $sample_facebook_token_response, $sample_facebook_user_data_response;
        $facebook_fetcher = new FakeFacebookUtilsFacebookFetcher($sample_facebook_token_response, $sample_facebook_user_data_response);
        $facebookUtils = new FacebookUtils();
        $facebookUtils->setAppId('fake-app-id');
        $facebookUtils->setAppSecret('fake-app-secret');
        $facebookUtils->setRedirectUrl('fake-redirect-url');
        $facebookUtils->setDateUtils($this->dateUtils);
        $facebookUtils->setFacebookFetcher($facebook_fetcher);

        $facebookUtils->setAppId('new-app-id');
        $facebookUtils->setAppSecret('new-app-secret');

        $this->assertSame(
            'https://www.facebook.com/v8.0/dialog/oauth'.
                '?client_id=new-app-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&scope=email,public_profile',
            urldecode($facebookUtils->getAuthUrl())
        );
    }

    public function testGetAuthUrl(): void {
        $this->assertSame(
            'https://www.facebook.com/v8.0/dialog/oauth'.
                '?client_id=fake-app-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&scope=email,public_profile',
            urldecode($this->facebookUtils->getAuthUrl())
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
        ], $this->facebookUtils->getTokenDataForCode('fake-code'));
    }

    public function testGetUserData(): void {
        $this->assertSame('fake-code', $this->facebookUtils->getUserData('fake-code'));
    }
}
