<?php

declare(strict_types=1);

use Monolog\Logger;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../../../src/api/endpoints/UpdateUserEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
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
 * @covers \UpdateUserEndpoint
 */
final class UpdateUserEndpointTest extends UnitTestCase {
    const VALID_INPUT = [
        'id' => 2,
        'firstName' => 'First',
        'lastName' => 'Last',
        'username' => 'test',
        'email' => 'test@olzimmerberg.ch',
        'phone' => '+41441234567',
        'gender' => 'F',
        'birthdate' => '1992-08-05 12:00:00',
        'street' => 'Teststrasse 123',
        'postalCode' => '1234',
        'city' => 'Muster',
        'region' => 'ZH',
        'countryCode' => 'CH',
        'avatarId' => 'fake-avatar-id.jpg',
    ];

    public function testUpdateUserEndpointIdent(): void {
        $endpoint = new UpdateUserEndpoint();
        $this->assertSame('UpdateUserEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUserEndpointWrongUsername(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $logger = new Logger('UpdateUserEndpointTest');
        $endpoint = new UpdateUserEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ], $session->session_storage);
    }

    public function testUpdateUserEndpointInvalidNewUsername(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $logger = new Logger('UpdateUserEndpointTest');
        $endpoint = new UpdateUserEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        try {
            $endpoint->call(array_merge(
                self::VALID_INPUT,
                ['username' => 'invalid@']
            ));
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'username' => ['Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten.'],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                'auth' => 'ftp',
                'root' => 'karten',
                'user' => 'admin',
            ], $session->session_storage);
        }
    }

    public function testUpdateUserEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $env_utils->fake_data_path = 'fake-data-path/';
        $logger = new Logger('UpdateUserEndpointTest');
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('test@olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame('+41441234567', $admin_user->getPhone());
        $this->assertSame('F', $admin_user->getGender());
        $this->assertSame('1992-08-05 12:00:00', $admin_user->getBirthdate()->format('Y-m-d H:i:s'));
        $this->assertSame('Teststrasse 123', $admin_user->getStreet());
        $this->assertSame('1234', $admin_user->getPostalCode());
        $this->assertSame('Muster', $admin_user->getCity());
        $this->assertSame('ZH', $admin_user->getRegion());
        $this->assertSame('CH', $admin_user->getCountryCode());
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

    public function testUpdateUserEndpointRemoveAvatar(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $env_utils = new FakeEnvUtils();
        $env_utils->fake_data_path = 'fake-data-path/';
        $logger = new Logger('UpdateUserEndpointTest');
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(array_merge(
            self::VALID_INPUT,
            ['avatarId' => '-']
        ));

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('test@olzimmerberg.ch', $admin_user->getEmail());
        $this->assertSame('+41441234567', $admin_user->getPhone());
        $this->assertSame('F', $admin_user->getGender());
        $this->assertSame('1992-08-05 12:00:00', $admin_user->getBirthdate()->format('Y-m-d H:i:s'));
        $this->assertSame('Teststrasse 123', $admin_user->getStreet());
        $this->assertSame('1234', $admin_user->getPostalCode());
        $this->assertSame('Muster', $admin_user->getCity());
        $this->assertSame('ZH', $admin_user->getRegion());
        $this->assertSame('CH', $admin_user->getCountryCode());
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
