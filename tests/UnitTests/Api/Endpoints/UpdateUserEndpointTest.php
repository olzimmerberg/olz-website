<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\UpdateUserEndpoint;
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
 * @coversNothing
 */
class UpdateUserEndpointForTest extends UpdateUserEndpoint {
    public $unlink_calls = [];
    public $rename_calls = [];

    protected function unlink($path) {
        $this->unlink_calls[] = $path;
    }

    protected function rename($source_path, $destination_path) {
        $this->rename_calls[] = [$source_path, $destination_path];
    }
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\UpdateUserEndpoint
 */
final class UpdateUserEndpointTest extends UnitTestCase {
    public const MINIMAL_INPUT = [
        'id' => 2,
        'firstName' => 'First',
        'lastName' => 'Last',
        'username' => 'test',
        'email' => 'bot@staging.olzimmerberg.ch',
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
        'avatarId' => null,
        'recaptchaToken' => null,
    ];
    public const MAXIMAL_INPUT = [
        'id' => 2,
        'firstName' => 'First',
        'lastName' => 'Last',
        'username' => 'test',
        'email' => 'bot@staging.olzimmerberg.ch',
        'phone' => '+41441234567',
        'gender' => 'F',
        'birthdate' => '1992-08-05',
        'street' => 'Teststrasse 123',
        'postalCode' => '1234',
        'city' => 'Muster',
        'region' => 'ZH',
        'countryCode' => 'CH',
        'siCardNumber' => 1234567,
        'solvNumber' => 'JACK7NORRIS',
        'avatarId' => 'fake-avatar-id.jpg',
        'recaptchaToken' => 'valid-recaptcha-token',
    ];

    public function testUpdateUserEndpointIdent(): void {
        $endpoint = new UpdateUserEndpointForTest();
        $this->assertSame('UpdateUserEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUserEndpointWrongUsername(): void {
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call(self::MINIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ], $session->session_storage);
        $this->assertSame([], $endpoint->unlink_calls);
        $this->assertSame([], $endpoint->rename_calls);
    }

