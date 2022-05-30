<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../_/api/endpoints/DeleteUserEndpoint.php';
require_once __DIR__.'/../../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../../_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../../_/utils/session/MemorySession.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeDeleteUserEndpointNewsEntryRepository {
    public $has_news = true;

    public function findBy($where) {
        return [new NewsEntry()];
    }

    public function findOneBy($where) {
        return $this->has_news ? new NewsEntry() : null;
    }
}

class FakeDeleteUserEndpointNotificationSubscriptionRepository {
    public function findBy($where) {
        return [new NotificationSubscription()];
    }
}

class FakeDeleteUserEndpointTelegramLinkRepository {
    public function findBy($where) {
        return [new TelegramLink()];
    }
}

class FakeDeleteUserEndpointStravaLinkRepository {
    public function findBy($where) {
        return [new StravaLink()];
    }
}

class FakeDeleteUserEndpointGoogleLinkRepository {
    public function findBy($where) {
        return [new GoogleLink()];
    }
}

class FakeDeleteUserEndpointFacebookLinkRepository {
    public function findBy($where) {
        return [new FacebookLink()];
    }
}

class FakeDeleteUserEndpointAccessTokenRepository {
    public function findBy($where) {
        return [new AccessToken()];
    }
}

/**
 * @internal
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
 * @covers \DeleteUserEndpoint
 */
final class DeleteUserEndpointTest extends UnitTestCase {
    public function testDeleteUserEndpointIdent(): void {
        $endpoint = new DeleteUserEndpoint();
        $this->assertSame('DeleteUserEndpoint', $endpoint->getIdent());
    }

    public function testDeleteUserEndpointWrongUsername(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $logger = new Logger('DeleteUserEndpointTest');
        $endpoint = new DeleteUserEndpoint();
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

        $result = $endpoint->call(['id' => 1]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'wrong_user',
        ], $session->session_storage);
    }

    public function testDeleteUserEndpointCannotDelete(): void {
        $entity_manager = new FakeEntityManager();
        $news_repo = new FakeDeleteUserEndpointNewsEntryRepository();
        $news_repo->has_news = true;
        $entity_manager->repositories['NewsEntry'] = $news_repo;
        $entity_manager->repositories['NotificationSubscription'] = new FakeDeleteUserEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories['TelegramLink'] = new FakeDeleteUserEndpointTelegramLinkRepository();
        $entity_manager->repositories['StravaLink'] = new FakeDeleteUserEndpointStravaLinkRepository();
        $entity_manager->repositories['GoogleLink'] = new FakeDeleteUserEndpointGoogleLinkRepository();
        $entity_manager->repositories['FacebookLink'] = new FakeDeleteUserEndpointFacebookLinkRepository();
        $entity_manager->repositories['AccessToken'] = new FakeDeleteUserEndpointAccessTokenRepository();
        $auth_utils = new FakeAuthUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $env_utils->fake_data_path = 'fake-data-path/';
        $logger = new Logger('DeleteUserEndpointTest');
        $endpoint = new DeleteUserEndpointForTest();
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
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['id' => 2]);

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(2, $admin_user->getId());
        $this->assertSame('Admin', $admin_user->getFirstName());
        $this->assertSame('Istrator', $admin_user->getLastName());
        $this->assertSame('admin', $admin_user->getUsername());
        $this->assertSame('', $admin_user->getEmail());
        $this->assertSame('', $admin_user->getPasswordHash());
        $this->assertSame('', $admin_user->getPhone());
        $this->assertSame(null, $admin_user->getGender());
        $this->assertSame(null, $admin_user->getBirthdate());
        $this->assertSame(null, $admin_user->getStreet());
        $this->assertSame(null, $admin_user->getPostalCode());
        $this->assertSame(null, $admin_user->getCity());
        $this->assertSame(null, $admin_user->getRegion());
        $this->assertSame(null, $admin_user->getCountryCode());
        $this->assertSame('', $admin_user->getZugriff());
        $this->assertSame(null, $admin_user->getRoot());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $admin_user->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(7, count($entity_manager->removed));
        $this->assertTrue($entity_manager->removed[0] instanceof NewsEntry);
        $this->assertTrue($entity_manager->removed[1] instanceof NotificationSubscription);
        $this->assertTrue($entity_manager->removed[2] instanceof TelegramLink);
        $this->assertTrue($entity_manager->removed[3] instanceof StravaLink);
        $this->assertTrue($entity_manager->removed[4] instanceof GoogleLink);
        $this->assertTrue($entity_manager->removed[5] instanceof FacebookLink);
        $this->assertTrue($entity_manager->removed[6] instanceof AccessToken);
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
        $entity_manager = new FakeEntityManager();
        $news_repo = new FakeDeleteUserEndpointNewsEntryRepository();
        $news_repo->has_news = false;
        $entity_manager->repositories['NewsEntry'] = $news_repo;
        $entity_manager->repositories['NotificationSubscription'] = new FakeDeleteUserEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories['TelegramLink'] = new FakeDeleteUserEndpointTelegramLinkRepository();
        $entity_manager->repositories['StravaLink'] = new FakeDeleteUserEndpointStravaLinkRepository();
        $entity_manager->repositories['GoogleLink'] = new FakeDeleteUserEndpointGoogleLinkRepository();
        $entity_manager->repositories['FacebookLink'] = new FakeDeleteUserEndpointFacebookLinkRepository();
        $entity_manager->repositories['AccessToken'] = new FakeDeleteUserEndpointAccessTokenRepository();
        $auth_utils = new FakeAuthUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $env_utils->fake_data_path = 'fake-data-path/';
        $logger = new Logger('DeleteUserEndpointTest');
        $endpoint = new DeleteUserEndpointForTest();
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
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['id' => 2]);

        $this->assertSame(['status' => 'OK'], $result);
        $admin_user = $entity_manager->getRepository('User')->admin_user;
        $this->assertSame(8, count($entity_manager->removed));
        $this->assertTrue($entity_manager->removed[0] instanceof NewsEntry);
        $this->assertTrue($entity_manager->removed[1] instanceof NotificationSubscription);
        $this->assertTrue($entity_manager->removed[2] instanceof TelegramLink);
        $this->assertTrue($entity_manager->removed[3] instanceof StravaLink);
        $this->assertTrue($entity_manager->removed[4] instanceof GoogleLink);
        $this->assertTrue($entity_manager->removed[5] instanceof FacebookLink);
        $this->assertTrue($entity_manager->removed[6] instanceof AccessToken);
        $this->assertSame($admin_user, $entity_manager->removed[7]);
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
}
