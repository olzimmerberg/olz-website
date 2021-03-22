<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../fake/fake_strava_link.php';
require_once __DIR__.'/../../../../src/api/endpoints/LoginWithStravaEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/index.php';
require_once __DIR__.'/../../../../src/utils/auth/StravaUtils.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeLoginWithStravaEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'AuthRequest' => new FakeLoginWithStravaEndpointAuthRequestRepository(),
            'StravaLink' => new FakeLoginWithStravaEndpointStravaLinkRepository(),
            'User' => new FakeLoginWithStravaEndpointUserRepository(),
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

class FakeLoginWithStravaEndpointAuthRequestRepository {
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

class FakeLoginWithStravaEndpointStravaLinkRepository {
    public function findOneBy($where) {
        if ($where === ['strava_user' => 'fake_existing_id']) {
            $strava_link = get_fake_strava_link();
            $strava_link->setUser(get_fake_user());
            return $strava_link;
        }
        return null;
    }
}

class FakeLoginWithStravaEndpointUserRepository {
    public function findOneBy($where) {
        if ($where === ['username' => 'admin']) {
            $admin_user = get_fake_user();
            $admin_user->setUsername('admin');
            $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
            $admin_user->setZugriff('ftp');
            $admin_user->setRoot('karten');
            return $admin_user;
        }
        return null;
    }
}

class FakeLoginWithStravaEndpointStravaFetcher {
    public function __construct($response) {
        $this->response = $response;
    }

    public function fetchTokenDataForCode($token_request_data) {
        return $this->response;
    }
}

/**
 * @internal
 * @covers \LoginWithStravaEndpoint
 */
final class LoginWithStravaEndpointTest extends UnitTestCase {
    public function testLoginWithStravaEndpointIdent(): void {
        $endpoint = new LoginWithStravaEndpoint();
        $this->assertSame('LoginWithStravaEndpoint', $endpoint->getIdent());
    }

    public function testLoginWithStravaEndpointWithoutInput(): void {
        $entity_manager = new FakeLoginWithStravaEndpointEntityManager();
        $strava_fetcher = new FakeLoginWithStravaEndpointStravaFetcher([]);
        $strava_utils = new StravaUtils('fake-client-id', 'fake-client-secret', 'fake-redirect-url', $strava_fetcher);
        $logger = new Logger('LoginWithStravaEndpointTest');
        $endpoint = new LoginWithStravaEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setStravaUtils($strava_utils);
        $endpoint->setLogger($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'code' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testLoginWithStravaEndpointWithExistingUser(): void {
        $entity_manager = new FakeLoginWithStravaEndpointEntityManager();
        $strava_fetcher = new FakeLoginWithStravaEndpointStravaFetcher([
            'token_type' => 'fake_token_type',
            'expires_at' => 713014020,
            'refresh_token' => 'fake_refresh_token',
            'access_token' => 'fake_access_token',
            'athlete' => [
                'id' => 'fake_existing_id',
                'firstname' => 'fake_firstname',
                'lastname' => 'fake_lastname',
                'sex' => 'F',
                'city' => 'fake_city',
                'state' => 'fake_state',
                'country' => 'fake_country',
                'profile' => 'fake_profile',
            ],
        ]);
        $strava_utils = new StravaUtils('fake-client-id', 'fake-client-secret', 'fake-redirect-url', $strava_fetcher);
        $logger = new Logger('LoginWithStravaEndpointTest');
        $endpoint = new LoginWithStravaEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setStravaUtils($strava_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['code' => 'fake-code']);

        $this->assertSame([
            'status' => 'AUTHENTICATED',
            'tokenType' => null,
            'expiresAt' => null,
            'refreshToken' => null,
            'accessToken' => null,
            'userIdentifier' => null,
            'firstName' => null,
            'lastName' => null,
            'gender' => null,
            'city' => null,
            'region' => null,
            'country' => null,
            'profilePictureUrl' => null,
        ], $result);
        $this->assertSame([
            'auth' => null,
            'root' => null,
            'user' => 'user',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_STRAVA',
                'timestamp' => null,
                'username' => 'user',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
    }

    public function testLoginWithStravaEndpointWithNewUser(): void {
        $entity_manager = new FakeLoginWithStravaEndpointEntityManager();
        $strava_fetcher = new FakeLoginWithStravaEndpointStravaFetcher([
            'token_type' => 'fake_token_type',
            'expires_at' => 713021220,
            'refresh_token' => 'fake_refresh_token',
            'access_token' => 'fake_access_token',
            'athlete' => [
                'id' => 'fake_id',
                'firstname' => 'fake_firstname',
                'lastname' => 'fake_lastname',
                'sex' => 'F',
                'city' => 'fake_city',
                'state' => 'fake_state',
                'country' => 'fake_country',
                'profile' => 'fake_profile',
            ],
        ]);
        $strava_utils = new StravaUtils('fake-client-id', 'fake-client-secret', 'fake-redirect-url', $strava_fetcher);
        $logger = new Logger('LoginWithStravaEndpointTest');
        $endpoint = new LoginWithStravaEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setStravaUtils($strava_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['code' => 'fake-code']);

        $this->assertSame([
            'status' => 'NOT_REGISTERED',
            'tokenType' => 'fake_token_type',
            'expiresAt' => '1992-08-05 13:27:00',
            'refreshToken' => 'fake_refresh_token',
            'accessToken' => 'fake_access_token',
            'userIdentifier' => 'fake_id',
            'firstName' => 'fake_firstname',
            'lastName' => 'fake_lastname',
            'gender' => 'F',
            'city' => 'fake_city',
            'region' => 'fake_state',
            'country' => 'fake_country',
            'profilePictureUrl' => 'fake_profile',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([], $entity_manager->getRepository('AuthRequest')->auth_requests);
    }

    public function testLoginWithStravaEndpointWithInvalidCode(): void {
        $entity_manager = new FakeLoginWithStravaEndpointEntityManager();
        $strava_fetcher = new FakeLoginWithStravaEndpointStravaFetcher([
            'message' => 'Bad Request',
            'errors' => [
                [
                    'resource' => 'Application',
                    'field' => 'code',
                    'code' => 'invalid',
                ],
            ],
        ]);
        $strava_utils = new StravaUtils('fake-client-id', 'fake-client-secret', 'fake-redirect-url', $strava_fetcher);
        $logger = new Logger('LoginWithStravaEndpointTest');
        $endpoint = new LoginWithStravaEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setStravaUtils($strava_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['code' => 'invalid-code']);

        $this->assertSame([
            'status' => 'INVALID_CODE',
            'tokenType' => null,
            'expiresAt' => null,
            'refreshToken' => null,
            'accessToken' => null,
            'userIdentifier' => null,
            'firstName' => null,
            'lastName' => null,
            'gender' => null,
            'city' => null,
            'region' => null,
            'country' => null,
            'profilePictureUrl' => null,
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([], $entity_manager->getRepository('AuthRequest')->auth_requests);
    }
}
