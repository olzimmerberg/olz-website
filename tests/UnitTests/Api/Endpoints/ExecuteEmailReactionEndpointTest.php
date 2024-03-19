<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\ExecuteEmailReactionEndpoint;
use Olz\Entity\NotificationSubscription;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\ExecuteEmailReactionEndpoint
 */
final class ExecuteEmailReactionEndpointTest extends UnitTestCase {
    public function testExecuteEmailReactionEndpointIdent(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $this->assertSame('ExecuteEmailReactionEndpoint', $endpoint->getIdent());
    }

    public function testUnsubscribeFromNotificationEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type' => NotificationSubscription::TYPE_DAILY_SUMMARY,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING This is odd: Multiple email notification subscriptions will be deleted for just one notification type: daily_summary.",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=1, notification_type=daily_summary, notification_type_args={}, ).",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=1, notification_type=weekly_summary, notification_type_args={}, ).",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=1, notification_type=monthly_preview, notification_type_args={}, ).",
            "NOTICE 3 email notification subscriptions removed.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(3, count($entity_manager->removed));
        $this->assertSame(12, $entity_manager->removed[0]->getId());
        $this->assertSame(123, $entity_manager->removed[1]->getId());
        $this->assertSame(1234, $entity_manager->removed[2]->getId());
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    public function testCancelEmailConfigReminderEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();
        $subscription = new NotificationSubscription();
        $subscription->setId(3);
        $subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
        $subscription->setUser(FakeUser::defaultUser());
        $subscription->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
        $subscription->setNotificationTypeArgs('{"cancelled":false}');
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[NotificationSubscription::class]->entitiesToBeFound = [$subscription];

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=1, notification_type=email_config_reminder, notification_type_args={\"cancelled\":false}, ).",
            "NOTICE 1 email notification subscriptions removed.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
        $this->assertSame('{"cancelled":true}', $subscription->getNotificationTypeArgs());
    }

    public function testUnsubscribeFromAllNotificationsEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type_all' => 1,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=1, notification_type=daily_summary, notification_type_args={}, ).",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=1, notification_type=weekly_summary, notification_type_args={}, ).",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=1, notification_type=monthly_preview, notification_type_args={}, ).",
            "NOTICE 3 email notification subscriptions removed.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(3, count($entity_manager->removed));
        $this->assertSame(12, $entity_manager->removed[0]->getId());
        $this->assertSame(123, $entity_manager->removed[1]->getId());
        $this->assertSame(1234, $entity_manager->removed[2]->getId());
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    public function testUnsubscribeButNotUserGivenEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'notification_type_all' => 1,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid user 0 to unsubscribe from email notifications.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    public function testUnsubscribeButNoNotificationTypeGivenEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid email notification type to unsubscribe from.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    public function testInvalidActionEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'invalid',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Unknown email reaction action: invalid.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    public function testInvalidTokenEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode('')]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid email reaction token: \"\"",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    public function testResetPasswordEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 2,
            'new_password' => 'geeenius',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
        $this->assertSame(0, count($entity_manager->persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $this->assertSame(md5('geeenius'), FakeUser::adminUser()->getPasswordHash()); // just for test
    }

    public function testResetPasswordNoSuchUserEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 9999, // inexistent
            'new_password' => 'geeenius',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid user 9999 to reset password.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
    }

    public function testResetButInvalidPasswordEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 2,
            'new_password' => 'genius',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR New password is too short.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
    }

    public function testVerifyEmailReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'verify_email',
            'user' => 1,
            'email' => 'default-user@staging.olzimmerberg.ch',
            'token' => 'defaulttoken',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(true, $entity_manager->flushed);
        $this->assertSame(true, FakeUser::defaultUser()->isEmailVerified());
    }

    public function testVerifyEmailReactionEndpointInvalidToken(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'verify_email',
            'user' => 1,
            'email' => 'default-user@staging.olzimmerberg.ch',
            'token' => 'invalid',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid email verification token invalid for user 1 (token: defaulttoken).",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(false, $entity_manager->flushed);
        $this->assertSame(false, FakeUser::defaultUser()->isEmailVerified());
    }

    public function testVerifyEmailReactionEndpointEmailMismatch(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'verify_email',
            'user' => 1,
            'email' => 'another@email.ch',
            'token' => 'defaulttoken',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Trying to verify email (another@email.ch) for user 1 (email: default-user@staging.olzimmerberg.ch).",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(false, $entity_manager->flushed);
        $this->assertSame(false, FakeUser::defaultUser()->isEmailVerified());
    }

    public function testVerifyEmailReactionEndpointNoSuchUser(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'verify_email',
            'user' => 9999,
            'email' => 'default-user@staging.olzimmerberg.ch',
            'token' => 'defaulttoken',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid user 9999 to verify email.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(false, $entity_manager->flushed);
    }

    public function testDeleteNewsReactionEndpoint(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'delete_news',
            'news_id' => 12,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(true, $entity_manager->flushed);
    }

    public function testDeleteNewsReactionEndpointNoSuchEntry(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'delete_news',
            'news_id' => 9999,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Trying to delete inexistent news entry: 9999.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(false, $entity_manager->flushed);
    }
}
