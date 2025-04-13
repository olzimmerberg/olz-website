<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Users\Endpoints;

use Olz\Entity\AuthRequest;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Users\Endpoints\CreateUserEndpoint;
use Olz\Utils\MemorySession;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Users\Endpoints\CreateUserEndpoint
 */
final class CreateUserEndpointTest extends UnitTestCase {
    public const MINIMAL_INPUT = [
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'parentUserId' => null,
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
            'avatarImageId' => null,
        ],
        'custom' => [
            'captchaToken' => 'valid-token',
        ],
    ];

    public const MAXIMAL_INPUT = [
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'parentUserId' => 1,
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
            'avatarImageId' => 'fake-avatar-id.jpg',
        ],
        'custom' => [
            'captchaToken' => 'valid-token',
        ],
    ];

    public function testCreateUserEndpointWithoutInput(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
                'meta' => [
                    'ownerUserId' => null,
                    'ownerRoleId' => null,
                    'onOff' => true,
                ],
                'data' => [
                    'parentUserId' => null,
                    'firstName' => null,
                    'lastName' => null,
                    'username' => null,
                    'email' => null,
                    'password' => null,
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
                    'avatarImageId' => null,
                ],
                'custom' => [
                    'captchaToken' => null,
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'data' => [[
                    'firstName' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                    'lastName' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                    'username' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                ]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testCreateUserEndpointWithInvalidcaptchaToken(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'custom' => ['captchaToken' => 'invalid-token'],
        ]);

        $this->assertEquals([
            'custom' => ['status' => 'DENIED'],
            'id' => null,
        ], $result);
        $this->assertSame([], $session->session_storage);
    }

    public function testCreateUserEndpointWithInvalidUsername(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'username' => 'inv@lid',
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (inv@lid@) <fakeEmail> (Parent: )",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'username' => ['Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten.'],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testCreateUserEndpointWithShortPassword(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'password' => 'short',
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <fakeEmail> (Parent: )",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'password' => ['Das Passwort muss mindestens 8 Zeichen lang sein.'],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testCreateUserEndpointWithOlzimmerbergEmail(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'email' => 'fake-user@olzimmerberg.ch',
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <fake-user@olzimmerberg.ch> (Parent: )",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Bitte keine @olzimmerberg.ch E-Mail verwenden.'],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testCreateUserEndpointWithoutEmail(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'email' => null,
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <> (Parent: )",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Feld darf nicht leer sein.'],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testCreateUserEndpointWithoutPassword(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'password' => null,
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <fakeEmail> (Parent: )",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'password' => ['Feld darf nicht leer sein.'],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testCreateUserEndpointWithMinimalDataForNewUser(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MINIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <fakeEmail> (Parent: )",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => strval(FakeEntityManager::AUTO_INCREMENT_ID),
            'auth_user' => 'fakeUsername',
            'auth_user_id' => strval(FakeEntityManager::AUTO_INCREMENT_ID),
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
        $this->assertCount(1, $entity_manager->persisted);
        $user = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertNull($user->getOldUsername());
        $this->assertSame('fakeEmail', $user->getEmail());
        $this->assertFalse($user->isEmailVerified());
        $this->assertNull($user->getEmailVerificationToken());
        $this->assertFalse($user->hasPermission('verified_email'));
        $this->assertSame('fakeFirstName', $user->getFirstName());
        $this->assertSame('fakeLastName', $user->getLastName());
        $this->assertNull($user->getPhone());
        $this->assertNull($user->getGender());
        $this->assertNull($user->getBirthdate());
        $this->assertNull($user->getStreet());
        $this->assertNull($user->getPostalCode());
        $this->assertNull($user->getCity());
        $this->assertNull($user->getRegion());
        $this->assertNull($user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testCreateUserEndpointWithMaximalDataForNewUser(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MAXIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <fakeEmail> (Parent: 1)",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame([], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertCount(1, $entity_manager->persisted);
        $user = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertNull($user->getOldUsername());
        $this->assertSame('fakeEmail', $user->getEmail());
        $this->assertFalse($user->isEmailVerified());
        $this->assertNull($user->getEmailVerificationToken());
        $this->assertFalse($user->hasPermission('verified_email'));
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

    public function testCreateUserEndpointWithMinimalDataForNewFamilyUser(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'data' => [
                ...self::MINIMAL_INPUT['data'],
                'parentUserId' => FakeUser::adminUser()->getId(),
                'email' => null,
                'password' => null,
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <> (Parent: 2)",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame([], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertCount(1, $entity_manager->persisted);
        $user = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertNull($user->getOldUsername());
        $this->assertNull($user->getEmail());
        $this->assertFalse($user->isEmailVerified());
        $this->assertNull($user->getEmailVerificationToken());
        $this->assertFalse($user->hasPermission('verified_email'));
        $this->assertSame('fakeFirstName', $user->getFirstName());
        $this->assertSame('fakeLastName', $user->getLastName());
        $this->assertNull($user->getPhone());
        $this->assertNull($user->getGender());
        $this->assertNull($user->getBirthdate());
        $this->assertNull($user->getStreet());
        $this->assertNull($user->getPostalCode());
        $this->assertNull($user->getCity());
        $this->assertNull($user->getRegion());
        $this->assertNull($user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testCreateUserEndpointWithMaximalDataForNewFamilyUser(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            ...self::MAXIMAL_INPUT,
            'data' => [
                ...self::MAXIMAL_INPUT['data'],
                'parentUserId' => FakeUser::adminUser()->getId(),
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <fakeEmail> (Parent: 2)",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame([], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
        $this->assertCount(1, $entity_manager->persisted);
        $user = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertNull($user->getOldUsername());
        $this->assertSame('fakeEmail', $user->getEmail());
        $this->assertFalse($user->isEmailVerified());
        $this->assertNull($user->getEmailVerificationToken());
        $this->assertFalse($user->hasPermission('verified_email'));
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

    public function testCreateUserEndpointWithValidDataForExistingUsernameWithoutPassword(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'data' => [
                ...self::MINIMAL_INPUT['data'],
                'username' => 'child1', // has no password yet
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (child1@) <fakeEmail> (Parent: )",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => 5,
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'child1',
            'user_id' => '5',
            'auth_user' => 'child1',
            'auth_user_id' => '5',
        ], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'child1',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
    }

    public function testCreateUserEndpointWithValidDataForExistingUsernameWithPassword(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'username' => 'admin', // has a password
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (admin@) <fakeEmail> (Parent: )",
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

    public function testCreateUserEndpointWithValidDataForExistingEmailWithoutPassword(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'data' => [
                ...self::MINIMAL_INPUT['data'],
                'username' => 'inexistent',
                'email' => 'child1@gmail.com',
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (inexistent@) <child1@gmail.com> (Parent: )",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => 5,
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'inexistent',
            'user_id' => '5',
            'auth_user' => 'inexistent',
            'auth_user_id' => '5',
        ], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'inexistent',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
    }

    public function testCreateUserEndpointWithValidDataForExistingEmailWithPassword(): void {
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'username' => 'inexistent',
                    'email' => 'admin@gmail.com',
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "INFO New sign-up (using password): fakeFirstName fakeLastName (inexistent@) <admin@gmail.com> (Parent: )",
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

    public function testCreateUserEndpointErrorSending(): void {
        WithUtilsCache::get('emailUtils')->send_email_verification_email_error = new \Exception('test');
        $endpoint = new CreateUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(self::MINIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO New sign-up (using password): fakeFirstName fakeLastName (fakeUsername@) <fakeEmail> (Parent: )",
            "ERROR Error sending fake verification email",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals([
            'custom' => ['status' => 'OK_NO_EMAIL_VERIFICATION'],
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => strval(FakeEntityManager::AUTO_INCREMENT_ID),
            'auth_user' => 'fakeUsername',
            'auth_user_id' => strval(FakeEntityManager::AUTO_INCREMENT_ID),
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
        $this->assertCount(1, $entity_manager->persisted);
        $user = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $user->getId());
        $this->assertSame('fakeUsername', $user->getUsername());
        $this->assertNull($user->getOldUsername());
        $this->assertSame('fakeEmail', $user->getEmail());
        $this->assertFalse($user->isEmailVerified());
        $this->assertNull($user->getEmailVerificationToken());
        $this->assertFalse($user->hasPermission('verified_email'));
        $this->assertSame('fakeFirstName', $user->getFirstName());
        $this->assertSame('fakeLastName', $user->getLastName());
        $this->assertNull($user->getPhone());
        $this->assertNull($user->getGender());
        $this->assertNull($user->getBirthdate());
        $this->assertNull($user->getStreet());
        $this->assertNull($user->getPostalCode());
        $this->assertNull($user->getCity());
        $this->assertNull($user->getRegion());
        $this->assertNull($user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
