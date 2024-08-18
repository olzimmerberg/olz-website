<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Users\Endpoints;

use Olz\Entity\User;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\FakeRecaptchaUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Users\Endpoints\UpdateUserEndpoint;
use Olz\Utils\MemorySession;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @coversNothing
 */
class UpdateUserEndpointForTest extends UpdateUserEndpoint {
    /** @var array<string> */
    public array $unlink_calls = [];
    /** @var array<array{0: string, 1: string}> */
    public array $rename_calls = [];

    protected function unlink(string $path): void {
        $this->unlink_calls[] = $path;
    }

    protected function rename(string $source_path, string $destination_path): void {
        $this->rename_calls[] = [$source_path, $destination_path];
    }
}

/**
 * @internal
 *
 * @covers \Olz\Users\Endpoints\UpdateUserEndpoint
 */
final class UpdateUserEndpointTest extends UnitTestCase {
    public const MINIMAL_INPUT = [
        'id' => 2,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'firstName' => 'First',
            'lastName' => 'Last',
            'username' => 'test',
            'email' => 'bot@staging.olzimmerberg.ch',
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
            'avatarId' => null,
        ],
    ];
    public const MAXIMAL_INPUT = [
        'id' => 2,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'firstName' => 'First',
            'lastName' => 'Last',
            'username' => 'test',
            'email' => 'bot@staging.olzimmerberg.ch',
            'password' => null,
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
        ],
    ];

    public function testUpdateUserEndpointIdent(): void {
        $endpoint = new UpdateUserEndpointForTest();
        $this->assertSame('UpdateUserEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUserEndpointWrongUsername(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ];
        $endpoint->setSession($session);

        try {
            $endpoint->call(self::MINIMAL_INPUT);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                'auth' => 'ftp',
                'root' => 'karten',
                'user' => 'wrong_user',
            ], $session->session_storage);
            $this->assertSame([], $endpoint->unlink_calls);
            $this->assertSame([], $endpoint->rename_calls);
        }
    }

    public function testUpdateUserEndpointInvalidNewUsername(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
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
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'username' => 'invalid@',
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'username' => ['Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten.'],
                // @phpstan-ignore-next-line
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
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
            $endpoint->call([
                ...self::MINIMAL_INPUT,
                'data' => [
                    ...self::MINIMAL_INPUT['data'],
                    'email' => 'bot@olzimmerberg.ch',
                ],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Bitte keine @olzimmerberg.ch E-Mail verwenden.'],
                // @phpstan-ignore-next-line
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
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
        $endpoint->setRecaptchaUtils(new FakeRecaptchaUtils());

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK', 'id' => 2], $result);
        $admin_user = FakeUser::adminUser();
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('admin', $admin_user->getOldUsername());
        $this->assertSame('bot@staging.olzimmerberg.ch', $admin_user->getEmail());
        $this->assertNull($admin_user->getEmailVerificationToken());
        $this->assertFalse($admin_user->hasPermission('verified_email'));
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertNull($admin_user->getPhone());
        $this->assertNull($admin_user->getGender());
        $this->assertNull($admin_user->getBirthdate());
        $this->assertNull($admin_user->getStreet());
        $this->assertNull($admin_user->getPostalCode());
        $this->assertNull($admin_user->getCity());
        $this->assertNull($admin_user->getRegion());
        $this->assertNull($admin_user->getCountryCode());
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
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
        $endpoint->setRecaptchaUtils(new FakeRecaptchaUtils());

        $result = $endpoint->call(self::MAXIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK', 'id' => 2], $result);
        $admin_user = FakeUser::adminUser();
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('admin', $admin_user->getOldUsername());
        $this->assertSame('bot@staging.olzimmerberg.ch', $admin_user->getEmail());
        $this->assertNull($admin_user->getEmailVerificationToken());
        $this->assertFalse($admin_user->hasPermission('verified_email'));
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
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
            'data' => [
                ...self::MINIMAL_INPUT['data'],
                'email' => 'admin-user@staging.olzimmerberg.ch',
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK', 'id' => 2], $result);
        $admin_user = FakeUser::adminUser();
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('admin', $admin_user->getOldUsername());
        $this->assertSame('admin-user@staging.olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame('admintoken', $admin_user->getEmailVerificationToken());
        $this->assertTrue($admin_user->hasPermission('verified_email'));
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertNull($admin_user->getPhone());
        $this->assertNull($admin_user->getGender());
        $this->assertNull($admin_user->getBirthdate());
        $this->assertNull($admin_user->getStreet());
        $this->assertNull($admin_user->getPostalCode());
        $this->assertNull($admin_user->getCity());
        $this->assertNull($admin_user->getRegion());
        $this->assertNull($admin_user->getCountryCode());
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

    public function testUpdateUserEndpointWithExistingUsername(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
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
        $endpoint->setRecaptchaUtils(new FakeRecaptchaUtils());

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING Bad user request',
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'username' => ['Dieser Benutzername ist bereits vergeben.'],
                // @phpstan-ignore-next-line
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
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
        $endpoint->setRecaptchaUtils(new FakeRecaptchaUtils());

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING Bad user request',
            ], $this->getLogs());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Es existiert bereits eine Person mit dieser E-Mail Adresse.'],
                // @phpstan-ignore-next-line
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
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
        $endpoint->setRecaptchaUtils(new FakeRecaptchaUtils());

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'data' => [
                ...self::MINIMAL_INPUT['data'],
                'avatarId' => '-',
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK', 'id' => 2], $result);
        $admin_user = FakeUser::adminUser();
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('admin', $admin_user->getOldUsername());
        $this->assertSame('bot@staging.olzimmerberg.ch', $admin_user->getEmail());
        $this->assertNull($admin_user->getEmailVerificationToken());
        $this->assertFalse($admin_user->hasPermission('verified_email'));
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertNull($admin_user->getPhone());
        $this->assertNull($admin_user->getGender());
        $this->assertNull($admin_user->getBirthdate());
        $this->assertNull($admin_user->getStreet());
        $this->assertNull($admin_user->getPostalCode());
        $this->assertNull($admin_user->getCity());
        $this->assertNull($admin_user->getRegion());
        $this->assertNull($admin_user->getCountryCode());
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

    // public function testUpdateUserEndpointNoAccess(): void {
    //     WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
    //     WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
    //     $endpoint = new UpdateUserEndpoint();
    //     $endpoint->runtimeSetup();

    //     try {
    //         $endpoint->call($this->getValidInput());
    //         $this->fail('Error expected');
    //     } catch (HttpError $err) {
    //         $this->assertSame([
    //             "INFO Valid user request",
    //             "WARNING HTTP error 403",
    //         ], $this->getLogs());

    //         $this->assertSame([
    //             [FakeRole::maximal(), 'default', 'default', 'role', null, 'roles'],
    //         ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

    //         $this->assertSame(403, $err->getCode());
    //     }
    // }

    // public function testUpdateUserEndpointNoSuchEntity(): void {
    //     WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
    //     WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
    //     $endpoint = new UpdateUserEndpoint();
    //     $endpoint->runtimeSetup();

    //     try {
    //         $endpoint->call([
    //             ...$this->getValidInput(),
    //             'id' => FakeOlzRepository::NULL_ID,
    //         ]);
    //         $this->fail('Error expected');
    //     } catch (HttpError $err) {
    //         $this->assertSame([
    //             "INFO Valid user request",
    //             "WARNING HTTP error 404",
    //         ], $this->getLogs());

    //         $this->assertSame(
    //             [],
    //             WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
    //         );

    //         $this->assertSame(404, $err->getCode());
    //     }
    // }

    // public function testUpdateUserEndpointNoEntityAccess(): void {
    //     WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
    //     WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
    //     $endpoint = new UpdateUserEndpoint();
    //     $endpoint->runtimeSetup();

    //     try {
    //         $endpoint->call($this->getValidInput());
    //         $this->fail('Error expected');
    //     } catch (HttpError $err) {
    //         $this->assertSame([
    //             "INFO Valid user request",
    //             "WARNING HTTP error 403",
    //         ], $this->getLogs());

    //         $this->assertSame([
    //             [FakeRole::maximal(), 'default', 'default', 'role', null, 'roles'],
    //         ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

    //         $this->assertSame(403, $err->getCode());
    //     }
    // }

    // public function testUpdateUserEndpointParentRoleAccess(): void {
    //     $id = FakeRole::subVorstandRole(false, 2)->getId();
    //     WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
    //     WithUtilsCache::get('authUtils')->authenticated_roles = [FakeRole::subVorstandRole(false, 1)];
    //     WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
    //     $endpoint = new UpdateUserEndpoint();
    //     $endpoint->runtimeSetup();

    //     $result = $endpoint->call([
    //         ...$this->getValidInput(),
    //         'id' => $id,
    //     ]);

    //     $this->assertSame([
    //         "INFO Valid user request",
    //         "NOTICE OLD:",
    //         "NOTICE NEW:",
    //         "INFO Valid user response",
    //     ], $this->getLogs());

    //     $this->assertSame([
    //         'status' => 'OK',
    //         'id' => $id,
    //     ], $result);

    //     $this->assertSame([
    //         [FakeRole::subVorstandRole(false, 2), null, null, null, null, 'roles'],
    //         [FakeRole::subVorstandRole(false, 1), null, null, null, null, 'roles'],
    //     ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

    //     $entity_manager = WithUtilsCache::get('entityManager');
    //     $this->assertCount(1, $entity_manager->persisted);
    //     $this->assertCount(1, $entity_manager->flushed_persisted);
    //     $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    //     $entity = $entity_manager->persisted[0];
    //     $this->assertSame($id, $entity->getId());
    //     $this->assertSame('test', $entity->getUsername());
    //     $this->assertSame('sub_sub_vorstand_role', $entity->getOldUsername());
    //     $this->assertSame('Test Role', $entity->getName());
    //     $this->assertSame('Title Test Role', $entity->getTitle());
    //     $this->assertSame('Description Test Role', $entity->getDescription());
    //     $this->assertSame('Just do it!', $entity->getGuide());
    //     $this->assertSame(FakeRole::vorstandRole()->getId(), $entity->getParentRoleId());
    //     $this->assertSame(2, $entity->getIndexWithinParent());
    //     $this->assertSame(6, $entity->getFeaturedIndex());
    //     $this->assertTrue($entity->getCanHaveChildRoles());
    //     $this->assertSame(1, $entity->getOnOff());

    //     $this->assertSame([
    //         [$entity, 1, 1, 1],
    //     ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

    //     $this->assertSame([
    //         [
    //             ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
    //             realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/roles/{$id}/img/",
    //         ],
    //         [
    //             ['uploaded_file1.pdf', 'uploaded_file2.txt'],
    //             realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/roles/{$id}/",
    //         ],
    //     ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    //     $this->assertSame([
    //         [
    //             ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
    //             realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/roles/{$id}/",
    //         ],
    //     ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    // }

    // public function testUpdateUserEndpointRoleAccess(): void {
    //     $id = FakeRole::subVorstandRole(false, 2)->getId();
    //     WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
    //     WithUtilsCache::get('authUtils')->authenticated_roles = [FakeRole::subVorstandRole(false, 2)];
    //     WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
    //     $endpoint = new UpdateUserEndpoint();
    //     $endpoint->runtimeSetup();

    //     $result = $endpoint->call([
    //         ...$this->getValidInput(),
    //         'id' => $id,
    //     ]);

    //     $this->assertSame([
    //         "INFO Valid user request",
    //         "NOTICE OLD:",
    //         "NOTICE NEW:",
    //         "INFO Valid user response",
    //     ], $this->getLogs());

    //     $this->assertSame([
    //         'status' => 'OK',
    //         'id' => $id,
    //     ], $result);

    //     $this->assertSame([
    //         [FakeRole::subVorstandRole(false, 2), null, null, null, null, 'roles'],
    //         [FakeRole::subVorstandRole(false, 1), null, null, null, null, 'roles'],
    //     ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

    //     $entity_manager = WithUtilsCache::get('entityManager');
    //     $this->assertCount(1, $entity_manager->persisted);
    //     $this->assertCount(1, $entity_manager->flushed_persisted);
    //     $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    //     $entity = $entity_manager->persisted[0];
    //     $this->assertSame($id, $entity->getId());
    //     $this->assertSame('test', $entity->getUsername());
    //     $this->assertSame('sub_sub_vorstand_role', $entity->getOldUsername());
    //     $this->assertSame('Test Role', $entity->getName());
    //     $this->assertSame('Title Test Role', $entity->getTitle());
    //     $this->assertSame('Description Test Role', $entity->getDescription());
    //     $this->assertSame('Just do it!', $entity->getGuide());
    //     // not updated:
    //     $this->assertSame(FakeRole::subVorstandRole(false, 1)->getId(), $entity->getParentRoleId());
    //     // not updated:
    //     $this->assertSame(0, $entity->getIndexWithinParent());
    //     // not updated:
    //     $this->assertNull($entity->getFeaturedIndex());
    //     // not updated:
    //     $this->assertTrue($entity->getCanHaveChildRoles());

    //     $this->assertSame([
    //         [$entity, 1, 1, 1],
    //     ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

    //     $this->assertSame([
    //         [
    //             ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
    //             realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/roles/{$id}/img/",
    //         ],
    //         [
    //             ['uploaded_file1.pdf', 'uploaded_file2.txt'],
    //             realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/roles/{$id}/",
    //         ],
    //     ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    //     $this->assertSame([
    //         [
    //             ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
    //             realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/roles/{$id}/",
    //         ],
    //     ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    // }

    // public function testUpdateUserEndpoint(): void {
    //     $id = FakeOlzRepository::MAXIMAL_ID;
    //     WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
    //     WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
    //     $endpoint = new UpdateUserEndpoint();
    //     $endpoint->runtimeSetup();

    //     mkdir(__DIR__.'/../../tmp/temp/');
    //     file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageA.jpg', '');
    //     file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageB.jpg', '');
    //     file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file1.pdf', '');
    //     file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file2.txt', '');
    //     mkdir(__DIR__.'/../../tmp/files/');
    //     mkdir(__DIR__.'/../../tmp/files/roles/');
    //     mkdir(__DIR__.'/../../tmp/img/');
    //     mkdir(__DIR__.'/../../tmp/img/roles/');

    //     $result = $endpoint->call($this->getValidInput());

    //     $this->assertSame([
    //         "INFO Valid user request",
    //         "NOTICE OLD:",
    //         "NOTICE NEW:",
    //         "INFO Valid user response",
    //     ], $this->getLogs());

    //     $this->assertSame([
    //         'status' => 'OK',
    //         'id' => $id,
    //     ], $result);

    //     $this->assertSame([
    //         [FakeRole::maximal(), 'default', 'default', 'role', null, 'roles'],
    //         [FakeRole::vorstandRole(), null, null, null, null, 'roles'],
    //     ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

    //     $entity_manager = WithUtilsCache::get('entityManager');
    //     $this->assertCount(1, $entity_manager->persisted);
    //     $this->assertCount(1, $entity_manager->flushed_persisted);
    //     $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    //     $entity = $entity_manager->persisted[0];
    //     $this->assertSame($id, $entity->getId());
    //     $this->assertSame('test', $entity->getUsername());
    //     $this->assertSame('test-role', $entity->getOldUsername());
    //     $this->assertSame('Test Role', $entity->getName());
    //     $this->assertSame('Title Test Role', $entity->getTitle());
    //     $this->assertSame('Description Test Role', $entity->getDescription());
    //     $this->assertSame('Just do it!', $entity->getGuide());
    //     $this->assertSame(FakeRole::vorstandRole()->getId(), $entity->getParentRoleId());
    //     $this->assertSame(2, $entity->getIndexWithinParent());
    //     $this->assertSame(6, $entity->getFeaturedIndex());
    //     $this->assertTrue($entity->getCanHaveChildRoles());
    //     $this->assertSame(1, $entity->getOnOff());

    //     $this->assertSame([
    //         [$entity, 1, 1, 1],
    //     ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

    //     $this->assertSame([
    //         [
    //             ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
    //             realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/roles/{$id}/img/",
    //         ],
    //         [
    //             ['uploaded_file1.pdf', 'uploaded_file2.txt'],
    //             realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/roles/{$id}/",
    //         ],
    //     ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    //     $this->assertSame([
    //         [
    //             ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
    //             realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/roles/{$id}/",
    //         ],
    //     ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    // }
}
