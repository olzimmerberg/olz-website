<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/utils/auth/GoogleUtils.php';

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

/**
 * @internal
 * @coversNothing
 */
final class GoogleUtilsTest extends TestCase {
    private $google_utils;

    public function __construct() {
        parent::__construct();
        $this->google_utils = new GoogleUtils('fake-client-id', 'fake-client-secret', 'fake-redirect-url');
    }

    public function testExtractFirstName(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Given', $this->google_utils->extractFirstName($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractFirstName($empty_people_api_response));
    }

    public function testExtractLastName(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Family', $this->google_utils->extractLastName($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractLastName($empty_people_api_response));
    }

    public function testExtractGender(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('M', $this->google_utils->extractGender($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractGender($empty_people_api_response));
    }

    public function testExtractStreet(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Tischenloostrasse 57', $this->google_utils->extractStreet($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractStreet($empty_people_api_response));
    }

    public function testExtractPostalCode(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('8800', $this->google_utils->extractPostalCode($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractPostalCode($empty_people_api_response));
    }

    public function testExtractCity(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Thalwil', $this->google_utils->extractCity($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractCity($empty_people_api_response));
    }

    public function testExtractRegion(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('Kanton Zürich', $this->google_utils->extractRegion($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractRegion($empty_people_api_response));
    }

    public function testExtractCountry(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('CH', $this->google_utils->extractCountry($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractCountry($empty_people_api_response));
    }

    public function testExtractBirthday(): void {
        global $google_utils, $sample_people_api_response, $empty_people_api_response;
        $this->assertSame('1989-11-09', $this->google_utils->extractBirthday($sample_people_api_response));
        $this->assertSame(null, $this->google_utils->extractBirthday($empty_people_api_response));
    }
}
