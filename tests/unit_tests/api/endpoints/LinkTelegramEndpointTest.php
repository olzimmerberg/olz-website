<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../../src/api/endpoints/LinkTelegramEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

class FakeLinkTelegramEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'User' => new FakeLinkTelegramEndpointUserRepository(),
        ];
    }

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }

    public function persist($object) {
        $this->persisted[] = $object;
    }

    public function flush() {
        $this->flushed = $this->persisted;
    }
}

class FakeLinkTelegramEndpointUserRepository {
    public function findOneBy($where) {
        if ($where === ['username' => 'admin']) {
            $admin_user = get_fake_user();
            $admin_user->setUsername('admin');
            return $admin_user;
        }
        return null;
    }
}

class FakeLinkTelegramEndpointTelegramUtils {
    public function getFreshChatLinkForUser($user) {
        if ($user->getUsername() == 'admin') {
            return 'correct-link';
        }
        return 'wrong-link';
    }
}

/**
 * @internal
 * @covers \LinkTelegramEndpoint
 */
final class LinkTelegramEndpointTest extends TestCase {
    public function testLinkTelegramEndpointIdent(): void {
        $endpoint = new LinkTelegramEndpoint();
        $this->assertSame('LinkTelegramEndpoint', $endpoint->getIdent());
    }

    public function testLinkTelegramEndpoint(): void {
        $entity_manager = new FakeLinkTelegramEndpointEntityManager();
        $telegram_utils = new FakeLinkTelegramEndpointTelegramUtils();
        $logger = new Logger('LinkTelegramEndpointTest');
        $endpoint = new LinkTelegramEndpoint();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setTelegramUtils($telegram_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame(['chatLink' => 'correct-link'], $result);
    }
}
