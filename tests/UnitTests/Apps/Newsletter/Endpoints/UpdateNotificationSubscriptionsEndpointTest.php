<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Newsletter\Endpoints;

use Olz\Apps\Newsletter\Endpoints\UpdateNotificationSubscriptionsEndpoint;
use Olz\Constants\NotificationDeliveryType;
use Olz\Constants\NotificationType;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Apps\Newsletter\Endpoints\UpdateNotificationSubscriptionsEndpoint
 */
final class UpdateNotificationSubscriptionsEndpointTest extends UnitTestCase {
    public function testUpdateNotificationSubscriptionsEndpointEmail(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $entity_manager = WithUtilsCache::get('entityManager');
        $endpoint = new UpdateNotificationSubscriptionsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'deliveryType' => NotificationDeliveryType::EMAIL->value,
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

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            [
                NotificationType::DAILY_SUMMARY,
                json_encode([
                    'aktuell' => true,
                    'blog' => true,
                    'forum' => true,
                    'galerie' => true,
                    'termine' => true,
                ]),
            ],
            [
                NotificationType::DEADLINE_WARNING,
                json_encode(['days' => 3]),
            ],
            [
                NotificationType::MONTHLY_PREVIEW,
                json_encode([]),
            ],
            [
                NotificationType::WEEKLY_PREVIEW,
                json_encode([]),
            ],
            [
                NotificationType::WEEKLY_SUMMARY,
                json_encode([
                    'aktuell' => true,
                    'blog' => true,
                    'forum' => true,
                    'galerie' => true,
                    'termine' => true,
                ]),
            ],
            [
                NotificationType::EMAIL_CONFIG_REMINDER,
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
        $this->assertSame(
            [2, 12, 13, 16],
            array_map(
                fn ($item) => $item->getId(),
                $entity_manager->removed
            ),
        );
        $this->assertSame(
            $entity_manager->removed,
            $entity_manager->flushed_removed
        );
    }

    public function testUpdateNotificationSubscriptionsEndpointTelegram(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $entity_manager = WithUtilsCache::get('entityManager');
        $endpoint = new UpdateNotificationSubscriptionsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'deliveryType' => NotificationDeliveryType::TELEGRAM->value,
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

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            [
                NotificationType::TELEGRAM_CONFIG_REMINDER,
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
        $this->assertSame(
            [6, 14, 18, 19],
            array_map(
                fn ($item) => $item->getId(),
                $entity_manager->removed
            ),
        );
        $this->assertSame(
            $entity_manager->removed,
            $entity_manager->flushed_removed
        );
    }
}
