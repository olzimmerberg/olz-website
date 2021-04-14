<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/api/endpoints/UpdateOlzTextEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/OlzText.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeUpdateOlzTextEndpointAuthUtils {
    public $has_permission;

    public function hasPermission($query) {
        return $this->has_permission;
    }
}

class FakeUpdateOlzTextEndpointEntityManager {
    public $persisted = [];
    public $removed = [];
    public $flushed_persisted = [];
    public $flushed_removed = [];
    public $repositories = [];

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }

    public function persist($object) {
        $this->persisted[] = $object;
    }

    public function remove($object) {
        $this->removed[] = $object;
    }

    public function flush() {
        $this->flushed_persisted = $this->persisted;
        $this->flushed_removed = $this->removed;
    }
}

class FakeUpdateOlzTextEndpointUserRepository {
    public function __construct() {
        $admin_user = get_fake_user();
        $admin_user->setId(1);
        $admin_user->setUsername('admin');
        $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
        $admin_user->setZugriff('all');
        $admin_user->setRoot('karten');
        $this->admin_user = $admin_user;
    }

    public function findOneBy($where) {
        if ($where === ['username' => 'admin']) {
            return $this->admin_user;
        }
        if ($where === ['username' => 'noaccess']) {
            $noaccess_user = get_fake_user();
            $noaccess_user->setZugriff('ftp');
            return $noaccess_user;
        }
        return null;
    }
}

class FakeUpdateOlzTextEndpointOlzTextRepository {
    public function __construct() {
        $olz_text = new OlzText();
        $olz_text->setId(1);
        $this->olz_text = $olz_text;
    }

    public function findOneBy($where) {
        if ($where === ['id' => 1]) {
            return $this->olz_text;
        }
        return null;
    }
}

/**
 * @internal
 * @covers \UpdateOlzTextEndpoint
 */
final class UpdateOlzTextEndpointTest extends UnitTestCase {
    public function testUpdateOlzTextEndpointIdent(): void {
        $endpoint = new UpdateOlzTextEndpoint();
        $this->assertSame('UpdateOlzTextEndpoint', $endpoint->getIdent());
    }

    public function testUpdateOlzTextEndpointNoAccess(): void {
        $auth_utils = new FakeUpdateOlzTextEndpointAuthUtils();
        $auth_utils->has_permission = false;
        $entity_manager = new FakeUpdateOlzTextEndpointEntityManager();
        $user_repo = new FakeUpdateOlzTextEndpointUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $logger = new Logger('UpdateOlzTextEndpointTest');
        $endpoint = new UpdateOlzTextEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'id' => 1,
            'text' => 'New **content**!',
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
    }

    public function testUpdateOlzTextEndpointNoEntry(): void {
        $auth_utils = new FakeUpdateOlzTextEndpointAuthUtils();
        $auth_utils->has_permission = true;
        $entity_manager = new FakeUpdateOlzTextEndpointEntityManager();
        $user_repo = new FakeUpdateOlzTextEndpointUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $olz_text_repo = new FakeUpdateOlzTextEndpointOlzTextRepository();
        $entity_manager->repositories['OlzText'] = $olz_text_repo;
        $logger = new Logger('UpdateOlzTextEndpointTest');
        $endpoint = new UpdateOlzTextEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'id' => 3,
            'text' => 'New **content**!',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame('New **content**!', $entity_manager->persisted[0]->getText());
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame('New **content**!', $entity_manager->flushed_persisted[0]->getText());
    }

    public function testUpdateOlzTextEndpoint(): void {
        $auth_utils = new FakeUpdateOlzTextEndpointAuthUtils();
        $auth_utils->has_permission = true;
        $entity_manager = new FakeUpdateOlzTextEndpointEntityManager();
        $user_repo = new FakeUpdateOlzTextEndpointUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $olz_text_repo = new FakeUpdateOlzTextEndpointOlzTextRepository();
        $entity_manager->repositories['OlzText'] = $olz_text_repo;
        $logger = new Logger('UpdateOlzTextEndpointTest');
        $endpoint = new UpdateOlzTextEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'id' => 1,
            'text' => 'New **content**!',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $olz_text = $entity_manager->getRepository('OlzText')->olz_text;
        $this->assertSame(1, $olz_text->getId());
        $this->assertSame('New **content**!', $olz_text->getText());
    }
}
