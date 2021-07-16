<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeUserRepository.php';
require_once __DIR__.'/../../../../src/api/endpoints/LinkTelegramEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

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
final class LinkTelegramEndpointTest extends UnitTestCase {
    public function testLinkTelegramEndpointIdent(): void {
        $endpoint = new LinkTelegramEndpoint();
        $this->assertSame('LinkTelegramEndpoint', $endpoint->getIdent());
    }

    public function testLinkTelegramEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
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
