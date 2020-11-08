<?php

class GoogleUtils {
    private $client_id;
    private $client_secret;
    private $redirect_url;

    public function __construct($client_id, $client_secret, $redirect_url) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_url = $redirect_url;
    }

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function setClientSecret($client_secret) {
        $this->client_secret = $client_secret;
    }

    public function getAuthUrl() {
        $google_auth_url = 'https://accounts.google.com/o/oauth2/v2/auth';
        $google_scopes = [
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/user.addresses.read',
            'https://www.googleapis.com/auth/user.birthday.read',
            // 'https://www.googleapis.com/auth/user.emails.read',
            'https://www.googleapis.com/auth/user.gender.read',
            'https://www.googleapis.com/auth/user.phonenumbers.read',
        ];
        $data = [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_url,
            'response_type' => 'code',
            'scope' => implode(' ', $google_scopes),
        ];
        return "{$google_auth_url}?".http_build_query($data, '', '&');
    }

    public function getTokenDataForCode($code) {
        $ch = curl_init();

        $google_token_url = 'https://oauth2.googleapis.com/token';
        $token_request_data = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirect_url,
        ];
        curl_setopt($ch, CURLOPT_URL, $google_token_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $token_result = curl_exec($ch);
        $token_response = json_decode($token_result, true);

        curl_reset($ch);

        $google_userinfo_url = 'https://www.googleapis.com/oauth2/v1/userinfo';
        $userinfo_request_data = [
            'alt' => 'json',
        ];
        curl_setopt($ch, CURLOPT_URL, $google_userinfo_url.'?'.http_build_query($userinfo_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: {$token_response['token_type']} {$token_response['access_token']}",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userinfo_result = curl_exec($ch);
        $userinfo_response = json_decode($userinfo_result, true);

        curl_close($ch);

        return [
            'token_type' => $token_response['token_type'],
            'expires_at' => time() + intval($token_response['expires_in']),
            'refresh_token' => $token_response['refresh_token'],
            'access_token' => $token_response['access_token'],
            'user_identifier' => $userinfo_response['id'],
            'first_name' => $userinfo_response['given_name'],
            'last_name' => $userinfo_response['family_name'],
            'email' => $userinfo_response['email'],
            'verified_email' => $userinfo_response['verified_email'],
            'profile_picture_url' => $userinfo_response['picture'],
        ];
    }

    public function getUserData($token_data) {
        $ch = curl_init();

        $google_token_url = 'https://people.googleapis.com/v1/people/me';
        $data = [
            'personFields' => 'phoneNumbers,names,genders,coverPhotos,addresses,birthdays,emailAddresses',
        ];
        curl_setopt($ch, CURLOPT_URL, $google_token_url.'?'.http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: {$token_data['token_type']} {$token_data['access_token']}",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $response = json_decode($result, true);

        curl_close($ch);

        // TODO: phone number (is not being returned currently)
        // TODO: email (in case userinfo did not get it)
        // TODO: cover photo (in case userinfo did not get it)
        // TODO: parse address (only `formattedValue` is currently returned)
        return array_merge(
            $token_data,
            [
                'gender' => $this->extractGender($response),
                'postalCode' => $this->extractPostalCode($response),
                'city' => $this->extractCity($response),
                'region' => $this->extractRegion($response),
                'country' => $this->extractCountry($response),
                'birthday' => $this->extractBirthday($response),
            ],
        );
    }

    public function extractFirstName($people_api_response) {
        $name = $this->extractPrimaryName($people_api_response);
        if ($name == null) {
            return null;
        }
        return $name['givenName'];
    }

    public function extractLastName($people_api_response) {
        $name = $this->extractPrimaryName($people_api_response);
        if ($name == null) {
            return null;
        }
        return $name['familyName'];
    }

    public function extractPrimaryName($people_api_response) {
        $names = $people_api_response['names'] ?? [];
        foreach ($names as $name) {
            $metadata = $name['metadata'] ?? [];
            $primary = $metadata['primary'] ?? false;
            if ($primary) {
                return $name;
            }
        }
        return null;
    }

    public function extractGender($people_api_response) {
        $gender_mapping = [
            'male' => 'M',
            'female' => 'F',
            'unspecified' => 'O',
        ];
        $genders = $people_api_response['genders'] ?? [];
        foreach ($genders as $gender) {
            $metadata = $gender['metadata'] ?? [];
            $primary = $metadata['primary'] ?? false;
            if ($primary) {
                return $gender_mapping[$gender['value']] ?? null;
            }
        }
        return null;
    }

    public function extractStreet($people_api_response) {
        $address = $this->extractPrimaryAddress($people_api_response);
        if ($address == null) {
            return null;
        }
        $street = $address['streetAddress'];
        if ($address['extendedAddress'] ?? false) {
            $street .= ' '.$address['extendedAddress'];
        }
        return $street;
    }

    public function extractPostalCode($people_api_response) {
        $address = $this->extractPrimaryAddress($people_api_response);
        if ($address == null) {
            return null;
        }
        return $address['postalCode'];
    }

    public function extractCity($people_api_response) {
        $address = $this->extractPrimaryAddress($people_api_response);
        if ($address == null) {
            return null;
        }
        return $address['city'];
    }

    public function extractRegion($people_api_response) {
        $address = $this->extractPrimaryAddress($people_api_response);
        if ($address == null) {
            return null;
        }
        return $address['region'];
    }

    public function extractCountry($people_api_response) {
        $address = $this->extractPrimaryAddress($people_api_response);
        if ($address == null) {
            return null;
        }
        return $address['countryCode'];
    }

    public function extractPrimaryAddress($people_api_response) {
        $addresses = $people_api_response['addresses'] ?? [];
        foreach ($addresses as $address) {
            $metadata = $address['metadata'] ?? [];
            $primary = $metadata['primary'] ?? false;
            if ($primary) {
                return $address;
            }
        }
        return null;
    }

    public function extractBirthday($people_api_response) {
        $birthdays = $people_api_response['birthdays'] ?? [];
        $full_birthdays = array_values(array_filter(
            $birthdays,
            function ($birthday) {
                $date = $birthday['date'] ?? [];
                return isset($date['year'], $date['month'], $date['day']);
            },
        ));
        foreach ($full_birthdays as $full_birthday) {
            $metadata = $full_birthday['metadata'] ?? [];
            $primary = $metadata['primary'] ?? false;
            if ($primary) {
                $date = $full_birthday['date'];
                return $this->googleDateToISO($date);
            }
        }
        if (count($full_birthdays) == 0) {
            return null;
        }
        $date = $full_birthdays[0]['date'];
        return $this->googleDateToISO($date);
    }

    public function googleDateToISO($date) {
        $year = str_pad($date['year'], 4, '0', STR_PAD_LEFT);
        $month = str_pad($date['month'], 2, '0', STR_PAD_LEFT);
        $day = str_pad($date['day'], 2, '0', STR_PAD_LEFT);
        return "{$year}-{$month}-{$day}";
    }
}

function getGoogleUtilsFromEnv() {
    global $base_href, $code_href, $GOOGLE_CLIENT_ID, $GOOGLE_CLIENT_SECRET;
    require_once __DIR__.'/../../config/paths.php';
    require_once __DIR__.'/../../config/server.php';

    $redirect_url = $base_href.$code_href.'konto_google.php';

    return new GoogleUtils($GOOGLE_CLIENT_ID, $GOOGLE_CLIENT_SECRET, $redirect_url);
}
