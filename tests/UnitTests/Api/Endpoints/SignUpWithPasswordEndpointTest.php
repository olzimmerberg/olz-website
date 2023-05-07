<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\SignUpWithPasswordEndpoint;
use Olz\Entity\AuthRequest;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use PhpTypeScriptApi\HttpError;

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
 *
 * @covers \Olz\Api\Endpoints\SignUpWithPasswordEndpoint
 */
final class SignUpWithPasswordEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'firstName' => 'fakeFirstName',
        'lastName' => 'fakeLastName',
        'username' => 'fakeUsername',
        'password' => 'securePassword',
        'email' => 'fakeEmail',
        'phone' => '+41441234567',
        'gender' => null,
        'birthdate' => null,
        'street' => 'fakeStreet',
        'postalCode' => 'fakePostalCode',
        'city' => 'fakeCity',
        'region' => 'fakeRegion',
        'countryCode' => 'CC',
        'siCardNumber' => 1234567,
        'solvNumber' => 'JACK7NORRIS',
        'recaptchaToken' => 'fake-recaptcha-token',
    ];

    public function testSignUpWithPasswordEndpointIdent(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $this->assertSame('SignUpWithPasswordEndpoint', $endpoint->getIdent());
    }

    public function testSignUpWithPasswordEndpointWithoutInput(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);
        try {
            $result = $endpoint->call([
                'firstName' => null,
                'lastName' => null,
                'username' => null,
                'password' => null,
                'email' => null,
                'phone' => null,
                'gender' => null,
                'birthdate' => null,
                'street' => null,
                'postalCode' => null,
                'city' => null,
                'region' => null,
                'countryCode' => null,
                'siCardNumber' => null,
                'solvNumber' => null,
                'recaptchaToken' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'firstName' => [['.' => ['Feld darf nicht leer sein.']]],
                'lastName' => [['.' => ['Feld darf nicht leer sein.']]],
                'username' => [['.' => ['Feld darf nicht leer sein.']]],
                'recaptchaToken' => [['.' => ['Feld darf nicht leer sein.']]],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithInvalidRecaptchaToken(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            ...self::VALID_INPUT,
            'recaptchaToken' => 'invalid-token',
        ]);

        $this->assertSame([
            'status' => 'DENIED',
        ], $result);
        $this->assertSame([], $session->session_storage);
    }

    public function testSignUpWithPasswordEndpointWithInvalidUsername(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                ...self::VALID_INPUT,
                'username' => 'invalid@',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (invalid@)",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'username' => ['Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithShortPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                ...self::VALID_INPUT,
                'password' => 'short',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'password' => ['Das Passwort muss mindestens 8 Zeichen lang sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithOlzimmerbergEmail(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                ...self::VALID_INPUT,
                'email' => 'bot@olzimmerberg.ch',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Bitte keine @olzimmerberg.ch E-Mail verwenden.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithoutEmail(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils(new Fake\FakeEmailUtils());
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                ...self::VALID_INPUT,
                'email' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithoutPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils(new Fake\FakeEmailUtils());
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                ...self::VALID_INPUT,
                'password' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'password' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithValidDataForNewUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils(new Fake\FakeEmailUtils());
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
            'auth_user' => 'fakeUsername',
            'auth_user_id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        // TODO: Check created user!
    }

    public function testSignUpWithPasswordEndpointWithValidDataForNewFamilyUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->current_user = Fake\FakeUsers::adminUser();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils(new Fake\FakeEmailUtils());
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            ...self::VALID_INPUT,
            'password' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        // TODO: Check created user!
    }

    public function testSignUpWithPasswordEndpointWithValidDataForExistingUsernameWithoutPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $user_repo = new Fake\FakeUserRepository();
        $existing_user = new User();
        $existing_user->setId(123);
        $user_repo->userToBeFound = $existing_user;
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils(new Fake\FakeEmailUtils());
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => 123,
            'auth_user' => 'fakeUsername',
            'auth_user_id' => 123,
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
    }

    public function testSignUpWithPasswordEndpointWithValidDataForExistingUsernameWithPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $user_repo = new Fake\FakeUserRepository();
        $existing_user = new User();
        $existing_user->setId(123);
        $existing_user->setPasswordHash('some-hash');
        $user_repo->userToBeFound = $existing_user;
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils(new Fake\FakeEmailUtils());
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call(self::VALID_INPUT);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame(
                [
                    'message' => 'Fehlerhafte Eingabe',
                    'error' => [
                        'type' => 'ValidationError',
                        'validationErrors' => [
                            'username' => ['Es existiert bereits eine Person mit diesem Benutzernamen. Wolltest du gar kein Konto erstellen, sondern dich nur einloggen?'],
                        ],
                    ],
                ],
                $httperr->getStructuredAnswer(),
            );
        }
    }

    public function testSignUpWithPasswordEndpointWithValidDataForExistingEmailWithoutPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $user_repo = new Fake\FakeUserRepository();
        $existing_user = new User();
        $existing_user->setId(123);
        $user_repo->userToBeFoundForQuery = function ($where) use ($existing_user) {
            if ($where === ['email' => 'fakeEmail']) {
                return $existing_user;
            }
            return null;
        };
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils(new Fake\FakeEmailUtils());
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => 123,
            'auth_user' => 'fakeUsername',
            'auth_user_id' => 123,
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
    }

    public function testSignUpWithPasswordEndpointWithValidDataForExistingEmailWithPassword(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $user_repo = new Fake\FakeUserRepository();
        $existing_user = new User();
        $existing_user->setId(123);
        $existing_user->setPasswordHash('some-hash');
        $user_repo->userToBeFoundForQuery = function ($where) use ($existing_user) {
            if ($where === ['email' => 'fakeEmail']) {
                return $existing_user;
            }
            return null;
        };
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils(new Fake\FakeEmailUtils());
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call(self::VALID_INPUT);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame(
                [
                    'message' => 'Fehlerhafte Eingabe',
                    'error' => [
                        'type' => 'ValidationError',
                        'validationErrors' => [
                            'email' => ['Es existiert bereits eine Person mit dieser E-Mail Adresse. Wolltest du gar kein Konto erstellen, sondern dich nur einloggen?'],
                        ],
                    ],
                ],
                $httperr->getStructuredAnswer(),
            );
        }
    }

    public function testSignUpWithPasswordEndpointErrorSending(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_request_repo = new FakeSignUpWithPasswordEndpointAuthRequestRepository();
        $entity_manager->repositories[AuthRequest::class] = $auth_request_repo;
        $logger = Fake\FakeLogger::create();
        $auth_utils = new Fake\FakeAuthUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $email_utils->send_email_verification_email_error = new \Exception('test');
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLog($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "ERROR Error sending fake verification email",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            'status' => 'OK_NO_EMAIL_VERIFICATION',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
            'auth_user' => 'fakeUsername',
            'auth_user_id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        // TODO: Check created user!
    }
}
