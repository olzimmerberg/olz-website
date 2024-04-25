<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\DeleteUserEndpoint;
use Olz\Entity\AccessToken;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\StravaLink;
use Olz\Entity\TelegramLink;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use Olz\Utils\WithUtilsCache;

class FakeDeleteUserEndpointNewsEntryRepository extends FakeOlzRepository {
    public $has_news = true;

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        return [new NewsEntry()];
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        return $this->has_news ? new NewsEntry() : null;
    }
}

class FakeDeleteUserEndpointNotificationSubscriptionRepository extends FakeOlzRepository {
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        return [new NotificationSubscription()];
    }
}

class FakeDeleteUserEndpointTelegramLinkRepository extends FakeOlzRepository {
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        return [new TelegramLink()];
    }
}

class FakeDeleteUserEndpointStravaLinkRepository extends FakeOlzRepository {
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        return [new StravaLink()];
    }
}

class FakeDeleteUserEndpointAccessTokenRepository extends FakeOlzRepository {
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        return [new AccessToken()];
    }
}

/**
 * @internal
 *
 * @coversNothing
 */
class DeleteUserEndpointForTest extends DeleteUserEndpoint {
    public $is_file_calls = [];
    public $unlink_calls = [];
    public $rename_calls = [];

    protected function isFile($path) {
        $this->is_file_calls[] = $path;
        return true;
    }

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
 * @covers \Olz\Api\Endpoints\DeleteUserEndpoint
 */
final class DeleteUserEndpointTest extends UnitTestCase {
    public function testDeleteUserEndpointIdent(): void {
        $endpoint = new DeleteUserEndpoint();
        $this->assertSame('DeleteUserEndpoint', $endpoint->getIdent());
    }

    public function testDeleteUserEndpointWrongUsername(): void {
        $endpoint = new DeleteUserEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call(['id' => 1]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ], $session->session_storage);
    }

    public function testDeleteUserEndpointCannotDelete(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $news_repo = new FakeDeleteUserEndpointNewsEntryRepository($entity_manager);
        $news_repo->has_news = true;
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $entity_manager->repositories[NotificationSubscription::class] = new FakeDeleteUserEndpointNotificationSubscriptionRepository($entity_manager);
        $entity_manager->repositories[TelegramLink::class] = new FakeDeleteUserEndpointTelegramLinkRepository($entity_manager);
        $entity_manager->repositories[StravaLink::class] = new FakeDeleteUserEndpointStravaLinkRepository($entity_manager);
        $entity_manager->repositories[AccessToken::class] = new FakeDeleteUserEndpointAccessTokenRepository($entity_manager);
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new DeleteUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call(['id' => 2]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = FakeUser::adminUser();
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('Admin', $admin_user->getFirstName());
        $this->assertSame('Istrator', $admin_user->getLastName());
        $this->assertSame('admin', $admin_user->getUsername());
        $this->assertSame('', $admin_user->getEmail());
        $this->assertSame('', $admin_user->getPasswordHash());
        $this->assertSame('', $admin_user->getPhone());
        $this->assertNull($admin_user->getGender());
        $this->assertNull($admin_user->getBirthdate());
        $this->assertNull($admin_user->getStreet());
        $this->assertNull($admin_user->getPostalCode());
        $this->assertNull($admin_user->getCity());
        $this->assertNull($admin_user->getRegion());
        $this->assertNull($admin_user->getCountryCode());
        $this->assertSame('', $admin_user->getPermissions());
        $this->assertNull($admin_user->getRoot());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $admin_user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertCount(5, $entity_manager->removed);
        $this->assertTrue($entity_manager->removed[0] instanceof NewsEntry);
        $this->assertTrue($entity_manager->removed[1] instanceof NotificationSubscription);
        $this->assertTrue($entity_manager->removed[2] instanceof TelegramLink);
        $this->assertTrue($entity_manager->removed[3] instanceof StravaLink);
        $this->assertTrue($entity_manager->removed[4] instanceof AccessToken);
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            'fake-data-path/img/users/2.jpg',
        ], $endpoint->is_file_calls);
        $this->assertSame([
            'fake-data-path/img/users/2.jpg',
        ], $endpoint->unlink_calls);
        $this->assertSame([], $endpoint->rename_calls);
    }

    public function testDeleteUserEndpointCanDelete(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $news_repo = new FakeDeleteUserEndpointNewsEntryRepository($entity_manager);
        $news_repo->has_news = false;
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $entity_manager->repositories[NotificationSubscription::class] = new FakeDeleteUserEndpointNotificationSubscriptionRepository($entity_manager);
        $entity_manager->repositories[TelegramLink::class] = new FakeDeleteUserEndpointTelegramLinkRepository($entity_manager);
        $entity_manager->repositories[StravaLink::class] = new FakeDeleteUserEndpointStravaLinkRepository($entity_manager);
        $entity_manager->repositories[AccessToken::class] = new FakeDeleteUserEndpointAccessTokenRepository($entity_manager);
        WithUtilsCache::get('envUtils')->fake_data_path = 'fake-data-path/';
        $endpoint = new DeleteUserEndpointForTest();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'user',
        ];
        $endpoint->setSession($session);

        $result = $endpoint->call(['id' => 1]);

        $this->assertSame([
            'INFO Valid user request',
            'WARNING Removing user user (User ID: 1).',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $default_user = FakeUser::defaultUser();
        $this->assertCount(6, $entity_manager->removed);
        $this->assertTrue($entity_manager->removed[0] instanceof NewsEntry);
        $this->assertTrue($entity_manager->removed[1] instanceof NotificationSubscription);
        $this->assertTrue($entity_manager->removed[2] instanceof TelegramLink);
        $this->assertTrue($entity_manager->removed[3] instanceof StravaLink);
        $this->assertTrue($entity_manager->removed[4] instanceof AccessToken);
        $this->assertSame($default_user, $entity_manager->removed[5]);
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
        $this->assertSame([], $session->session_storage);
        $this->assertSame([
            'fake-data-path/img/users/1.jpg',
        ], $endpoint->is_file_calls);
        $this->assertSame([
            'fake-data-path/img/users/1.jpg',
        ], $endpoint->unlink_calls);
        $this->assertSame([], $endpoint->rename_calls);
    }
}
