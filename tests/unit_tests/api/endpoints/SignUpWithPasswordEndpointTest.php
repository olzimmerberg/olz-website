<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/fake_strava_link.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeUserRepository.php';
require_once __DIR__.'/../../../../src/api/endpoints/SignUpWithPasswordEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/index.php';
require_once __DIR__.'/../../../../src/utils/auth/StravaUtils.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeSignUpWithPasswordEndpointAuthRequestRepository {
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
 * @covers \SignUpWithPasswordEndpoint
 */
final class SignUpWithPasswordEndpointTest extends UnitTestCase {
    const VALID_INPUT = [
        'firstName' => 'fakeFirstName',
        'lastName' => 'fakeLastName',
        'username' => 'fakeUsername',
        'password' => 'securePassword',
        'email' => 'fakeEmail',
        'phone' => '+41441234567',
        'street' => 'fakeStreet',
        'postalCode' => 'fakePostalCode',
        'city' => 'fakeCity',
        'region' => 'fakeRegion',
        'countryCode' => 'fakeCountryCode',
    ];

    public function testSignUpWithPasswordEndpointIdent(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $this->assertSame('SignUpWithPasswordEndpoint', $endpoint->getIdent());
    }

    public function testSignUpWithPasswordEndpointWithoutInput(): void {
        $entity_manager = new FakeEntityManager();
        $logger = new Logger('SignUpWithPasswordEndpointTest');
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'firstName' => ['Feld darf nicht leer sein.'],
                'lastName' => ['Feld darf nicht leer sein.'],
                'username' => ['Feld darf nicht leer sein.'],
                'password' => ['Feld darf nicht leer sein.'],
                'email' => ['Feld darf nicht leer sein.'],
                'street' => ['Feld darf nicht leer sein.'],
                'postalCode' => ['Feld darf nicht leer sein.'],
                'city' => ['Feld darf nicht leer sein.'],
                'region' => ['Feld darf nicht leer sein.'],
                'countryCode' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithInvalidUsername(): void {
        $entity_manager = new FakeEntityManager();
        $logger = new Logger('SignUpWithPasswordEndpointTest');
        $auth_utils = new FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call(array_merge(self::VALID_INPUT, ['username' => 'invalid@']));
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'username' => ['Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithShortPassword(): void {
        $entity_manager = new FakeEntityManager();
        $logger = new Logger('SignUpWithPasswordEndpointTest');
        $auth_utils = new FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call(array_merge(self::VALID_INPUT, ['password' => 'short']));
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'password' => ['Das Passwort muss mindestens 8 Zeichen lang sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithValidDataForNewUser(): void {
        $entity_manager = new FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories['AuthRequest'] = $auth_request_repo;
        $logger = new Logger('SignUpWithPasswordEndpointTest');
        $auth_utils = new FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
    }

    public function testSignUpWithPasswordEndpointWithValidDataForExistingUserWithoutPassword(): void {
        $entity_manager = new FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories['AuthRequest'] = $auth_request_repo;
        $user_repo = new FakeUserRepository();
        $existing_user = new User();
        $existing_user->setId(123);
        $user_repo->userToBeFound = $existing_user;
        $entity_manager->repositories['User'] = $user_repo;
        $logger = new Logger('SignUpWithPasswordEndpointTest');
        $auth_utils = new FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => 123,
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
    }

    public function testSignUpWithPasswordEndpointWithValidDataForExistingUserWithPassword(): void {
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $existing_user = new User();
        $existing_user->setId(123);
        $existing_user->setPasswordHash('some-hash');
        $user_repo->userToBeFound = $existing_user;
        $entity_manager->repositories['User'] = $user_repo;
        $logger = new Logger('SignUpWithPasswordEndpointTest');
        $auth_utils = new FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        try {
            $result = $endpoint->call(self::VALID_INPUT);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(
                [
                    'message' => 'Fehlerhafte Eingabe.',
                    'error' => [
                        'type' => 'ValidationError',
                        'validationErrors' => [
                            'username' => ['Es existiert bereits eine Person mit diesem Benutzernamen.'],
                        ],
                    ],
                ],
                $httperr->getStructuredAnswer(),
            );
        }
    }
}
