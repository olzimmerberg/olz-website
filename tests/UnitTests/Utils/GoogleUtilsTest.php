<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\GoogleUtils;
use Olz\Utils\WithUtilsCache;

$sample_google_token_response = [
    "token_type" => "Bearer",
    "expires_in" => 21600,
    "refresh_token" => "e5n567567...",
    "access_token" => "a4b945687g...",
];

$sample_google_userinfo_response = [
    "id" => "fake-id",
    "given_name" => "Max",
    "family_name" => "Muster",
    "email" => "max@muster.ch",
    "verified_email" => true,
    "picture" => "http://fake-url",
];

$sample_people_api_response = [
    "resourceName" => "people/123456789012345678901",
    "etag" => "%Eg4BAj0HCAk+Cz8QQBk3LhoEAQIFByIMRnhibUsyNUJPMkU9",
    "addresses" => [
        [
            "metadata" => [
                "primary" => true,
                "source" => [
                    "type" => "PROFILE",
                    "id" => "123456789012345678901",
                ],
            ],
            "formattedValue" => "Tischenloostrasse 57, 8800 Thalwil, Kanton Zürich, Switzerland",
            "type" => "",
            "formattedType" => "",
            "poBox" => "",
            "streetAddress" => "Tischenloostrasse",
            "extendedAddress" => "57",
            "city" => "Thalwil",
            "region" => "Kanton Zürich",
            "postalCode" => "8800",
            "country" => "Switzerland",
            "countryCode" => "CH",
        ],
    ],
    "names" => [
        [
            "metadata" => [
                "primary" => true,
                "source" => [
                    "type" => "PROFILE",
                    "id" => "123456789012345678901",
                ],
            ],
            "displayName" => "Given Middle Family",
            "familyName" => "Family",
            "givenName" => "Given",
            "middleName" => "Middle",
            "displayNameLastFirst" => "Family, Given Middle",
            "unstructuredName" => "Given Middle Family",
        ],
    ],
    "coverPhotos" => [
        [
            "metadata" => [
                "primary" => true,
                "source" => [
                    "type" => "PROFILE",
                    "id" => "123456789012345678901",
                ],
            ],
            "url" => "https://data.repo/profile_picture.jpg",
            "default" => true,
        ],
    ],
    "genders" => [
        [
            "metadata" => [
                "primary" => true,
                "source" => [
                    "type" => "PROFILE",
                    "id" => "123456789012345678901",
                ],
            ],
            "value" => "male",
            "formattedValue" => "Male",
        ],
    ],
    "birthdays" => [
        [
            "metadata" => [
                "primary" => true,
                "source" => [
                    "type" => "PROFILE",
                    "id" => "123456789012345678901",
                ],
            ],
            "date" => [
                "month" => 11,
                "day" => 9,
            ],
        ],
        [
            "metadata" => [
                "source" => [
                    "type" => "ACCOUNT",
                    "id" => "123456789012345678901",
                ],
            ],
            "date" => [
                "year" => 1989,
                "month" => 11,
                "day" => 9,
            ],
        ],
    ],
    "emailAddresses" => [
        [
            "metadata" => [
                "primary" => true,
                "verified" => true,
                "source" => [
                    "type" => "ACCOUNT",
                    "id" => "123456789012345678901",
                ],
            ],
            "value" => "allestuetsmerweh@gmail.com",
        ],
        [
            "metadata" => [
                "verified" => true,
                "source" => [
                    "type" => "PROFILE",
                    "id" => "123456789012345678901",
                ],
            ],
            "value" => "allestuetsmerweh@gmail.com",
            "type" => "home",
            "formattedType" => "Home",
        ],
    ],
];

$empty_people_api_response = [
    "resourceName" => "people/123456789012345678901",
    "etag" => "%Eg4BAj0HCAk+Cz8QQBk3LhoEAQIFByIMRnhibUsyNUJPMkU9",
    "addresses" => [],
    "names" => [],
    "coverPhotos" => [],
    "genders" => [],
    "birthdays" => [],
    "emailAddresses" => [],
];

class FakeGoogleUtilsGoogleFetcher {
    private $google_token_response;
    private $google_user_data_response;
    private $google_people_api_response;

    public function __construct($google_token_response, $google_user_data_response, $google_people_api_response) {
        $this->google_token_response = $google_token_response;
        $this->google_user_data_response = $google_user_data_response;
        $this->google_people_api_response = $google_people_api_response;
    }

    public function fetchTokenDataForCode($request_data) {
        return $this->google_token_response;
    }

    public function fetchUserData($request_data, $token_data) {
        return $this->google_user_data_response;
    }