    public function testUpdateUserEndpointInvalidNewUsername(): void {
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        try {
            $endpoint->call(array_merge(
                self::MINIMAL_INPUT,
                ['username' => 'invalid@']
            ));
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'username' => ['Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten.'],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                'auth' => 'ftp',
                'root' => 'karten',
                'user' => 'admin',
            ], $session->session_storage);
            $this->assertSame([], $endpoint->unlink_calls);
            $this->assertSame([], $endpoint->rename_calls);
        }
    }

    public function testUpdateUserEndpointWithNewOlzimmerbergEmail(): void {
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        try {
            $endpoint->call(array_merge(
                self::MINIMAL_INPUT,
                ['email' => 'bot@olzimmerberg.ch']
            ));
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Bitte keine @olzimmerberg.ch E-Mail verwenden.'],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                'auth' => 'ftp',
                'root' => 'karten',
                'user' => 'admin',
            ], $session->session_storage);
            $this->assertSame([], $endpoint->unlink_calls);
            $this->assertSame([], $endpoint->rename_calls);
        }
    }

    public function testUpdateUserEndpointMinimal(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'recaptchaToken' => 'valid-recaptcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('admin', $admin_user->getOldUsername());
        $this->assertSame('bot@staging.olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame(null, $admin_user->getEmailVerificationToken());
        $this->assertSame(false, $admin_user->hasPermission('verified_email'));
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame(null, $admin_user->getPhone());
        $this->assertSame(null, $admin_user->getGender());
        $this->assertSame(null, $admin_user->getBirthdate());
        $this->assertSame(null, $admin_user->getStreet());
        $this->assertSame(null, $admin_user->getPostalCode());
        $this->assertSame(null, $admin_user->getCity());
        $this->assertSame(null, $admin_user->getRegion());
        $this->assertSame(null, $admin_user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $admin_user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'test',
        ], $session->session_storage);
        $this->assertSame([], $endpoint->unlink_calls);
        $this->assertSame([], $endpoint->rename_calls);
    }

    public function testUpdateUserEndpointMaximal(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call(self::MAXIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('admin', $admin_user->getOldUsername());
        $this->assertSame('bot@staging.olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame(null, $admin_user->getEmailVerificationToken());
        $this->assertSame(false, $admin_user->hasPermission('verified_email'));
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame('+41441234567', $admin_user->getPhone());
        $this->assertSame('F', $admin_user->getGender());
        $this->assertSame('1992-08-05 12:00:00', $admin_user->getBirthdate()->format('Y-m-d H:i:s'));
        $this->assertSame('Teststrasse 123', $admin_user->getStreet());
        $this->assertSame('1234', $admin_user->getPostalCode());
        $this->assertSame('Muster', $admin_user->getCity());
        $this->assertSame('ZH', $admin_user->getRegion());
        $this->assertSame('CH', $admin_user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $admin_user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'test',
        ], $session->session_storage);
        $this->assertSame([], $endpoint->unlink_calls);
        $this->assertSame([
            [
                'fake-data-path/temp/fake-avatar-id.jpg',
                'fake-data-path/img/users/2.jpg',
            ],
        ], $endpoint->rename_calls);
    }

    public function testUpdateUserEndpointSameEmail(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'email' => 'admin-user@staging.olzimmerberg.ch',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('admin', $admin_user->getOldUsername());
        $this->assertSame('admin-user@staging.olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame('admintoken', $admin_user->getEmailVerificationToken());
        $this->assertSame(true, $admin_user->hasPermission('verified_email'));
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame(null, $admin_user->getPhone());
        $this->assertSame(null, $admin_user->getGender());
        $this->assertSame(null, $admin_user->getBirthdate());
        $this->assertSame(null, $admin_user->getStreet());
        $this->assertSame(null, $admin_user->getPostalCode());
        $this->assertSame(null, $admin_user->getCity());
        $this->assertSame(null, $admin_user->getRegion());
        $this->assertSame(null, $admin_user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $admin_user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'test',
        ], $session->session_storage);
        $this->assertSame([], $endpoint->unlink_calls);
        $this->assertSame([], $endpoint->rename_calls);
    }

    public function testUpdateUserEndpointEmailUpdateWithoutRecaptcha(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        try {
            $endpoint->call(self::MINIMAL_INPUT);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'recaptchaToken' => ['Bei einer E-Mail-Änderung muss ein ReCaptcha Token angegeben werden.'],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                'auth' => 'ftp',
                'root' => 'karten',
                'user' => 'admin',
            ], $session->session_storage);
            $this->assertSame([], $endpoint->unlink_calls);
            $this->assertSame([], $endpoint->rename_calls);
        }
    }

    public function testUpdateUserEndpointWithInvalidRecaptcha(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'recaptchaToken' => 'invalid-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'DENIED'], $result);
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ], $session->session_storage);
        $this->assertSame([], $endpoint->unlink_calls);
        $this->assertSame([], $endpoint->rename_calls);
    }

    public function testUpdateUserEndpointWithExistingUsername(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $existing_user = new User();
        $existing_user->setId(123);
        $entity_manager->repositories[User::class]->userToBeFoundForQuery =
            function ($where) use ($existing_user) {
                if ($where === ['id' => 2]) {
                    return FakeUser::adminUser();
                }
                if ($where === ['username' => 'test']) {
                    return $existing_user;
                }
                return null;
            };
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'recaptchaToken' => 'valid-recaptcha-token',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'username' => ['Dieser Benutzername ist bereits vergeben.'],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                'auth' => 'ftp',
                'root' => 'karten',
                'user' => 'admin',
            ], $session->session_storage);
            $this->assertSame([], $endpoint->unlink_calls);
            $this->assertSame([], $endpoint->rename_calls);
        }
    }

    public function testUpdateUserEndpointWithExistingEmail(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $existing_user = new User();
        $existing_user->setId(123);
        $entity_manager->repositories[User::class]->userToBeFoundForQuery =
            function ($where) use ($existing_user) {
                if ($where === ['id' => 2]) {
                    return FakeUser::adminUser();
                }
                if ($where === ['email' => 'bot@staging.olzimmerberg.ch']) {
                    return $existing_user;
                }
                return null;
            };
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'recaptchaToken' => 'valid-recaptcha-token',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Es existiert bereits eine Person mit dieser E-Mail Adresse.'],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                'auth' => 'ftp',
                'root' => 'karten',
                'user' => 'admin',
            ], $session->session_storage);
            $this->assertSame([], $endpoint->unlink_calls);
            $this->assertSame([], $endpoint->rename_calls);
        }
    }

    public function testUpdateUserEndpointRemoveAvatar(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'avatarId' => '-',
            'recaptchaToken' => 'valid-recaptcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('admin', $admin_user->getOldUsername());
        $this->assertSame('bot@staging.olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame(null, $admin_user->getEmailVerificationToken());
        $this->assertSame(false, $admin_user->hasPermission('verified_email'));
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame(null, $admin_user->getPhone());
        $this->assertSame(null, $admin_user->getGender());
        $this->assertSame(null, $admin_user->getBirthdate());
        $this->assertSame(null, $admin_user->getStreet());
        $this->assertSame(null, $admin_user->getPostalCode());
        $this->assertSame(null, $admin_user->getCity());
        $this->assertSame(null, $admin_user->getRegion());
        $this->assertSame(null, $admin_user->getCountryCode());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $admin_user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'test',
        ], $session->session_storage);
        $this->assertSame([
            'fake-data-path/img/users/2.jpg',
        ], $endpoint->unlink_calls);
        $this->assertSame([], $endpoint->rename_calls);
    }
}
