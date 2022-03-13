<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/api/endpoints/UpdateNotificationSubscriptionsEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/NotificationSubscription.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

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

/**
 * @internal
 * @covers \UpdateNotificationSubscriptionsEndpoint
 */
final class UpdateNotificationSubscriptionsEndpointTest extends UnitTestCase {
    public function testUpdateNotificationSubscriptionsEndpointIdent(): void {
        $endpoint = new UpdateNotificationSubscriptionsEndpoint();
        $this->assertSame('UpdateNotificationSubscriptionsEndpoint', $endpoint->getIdent());
    }

    public function testUpdateNotificationSubscriptionsEndpointEmail(): void {
        $entity_manager = new FakeEntityManager();
        $notification_subscription_repo = new FakeNotificationSubscriptionsEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories['NotificationSubscription'] = $notification_subscription_repo;
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
            'dailySummaryTermine' => true,
            'weeklySummary' => true,
            'weeklySummaryAktuell' => true,
            'weeklySummaryBlog' => true,
            'weeklySummaryForum' => true,
            'weeklySummaryGalerie' => true,
            'weeklySummaryTermine' => true,
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            [
                NotificationSubscription::TYPE_DAILY_SUMMARY,
                json_encode([
                    'aktuell' => true,
                    'blog' => true,
                    'forum' => true,
                    'galerie' => true,
                    'termine' => true,
                ]),
            ],
            [
                NotificationSubscription::TYPE_DEADLINE_WARNING,
                json_encode(['days' => 3]),
            ],
            [
                NotificationSubscription::TYPE_MONTHLY_PREVIEW,
                json_encode([]),
            ],
            [
                NotificationSubscription::TYPE_WEEKLY_PREVIEW,
                json_encode([]),
            ],
            [
                NotificationSubscription::TYPE_WEEKLY_SUMMARY,
                json_encode([
                    'aktuell' => true,
                    'blog' => true,
                    'forum' => true,
                    'galerie' => true,
                    'termine' => true,
                ]),
            ],
            [
                NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                json_encode(['cancelled' => true]),
            ],
        ], array_map(function ($notification_subscription) {
            return [
                $notification_subscription->getNotificationType(),
                $notification_subscription->getNotificationTypeArgs(),
            ];
        }, $entity_manager->persisted));
        $this->assertSame(
            $entity_manager->persisted,
            $entity_manager->flushed_persisted
        );
        $this->assertSame(1, count($entity_manager->removed));
        $this->assertSame(123, $entity_manager->removed[0]->getId());
        $this->assertSame(
            $entity_manager->removed,
            $entity_manager->flushed_removed
        );
    }

    public function testUpdateNotificationSubscriptionsEndpointTelegram(): void {
        $entity_manager = new FakeEntityManager();
        $notification_subscription_repo = new FakeNotificationSubscriptionsEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories['NotificationSubscription'] = $notification_subscription_repo;
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
            'deliveryType' => NotificationSubscription::DELIVERY_TELEGRAM,
            'monthlyPreview' => false,
            'weeklyPreview' => false,
            'deadlineWarning' => false,
            'deadlineWarningDays' => '3',
            'dailySummary' => false,
            'dailySummaryAktuell' => false,
            'dailySummaryBlog' => false,
            'dailySummaryForum' => false,
            'dailySummaryGalerie' => false,
            'dailySummaryTermine' => false,
            'weeklySummary' => false,
            'weeklySummaryAktuell' => false,
            'weeklySummaryBlog' => false,
            'weeklySummaryForum' => false,
            'weeklySummaryGalerie' => false,
            'weeklySummaryTermine' => false,
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            [
                NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
                json_encode(['cancelled' => true]),
            ],
        ], array_map(function ($notification_subscription) {
            return [
                $notification_subscription->getNotificationType(),
                $notification_subscription->getNotificationTypeArgs(),
            ];
        }, $entity_manager->persisted));
        $this->assertSame(
            $entity_manager->persisted,
            $entity_manager->flushed_persisted
        );
        $this->assertSame(1, count($entity_manager->removed));
        $this->assertSame(123, $entity_manager->removed[0]->getId());
        $this->assertSame(
            $entity_manager->removed,
            $entity_manager->flushed_removed
        );
    }
}
