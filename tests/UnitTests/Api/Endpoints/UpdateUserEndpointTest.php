<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\UpdateUserEndpoint;
use Olz\Entity\User;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\MemorySession;
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
    public const VALID_INPUT = [
        'id' => 2,
        'firstName' => 'First',
        'lastName' => 'Last',
        'username' => 'test',
        'email' => 'bot@test.olzimmerberg.ch',
        'phone' => '+41441234567',
        'gender' => 'F',
        'birthdate' => '1992-08-05 12:00:00',
        'street' => 'Teststrasse 123',
        'postalCode' => '1234',
        'city' => 'Muster',
        'region' => 'ZH',
        'countryCode' => 'CH',
        'siCardNumber' => 1234567,
        'solvNumber' => 'JACK7NORRIS',
        'avatarId' => 'fake-avatar-id.jpg',
    ];

    public function testUpdateUserEndpointIdent(): void {
        $endpoint = new UpdateUserEndpoint();
        $this->assertSame('UpdateUserEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUserEndpointWrongUsername(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $logger = FakeLogger::create();
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
        $endpoint->setLog($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
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
        $logger = FakeLogger::create();
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
        $endpoint->setLog($logger);

        try {
            $endpoint->call(array_merge(
                self::VALID_INPUT,
                ['username' => 'invalid@']
            ));
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
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

    public function testUpdateUserEndpointWithNewOlzimmerbergEmail(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $logger = FakeLogger::create();
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
        $endpoint->setLog($logger);

        try {
            $endpoint->call(array_merge(
                self::VALID_INPUT,
                ['email' => 'bot@olzimmerberg.ch']
            ));
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                'email' => ['Bitte keine @olzimmerberg.ch E-Mail verwenden.'],
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
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $env_utils->fake_data_path = 'fake-data-path/';
        $logger = FakeLogger::create();
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('bot@test.olzimmerberg.ch', $admin_user->getEmail());
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

    public function testUpdateUserEndpointRemoveAvatar(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $env_utils->fake_data_path = 'fake-data-path/';
        $logger = FakeLogger::create();
        $endpoint = new UpdateUserEndpointForTest();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEnvUtils($env_utils);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call(array_merge(
            self::VALID_INPUT,
            ['avatarId' => '-']
        ));

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository(User::class)->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('First', $admin_user->getFirstName());
        $this->assertSame('Last', $admin_user->getLastName());
        $this->assertSame('test', $admin_user->getUsername());
        $this->assertSame('bot@test.olzimmerberg.ch', $admin_user->getEmail());
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
        $this->assertSame([
            'fake-data-path/img/users/2.jpg',
        ], $endpoint->unlink_calls);
        $this->assertSame([], $endpoint->rename_calls);
    }
}
