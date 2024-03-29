<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\SignUpWithPasswordEndpoint;
use Olz\Entity\AuthRequest;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\SignUpWithPasswordEndpoint
 */
final class SignUpWithPasswordEndpointTest extends UnitTestCase {
    public const MINIMAL_INPUT = [
        'firstName' => 'fakeFirstName',
        'lastName' => 'fakeLastName',
        'username' => 'fakeUsername',
        'password' => 'securePassword',
        'email' => 'fakeEmail',
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
        'recaptchaToken' => 'fake-recaptcha-token',
    ];
    public const MAXIMAL_INPUT = [
        'firstName' => 'fakeFirstName',
        'lastName' => 'fakeLastName',
        'username' => 'fakeUsername',
        'password' => 'securePassword',
        'email' => 'fakeEmail',
        'phone' => '+41441234567',
        'gender' => 'M',
        'birthdate' => '2020-03-13',
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
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
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
            ], $this->getLogs());
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
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'recaptchaToken' => 'invalid-token',
        ]);

        $this->assertSame([
            'status' => 'DENIED',
        ], $result);
        $this->assertSame([], $session->session_storage);
    }

    public function testSignUpWithPasswordEndpointWithInvalidUsername(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $result = $endpoint->call([
                ...self::MINIMAL_INPUT,
                'username' => 'invalid@',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (invalid@)",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'username' => ['Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithShortPassword(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $result = $endpoint->call([
                ...self::MINIMAL_INPUT,
                'password' => 'short',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'password' => ['Das Passwort muss mindestens 8 Zeichen lang sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithOlzimmerbergEmail(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $result = $endpoint->call([
                ...self::MINIMAL_INPUT,
                'email' => 'bot@olzimmerberg.ch',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Bitte keine @olzimmerberg.ch E-Mail verwenden.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithoutEmail(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $result = $endpoint->call([
                ...self::MINIMAL_INPUT,
                'email' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithoutPassword(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $result = $endpoint->call([
                ...self::MINIMAL_INPUT,
                'password' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'password' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithMinimalDataForNewUser(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MINIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $this->getLogs());
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
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame(1, count($entity_manager->persisted));
        $user = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertSame(null, $user->getOldUsername());
        $this->assertSame('fakeEmail', $user->getEmail());
        $this->assertSame(false, $user->isEmailVerified());
        $this->assertSame(null, $user->getEmailVerificationToken());
        $this->assertSame(false, $user->hasPermission('verified_email'));
        $this->assertSame('fakeFirstName', $user->getFirstName());
        $this->assertSame('fakeLastName', $user->getLastName());
        $this->assertSame(null, $user->getPhone());
        $this->assertSame(null, $user->getGender());
        $this->assertSame(null, $user->getBirthdate());
        $this->assertSame(null, $user->getStreet());
        $this->assertSame(null, $user->getPostalCode());
        $this->assertSame(null, $user->getCity());
        $this->assertSame(null, $user->getRegion());
        $this->assertSame(null, $user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testSignUpWithPasswordEndpointWithMaximalDataForNewUser(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MAXIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $this->getLogs());
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
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame(1, count($entity_manager->persisted));
        $user = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertSame(null, $user->getOldUsername());
        $this->assertSame('fakeEmail', $user->getEmail());
        $this->assertSame(false, $user->isEmailVerified());
        $this->assertSame(null, $user->getEmailVerificationToken());
        $this->assertSame(false, $user->hasPermission('verified_email'));
        $this->assertSame('fakeFirstName', $user->getFirstName());
        $this->assertSame('fakeLastName', $user->getLastName());
        $this->assertSame('+41441234567', $user->getPhone());
        $this->assertSame('M', $user->getGender());
        $this->assertSame('2020-03-13 12:00:00', $user->getBirthdate()->format('Y-m-d H:i:s'));
        $this->assertSame('fakeStreet', $user->getStreet());
        $this->assertSame('fakePostalCode', $user->getPostalCode());
        $this->assertSame('fakeCity', $user->getCity());
        $this->assertSame('fakeRegion', $user->getRegion());
        $this->assertSame('CC', $user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testSignUpWithPasswordEndpointWithMinimalDataForNewFamilyUser(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'email' => null,
            'password' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame(1, count($entity_manager->persisted));
        $user = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertSame(null, $user->getOldUsername());
        $this->assertSame(null, $user->getEmail());
        $this->assertSame(false, $user->isEmailVerified());
        $this->assertSame(null, $user->getEmailVerificationToken());
        $this->assertSame(false, $user->hasPermission('verified_email'));
        $this->assertSame('fakeFirstName', $user->getFirstName());
        $this->assertSame('fakeLastName', $user->getLastName());
        $this->assertSame(null, $user->getPhone());
        $this->assertSame(null, $user->getGender());
        $this->assertSame(null, $user->getBirthdate());
        $this->assertSame(null, $user->getStreet());
        $this->assertSame(null, $user->getPostalCode());
        $this->assertSame(null, $user->getCity());
        $this->assertSame(null, $user->getRegion());
        $this->assertSame(null, $user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testSignUpWithPasswordEndpointWithMaximalDataForNewFamilyUser(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MAXIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertSame(1, count($entity_manager->persisted));
        $user = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertSame(null, $user->getOldUsername());
        $this->assertSame('fakeEmail', $user->getEmail());
        $this->assertSame(false, $user->isEmailVerified());
        $this->assertSame(null, $user->getEmailVerificationToken());
        $this->assertSame(false, $user->hasPermission('verified_email'));
        $this->assertSame('fakeFirstName', $user->getFirstName());
        $this->assertSame('fakeLastName', $user->getLastName());
        $this->assertSame('+41441234567', $user->getPhone());
        $this->assertSame('M', $user->getGender());
        $this->assertSame('2020-03-13 12:00:00', $user->getBirthdate()->format('Y-m-d H:i:s'));
        $this->assertSame('fakeStreet', $user->getStreet());
        $this->assertSame('fakePostalCode', $user->getPostalCode());
        $this->assertSame('fakeCity', $user->getCity());
        $this->assertSame('fakeRegion', $user->getRegion());
        $this->assertSame('CC', $user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testSignUpWithPasswordEndpointWithValidDataForExistingUsernameWithoutPassword(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $existing_user = new User();
        $existing_user->setId(123);
        $entity_manager->repositories[User::class]->userToBeFound = $existing_user;
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MINIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $this->getLogs());
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
        $entity_manager = WithUtilsCache::get('entityManager');
        $existing_user = new User();
        $existing_user->setId(123);
        $existing_user->setPasswordHash('some-hash');
        $entity_manager->repositories[User::class]->userToBeFound = $existing_user;
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $endpoint->call(self::MINIMAL_INPUT);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $this->getLogs());
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
        $entity_manager = WithUtilsCache::get('entityManager');
        $existing_user = new User();
        $existing_user->setId(123);
        $entity_manager->repositories[User::class]->userToBeFoundForQuery =
            function ($where) use ($existing_user) {
                if ($where === ['email' => 'fakeEmail']) {
                    return $existing_user;
                }
                return null;
            };
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MINIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "INFO Valid user response",
        ], $this->getLogs());
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
        $entity_manager = WithUtilsCache::get('entityManager');
        $existing_user = new User();
        $existing_user->setId(123);
        $existing_user->setPasswordHash('some-hash');
        $entity_manager->repositories[User::class]->userToBeFoundForQuery =
            function ($where) use ($existing_user) {
                if ($where === ['email' => 'fakeEmail']) {
                    return $existing_user;
                }
                return null;
            };
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $result = $endpoint->call(self::MINIMAL_INPUT);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
                "WARNING Bad user request",
            ], $this->getLogs());
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
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('emailUtils')->send_email_verification_email_error = new \Exception('test');
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MINIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername)",
            "ERROR Error sending fake verification email",
            "INFO Valid user response",
        ], $this->getLogs());
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
        $this->assertSame(1, count($entity_manager->persisted));
        $user = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertSame(null, $user->getOldUsername());
        $this->assertSame('fakeEmail', $user->getEmail());
        $this->assertSame(false, $user->isEmailVerified());
        $this->assertSame(null, $user->getEmailVerificationToken());
        $this->assertSame(false, $user->hasPermission('verified_email'));
        $this->assertSame('fakeFirstName', $user->getFirstName());
        $this->assertSame('fakeLastName', $user->getLastName());
        $this->assertSame(null, $user->getPhone());
        $this->assertSame(null, $user->getGender());
        $this->assertSame(null, $user->getBirthdate());
        $this->assertSame(null, $user->getStreet());
        $this->assertSame(null, $user->getPostalCode());
        $this->assertSame(null, $user->getCity());
        $this->assertSame(null, $user->getRegion());
        $this->assertSame(null, $user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
