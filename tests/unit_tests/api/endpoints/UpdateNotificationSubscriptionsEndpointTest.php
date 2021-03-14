<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/endpoints/UpdateNotificationSubscriptionsEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/NotificationSubscription.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

class FakeNotificationSubscriptionsEndpointEntityManager {
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

class FakeNotificationSubscriptionsEndpointNotificationSubscriptionRepository {
    public function findBy($where) {
        $user = new User();
        $subscription = new NotificationSubscription();
        $subscription->setId(123);
        $subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
        $subscription->setUser($user);
        $subscription->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
        $subscription->setNotificationTypeArgs('{}');
        return [$subscription];
    }
}

class FakeNotificationSubscriptionsEndpointUserRepository {
    public function __construct() {
        $admin_user = get_fake_user();
        $admin_user->setId(1);
        $admin_user->setUsername('admin');
        $admin_user->setPasswordHash(password_hash('adm1n', PASSWORD_DEFAULT));
        $admin_user->setZugriff('ftp');
        $admin_user->setRoot('karten');
        $this->admin_user = $admin_user;
    }

    public function findOneBy($where) {
        if ($where === ['id' => 1]) {
            return $this->admin_user;
        }
        return null;
    }
}

/**
 * @internal
 * @covers \UpdateNotificationSubscriptionsEndpoint
 */
final class UpdateNotificationSubscriptionsEndpointTest extends TestCase {
    public function testUpdateNotificationSubscriptionsEndpointIdent(): void {
        $endpoint = new UpdateNotificationSubscriptionsEndpoint();
        $this->assertSame('UpdateNotificationSubscriptionsEndpoint', $endpoint->getIdent());
    }

    public function testUpdateNotificationSubscriptionsEndpoint(): void {
        $entity_manager = new FakeNotificationSubscriptionsEndpointEntityManager();
        $notification_subscription_repo = new FakeNotificationSubscriptionsEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories['NotificationSubscription'] = $notification_subscription_repo;
        $user_repo = new FakeNotificationSubscriptionsEndpointUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = new Logger('UpdateNotificationSubscriptionsEndpointTest');
        $endpoint = new UpdateNotificationSubscriptionsEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setDateUtils($date_utils);
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'admin',
        ];
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'deliveryType' => NotificationSubscription::DELIVERY_EMAIL,
            'monthlyPreview' => true,
            'weeklyPreview' => true,
            'deadlineWarning' => true,
            'deadlineWarningDays' => '3',
            'dailySummary' => true,
            'dailySummaryAktuell' => true,
            'dailySummaryBlog' => true,
            'dailySummaryForum' => true,
            'dailySummaryGalerie' => true,
            'weeklySummary' => true,
            'weeklySummaryAktuell' => true,
            'weeklySummaryBlog' => true,
            'weeklySummaryForum' => true,
            'weeklySummaryGalerie' => true,
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(5, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->removed));
        $this->assertSame(123, $entity_manager->removed[0]->getId());
        $this->assertSame(5, count($entity_manager->flushed_persisted));
        $this->assertSame(
            NotificationSubscription::TYPE_DAILY_SUMMARY,
            $entity_manager->flushed_persisted[0]->getNotificationType()
        );
        $this->assertSame(
            json_encode(['aktuell' => true, 'blog' => true, 'forum' => true, 'galerie' => true]),
            $entity_manager->flushed_persisted[0]->getNotificationTypeArgs()
        );
        $this->assertSame(
            NotificationSubscription::TYPE_DEADLINE_WARNING,
            $entity_manager->flushed_persisted[1]->getNotificationType()
        );
        $this->assertSame(
            json_encode(['days' => 3]),
            $entity_manager->flushed_persisted[1]->getNotificationTypeArgs()
        );
        $this->assertSame(
            NotificationSubscription::TYPE_MONTHLY_PREVIEW,
            $entity_manager->flushed_persisted[2]->getNotificationType()
        );
        $this->assertSame(
            json_encode([]),
            $entity_manager->flushed_persisted[2]->getNotificationTypeArgs()
        );
        $this->assertSame(
            NotificationSubscription::TYPE_WEEKLY_PREVIEW,
            $entity_manager->flushed_persisted[3]->getNotificationType()
        );
        $this->assertSame(
            json_encode([]),
            $entity_manager->flushed_persisted[3]->getNotificationTypeArgs()
        );
        $this->assertSame(
            NotificationSubscription::TYPE_WEEKLY_SUMMARY,
            $entity_manager->flushed_persisted[4]->getNotificationType()
        );
        $this->assertSame(
            json_encode(['aktuell' => true, 'blog' => true, 'forum' => true, 'galerie' => true]),
            $entity_manager->flushed_persisted[4]->getNotificationTypeArgs()
        );
        $this->assertSame(1, count($entity_manager->flushed_removed));
        $this->assertSame(123, $entity_manager->flushed_removed[0]->getId());
    }
}