    public function fetchPeopleApiData($request_data, $token_data) {
        return $this->google_people_api_response;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\GoogleUtils
 */
final class GoogleUtilsTest extends UnitTestCase {
    private $googleUtils;
    private $google_fetcher;
    private $dateUtils;

    public function __construct() {
        global $sample_google_token_response, $sample_google_userinfo_response, $sample_people_api_response;
        parent::__construct();
        $this->dateUtils = new FixedDateUtils('2020-03-13 19:30:00');
        $this->google_fetcher = new FakeGoogleUtilsGoogleFetcher($sample_google_token_response, $sample_google_userinfo_response, $sample_people_api_response);
        $googleUtils = new GoogleUtils();
        $googleUtils->setClientId('fake-client-id');
        $googleUtils->setClientSecret('fake-client-secret');
        $googleUtils->setRedirectUrl('fake-redirect-url');
        $googleUtils->setDateUtils($this->dateUtils);
        $googleUtils->setGoogleFetcher($this->google_fetcher);
        $this->googleUtils = $googleUtils;
    }

    public function testModifyGoogleUtils(): void {
        global $sample_google_token_response, $sample_google_userinfo_response, $sample_people_api_response;
        $fake_google_fetcher = new FakeGoogleUtilsGoogleFetcher($sample_google_token_response, $sample_google_userinfo_response, $sample_people_api_response);
        $googleUtils = new GoogleUtils();
        $googleUtils->setClientId('fake-client-id');
        $googleUtils->setClientSecret('fake-client-secret');
        $googleUtils->setRedirectUrl('fake-redirect-url');
        $googleUtils->setDateUtils($this->dateUtils);
        $googleUtils->setGoogleFetcher($fake_google_fetcher);

        $googleUtils->setClientId('new-client-id');
        $googleUtils->setClientSecret('new-client-secret');

        $this->assertSame(
            'https://accounts.google.com/o/oauth2/v2/auth'.
                '?client_id=new-client-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&scope=https://www.googleapis.com/auth/userinfo.profile '.
                'https://www.googleapis.com/auth/userinfo.email '.
                'https://www.googleapis.com/auth/user.addresses.read '.
                'https://www.googleapis.com/auth/user.birthday.read '.
                'https://www.googleapis.com/auth/user.gender.read '.
                'https://www.googleapis.com/auth/user.phonenumbers.read',
            urldecode($googleUtils->getAuthUrl())
        );
    }

    public function testGetAuthUrl(): void {
        $this->assertSame(
            'https://accounts.google.com/o/oauth2/v2/auth'.
                '?client_id=fake-client-id'.
                '&redirect_uri=fake-redirect-url'.
                '&response_type=code'.
                '&scope=https://www.googleapis.com/auth/userinfo.profile '.
                'https://www.googleapis.com/auth/userinfo.email '.
                'https://www.googleapis.com/auth/user.addresses.read '.
                'https://www.googleapis.com/auth/user.birthday.read '.
                'https://www.googleapis.com/auth/user.gender.read '.
                'https://www.googleapis.com/auth/user.phonenumbers.read',
            urldecode($this->googleUtils->getAuthUrl())
        );
    }

    public function testGetTokenDataForCode(): void {
        WithUtilsCache::setAll([
            'dateUtils' => new FixedDateUtils('2020-03-13 19:30:00'),
        ]);

        $this->assertSame([
            'token_type' => 'Bearer',
            'expires_at' => 1584149400,
            'refresh_token' => 'e5n567567...',
            'access_token' => 'a4b945687g...',
            'user_identifier' => 'fake-id',
            'first_name' => 'Max',
            'last_name' => 'Muster',
            'email' => 'max@muster.ch',
            'verified_email' => true,
            'profile_picture_url' => 'http://fake-url',
        ], $this->googleUtils->getTokenDataForCode('fake-code'));
    }

    public function testGetUserData(): void {
        $this->assertSame([
            'gender' => 'M',
            'postalCode' => '8800',
            'city' => 'Thalwil',
            'region' => 'Kanton Zürich',
            'country' => 'CH',
            'birthday' => '1989-11-09',
        ], $this->googleUtils->getUserData([], []));
    }

    public function testExtractFirstName(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Given', $this->googleUtils->extractFirstName($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractFirstName($empty_people_api_response));
    }

    public function testExtractLastName(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Family', $this->googleUtils->extractLastName($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractLastName($empty_people_api_response));
    }

    public function testExtractGender(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('M', $this->googleUtils->extractGender($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractGender($empty_people_api_response));
    }

    public function testExtractStreet(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Tischenloostrasse 57', $this->googleUtils->extractStreet($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractStreet($empty_people_api_response));
    }

    public function testExtractPostalCode(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('8800', $this->googleUtils->extractPostalCode($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractPostalCode($empty_people_api_response));
    }

    public function testExtractCity(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Thalwil', $this->googleUtils->extractCity($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractCity($empty_people_api_response));
    }

    public function testExtractRegion(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Kanton Zürich', $this->googleUtils->extractRegion($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractRegion($empty_people_api_response));
    }

    public function testExtractCountry(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('CH', $this->googleUtils->extractCountry($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractCountry($empty_people_api_response));
    }

    public function testExtractBirthday(): void {
        global $googleUtils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('1989-11-09', $this->googleUtils->extractBirthday($sample_people_api_response));
        $this->assertSame(null, $this->googleUtils->extractBirthday($empty_people_api_response));
    }
}
