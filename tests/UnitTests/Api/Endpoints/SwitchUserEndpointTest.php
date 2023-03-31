<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\SwitchUserEndpoint;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\SwitchUserEndpoint
 */
final class SwitchUserEndpointTest extends UnitTestCase {
    public function testSwitchUserEndpointIdent(): void {
        $endpoint = new SwitchUserEndpoint();
        $this->assertSame('SwitchUserEndpoint', $endpoint->getIdent());
    }

    public function testSwitchUserEndpointWithoutInput(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new SwitchUserEndpoint();
        $endpoint->setLog($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'userId' => ["Fehlender Schlüssel: userId."],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testSwitchUserEndpointWithNullInput(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new SwitchUserEndpoint();
        $endpoint->setLog($logger);
        try {
            $result = $endpoint->call([
                'userId' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'userId' => [['.' => ['Feld darf nicht leer sein.']]],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testSwitchUserEndpointCanSwitchToChild(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $endpoint = new SwitchUserEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'parent',
            'root' => 'parent',
            'user' => 'child1',
            'user_id' => 5,
            'auth_user' => 'parent',
            'auth_user_id' => 4,
        ];
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'userId' => 5, // child1
        ]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => 'child1',
            'root' => 'child1',
            'user' => 'child1',
            'user_id' => 5,
            'auth_user' => 'parent',
            'auth_user_id' => 4,
        ], $session->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testSwitchUserEndpointCanSwitchBetweenChildren(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $endpoint = new SwitchUserEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'child1',
            'root' => 'child1',
            'user' => 'child1',
            'user_id' => 5,
            'auth_user' => 'parent',
            'auth_user_id' => 4,
        ];
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'userId' => 6, // child2
        ]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => 'child2',
            'root' => 'child2',
            'user' => 'child2',
            'user_id' => 6,
            'auth_user' => 'parent',
            'auth_user_id' => 4,
        ], $session->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testSwitchUserEndpointCanSwitchToParent(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $endpoint = new SwitchUserEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'child1',
            'root' => 'child1',
            'user' => 'child1',
            'user_id' => 5,
            'auth_user' => 'parent',
            'auth_user_id' => 4,
        ];
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'userId' => 4, // child2
        ]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => 'parent',
            'root' => 'parent',
            'user' => 'parent',
            'user_id' => 4,
            'auth_user' => 'parent',
            'auth_user_id' => 4,
        ], $session->session_storage);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testSwitchUserEndpointCannotSwitchToInexistentUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $endpoint = new SwitchUserEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'parent',
            'root' => 'parent',
            'user' => 'child1',
            'user_id' => 5,
            'auth_user' => 'parent',
            'auth_user_id' => 4,
        ];
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                'userId' => 404, // inexistent
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(403, $httperr->getCode());
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame([
                'auth' => 'parent',
                'root' => 'parent',
                'user' => 'child1',
                'user_id' => 5,
                'auth_user' => 'parent',
                'auth_user_id' => 4,
            ], $session->session_storage);
        }
    }

    public function testSwitchUserEndpointCannotSwitchToNonChildUser(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $logger = Fake\FakeLogger::create();
        $endpoint = new SwitchUserEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'parent',
            'root' => 'parent',
            'user' => 'child1',
            'user_id' => 5,
            'auth_user' => 'parent',
            'auth_user_id' => 4,
        ];
        $endpoint->setSession($session);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([
                'userId' => 3, // vorstand (not a child)
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(403, $httperr->getCode());
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame([
                'auth' => 'parent',
                'root' => 'parent',
                'user' => 'child1',
                'user_id' => 5,
                'auth_user' => 'parent',
                'auth_user_id' => 4,
            ], $session->session_storage);
        }
    }
}
