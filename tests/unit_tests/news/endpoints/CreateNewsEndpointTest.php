<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/news/endpoints/CreateNewsEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeUserRepository.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeCreateNewsEndpointRoleRepository {
    public function __construct() {
        $admin_role = get_fake_role();
        $admin_role->setId(1);
        $this->admin_role = $admin_role;
    }

    public function findOneBy($where) {
        if ($where === ['id' => 1]) {
            return $this->admin_role;
        }
        return null;
    }
}

class FakeCreateNewsEndpointAuthUtils {
    public $has_permission_by_query = [];

    public function hasPermission($query) {
        $has_permission = $this->has_permission_by_query[$query] ?? null;
        if ($has_permission === null) {
            throw new Exception("hasPermission has not been mocked for {$query}");
        }
        return $has_permission;
    }

    public function getSessionUser() {
        return get_fake_user();
    }
}

/**
 * @internal
 * @covers \CreateNewsEndpoint
 */
final class CreateNewsEndpointTest extends UnitTestCase {
    public function testCreateNewsEndpointIdent(): void {
        $endpoint = new CreateNewsEndpoint();
        $this->assertSame('CreateNewsEndpoint', $endpoint->getIdent());
    }

    public function testCreateNewsEndpointNoAccess(): void {
        $auth_utils = new FakeCreateNewsEndpointAuthUtils();
        $auth_utils->has_permission_by_query = ['news' => false];
        $logger = FakeLogger::create();
        $endpoint = new CreateNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'author' => 't.u.',
            'authorUserId' => 2,
            'authorRoleId' => 2,
            'title' => 'Test Titel',
            'teaser' => 'Das muss man gelesen haben!',
            'content' => 'Sehr viel Inhalt.',
            'external_url' => null,
            'tags' => ['test', 'unit'],
            'terminId' => null,
            'onOff' => true,
            'imageIds' => [],
            'fileIds' => [],
        ]);

        $this->assertSame(['status' => 'ERROR', 'newsId' => null], $result);
    }

    public function testCreateNewsEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $role_repo = new FakeCreateNewsEndpointRoleRepository();
        $entity_manager->repositories['Role'] = $role_repo;
        $auth_utils = new FakeCreateNewsEndpointAuthUtils();
        $auth_utils->has_permission_by_query = ['news' => true];
        $logger = FakeLogger::create();
        $endpoint = new CreateNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'author' => 't.u.',
            'authorUserId' => 2,
            'authorRoleId' => 2,
            'title' => 'Test Titel',
            'teaser' => 'Das muss man gelesen haben!',
            'content' => 'Sehr viel Inhalt.',
            'external_url' => null,
            'tags' => ['test', 'unit'],
            'terminId' => null,
            'onOff' => true,
            'imageIds' => [],
            'fileIds' => [],
        ]);

        $this->assertSame([
            'status' => 'OK',
            'newsId' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $news_entry->getId());
        $this->assertSame(null, $news_entry->getOwnerUser());
        $this->assertSame($role_repo->admin_role, $news_entry->getOwnerRole());
        $this->assertSame('t.u.', $news_entry->getAuthor());
        $this->assertSame($user_repo->admin_user, $news_entry->getAuthorUser());
        $this->assertSame(null, $news_entry->getAuthorRole());
        $this->assertSame('Test Titel', $news_entry->getTitle());
        $this->assertSame('Das muss man gelesen haben!', $news_entry->getTeaser());
        $this->assertSame('Sehr viel Inhalt.', $news_entry->getContent());
        $this->assertSame(null, $news_entry->getExternalUrl());
        $this->assertSame(' test unit ', $news_entry->getTags());
        $this->assertSame(null, $news_entry->getTermin());
        $this->assertSame(1, $news_entry->getOnOff());
    }
}
