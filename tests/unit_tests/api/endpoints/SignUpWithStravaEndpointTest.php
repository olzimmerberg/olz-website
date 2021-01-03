<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../fake/fake_strava_link.php';
require_once __DIR__.'/../../../../src/api/endpoints/SignUpWithStravaEndpoint.php';
require_once __DIR__.'/../../../../src/model/index.php';
require_once __DIR__.'/../../../../src/utils/auth/StravaUtils.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

class FakeSignUpWithStravaEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'AuthRequest' => new FakeSignUpWithStravaEndpointAuthRequestRepository(),
        ];
    }

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }

    public function persist($object) {
        $this->persisted[] = $object;
    }

    public function flush() {
        $this->flushed = $this->persisted;
    }
}

class FakeSignUpWithStravaEndpointAuthRequestRepository {
    public $auth_requests = [];
    public $can_authenticate = true;

    public function addAuthRequest($ip_address, $action, $username, $timestamp = null) {
        $this->auth_requests[] = [
            'ip_address' => $ip_address,
            'action' => $action,
            'timestamp' => $timestamp,
            'username' => $username,
        ];
    }

    public function canAuthenticate($ip_address, $timestamp = null) {
        return $this->can_authenticate;
    }
}

/**
 * @internal
 * @covers \SignUpWithStravaEndpoint
 */
final class SignUpWithStravaEndpointTest extends TestCase {
    public function testSignUpWithStravaEndpointWithoutInput(): void {
        $entity_manager = new FakeSignUpWithStravaEndpointEntityManager();
        $endpoint = new SignUpWithStravaEndpoint($entity_manager);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'stravaUser' => ['Feld darf nicht leer sein.'],
                'accessToken' => ['Feld darf nicht leer sein.'],
                'refreshToken' => ['Feld darf nicht leer sein.'],
                'expiresAt' => ['Feld darf nicht leer sein.'],
                'firstName' => ['Feld darf nicht leer sein.'],
                'lastName' => ['Feld darf nicht leer sein.'],
                'username' => ['Feld darf nicht leer sein.'],
                'email' => ['Feld darf nicht leer sein.'],
                'street' => ['Feld darf nicht leer sein.'],
                'postalCode' => ['Feld darf nicht leer sein.'],
                'city' => ['Feld darf nicht leer sein.'],
                'region' => ['Feld darf nicht leer sein.'],
                'countryCode' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithStravaEndpointWithValidData(): void {
        $entity_manager = new FakeSignUpWithStravaEndpointEntityManager();
        $endpoint = new SignUpWithStravaEndpoint($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            'stravaUser' => 'fakeStravaUser',
            'accessToken' => 'fakeAccessToken',
            'refreshToken' => 'fakeRefreshToken',
            'expiresAt' => '1992-08-05 13:27:00',
            'firstName' => 'fakeFirstName',
            'lastName' => 'fakeLastName',
            'username' => 'fakeUsername',
            'email' => 'fakeEmail',
            'street' => 'fakeStreet',
            'postalCode' => 'fakePostalCode',
            'city' => 'fakeCity',
            'region' => 'fakeRegion',
            'countryCode' => 'fakeCountryCode',
        ]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_STRAVA',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
    }
}
