<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Users\Endpoints;

use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Users\Endpoints\UpdateUserEndpoint;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

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
            'parentUserId' => null,
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
            'avatarImageId' => null,
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
            'parentUserId' => 1,
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
            'avatarImageId' => 'fake-avatar-id.jpg',
        ],
    ];

    public function testUpdateUserEndpointWrongUsername(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ];

        try {
            $endpoint->call(self::MINIMAL_INPUT);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                'auth' => 'ftp',
                'root' => 'karten',
                'user' => 'wrong_user',
            ], WithUtilsCache::get('session')->session_storage);

            $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
            $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
        }
    }

    public function testUpdateUserEndpointInvalidNewUsername(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

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
                "NOTICE Bad user request",
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
            ], WithUtilsCache::get('session')->session_storage);

            $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
            $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
        }
    }

    public function testUpdateUserEndpointWithNewOlzimmerbergEmail(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

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
                "NOTICE Bad user request",
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
            ], WithUtilsCache::get('session')->session_storage);

            $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
            $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
        }
    }

    public function testUpdateUserEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals(['custom' => ['status' => 'OK'], 'id' => 2], $result);
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
        ], WithUtilsCache::get('session')->session_storage);

        $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testUpdateUserEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

        $result = $endpoint->call(self::MAXIMAL_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals(['custom' => ['status' => 'OK'], 'id' => 2], $result);
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
        $this->assertSame('1992-08-05 12:00:00', $admin_user->getBirthdate()?->format('Y-m-d H:i:s'));
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
        ], WithUtilsCache::get('session')->session_storage);

        $id = 2;
        $this->assertSame([
            [
                ['fake-avatar-id.jpg'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/users/{$id}/img/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
            [
                ['fake-avatar-id.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/users/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testUpdateUserEndpointSameEmail(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

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
        $this->assertEquals(['custom' => ['status' => 'OK'], 'id' => 2], $result);
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
        ], WithUtilsCache::get('session')->session_storage);

        $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testUpdateUserEndpointWithExistingUsername(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $entity_manager = WithUtilsCache::get('entityManager');
        $existing_user = new User();
        $existing_user->setId(123);
        $entity_manager->repositories[User::class]->entityToBeFoundForQuery =
            function ($where) use ($existing_user) {
                if ($where === ['id' => 2]) {
                    return FakeUser::adminUser();
                }
                if ($where === ['username' => 'test']) {
                    return $existing_user;
                }
                return null;
            };
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'INFO Valid user request',
                'NOTICE Bad user request',
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
            ], WithUtilsCache::get('session')->session_storage);

            $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
            $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
        }
    }

    public function testUpdateUserEndpointWithExistingEmail(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $entity_manager = WithUtilsCache::get('entityManager');
        $existing_user = new User();
        $existing_user->setId(123);
        $entity_manager->repositories[User::class]->entityToBeFoundForQuery =
            function ($where) use ($existing_user) {
                if ($where === ['id' => 2]) {
                    return FakeUser::adminUser();
                }
                if ($where === ['email' => 'bot@staging.olzimmerberg.ch']) {
                    return $existing_user;
                }
                return null;
            };
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

        try {
            $endpoint->call([
                ...self::MINIMAL_INPUT,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'INFO Valid user request',
                'NOTICE Bad user request',
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
            ], WithUtilsCache::get('session')->session_storage);

            $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
            $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
        }
    }

    public function testUpdateUserEndpointRemoveAvatar(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];

        $result = $endpoint->call([
            ...self::MINIMAL_INPUT,
            'data' => [
                ...self::MINIMAL_INPUT['data'],
                'avatarImageId' => null,
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertEquals(['custom' => ['status' => 'OK'], 'id' => 2], $result);
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
        ], WithUtilsCache::get('session')->session_storage);

        $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testUpdateUserEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(self::MAXIMAL_INPUT);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());

            $this->assertSame([
                [FakeUser::adminUser(), 'admin', 'admin', null, null, 'users'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
            $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateUserEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['users' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateUserEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...self::MAXIMAL_INPUT,
                'id' => FakeOlzRepository::NULL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
            $this->assertSame([], WithUtilsCache::get('imageUtils')->generatedThumbnails);

            $this->assertSame(404, $err->getCode());
        }
    }
}
