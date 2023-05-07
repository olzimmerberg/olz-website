<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\ExecuteEmailReactionEndpoint;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class FakeExecuteEmailReactionEndpointNotificationSubscriptionRepository {
    public $subscriptions_to_find;

    public function findBy($query) {
        $user = new User();
        if ($this->subscriptions_to_find) {
            return $this->subscriptions_to_find;
        }
        $subscription_1 = new NotificationSubscription();
        $subscription_1->setId(1);
        $subscription_1->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
        $subscription_1->setUser($user);
        $subscription_1->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
        $subscription_1->setNotificationTypeArgs('{}');
        $subscription_2 = new NotificationSubscription();
        $subscription_2->setId(2);
        $subscription_2->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
        $subscription_2->setUser($user);
        $subscription_2->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
        $subscription_2->setNotificationTypeArgs('{}');
        return [$subscription_1, $subscription_2];
    }
}

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
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $notification_subscription_repo = new FakeExecuteEmailReactionEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories[NotificationSubscription::class] = $notification_subscription_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type' => NotificationSubscription::TYPE_DAILY_SUMMARY,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING This is odd: Multiple email notification subscriptions will be deleted for just one notification type: daily_summary.",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=, notification_type=daily_summary, notification_type_args={}, ).",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=, notification_type=weekly_summary, notification_type_args={}, ).",
            "NOTICE 2 email notification subscriptions removed.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(2, count($entity_manager->removed));
        $this->assertSame(1, $entity_manager->removed[0]->getId());
        $this->assertSame(2, $entity_manager->removed[1]->getId());
        $this->assertSame(2, count($entity_manager->flushed_removed));
        $this->assertSame(1, $entity_manager->flushed_removed[0]->getId());
        $this->assertSame(2, $entity_manager->flushed_removed[1]->getId());
    }

    public function testCancelEmailConfigReminderEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $notification_subscription_repo = new FakeExecuteEmailReactionEndpointNotificationSubscriptionRepository();
        $user = new User();
        $subscription = new NotificationSubscription();
        $subscription->setId(3);
        $subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
        $subscription->setUser($user);
        $subscription->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
        $subscription->setNotificationTypeArgs('{"cancelled":false}');
        $notification_subscription_repo->subscriptions_to_find = [$subscription];
        $entity_manager->repositories[NotificationSubscription::class] = $notification_subscription_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=, notification_type=email_config_reminder, notification_type_args={\"cancelled\":false}, ).",
            "NOTICE 1 email notification subscriptions removed.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
        $this->assertSame('{"cancelled":true}', $subscription->getNotificationTypeArgs());
    }

    public function testUnsubscribeFromAllNotificationsEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $notification_subscription_repo = new FakeExecuteEmailReactionEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories[NotificationSubscription::class] = $notification_subscription_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type_all' => 1,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=, notification_type=daily_summary, notification_type_args={}, ).",
            "NOTICE Removing email subscription: NotificationSubscription(delivery_type=email, user=, notification_type=weekly_summary, notification_type_args={}, ).",
            "NOTICE 2 email notification subscriptions removed.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(2, count($entity_manager->removed));
        $this->assertSame(1, $entity_manager->removed[0]->getId());
        $this->assertSame(2, $entity_manager->removed[1]->getId());
        $this->assertSame(2, count($entity_manager->flushed_removed));
        $this->assertSame(1, $entity_manager->flushed_removed[0]->getId());
        $this->assertSame(2, $entity_manager->flushed_removed[1]->getId());
    }

    public function testUnsubscribeButNotUserGivenEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'notification_type_all' => 1,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid user 0 to unsubscribe from email notifications.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }

    public function testUnsubscribeButNoNotificationTypeGivenEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $notification_subscription_repo = new FakeExecuteEmailReactionEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories[NotificationSubscription::class] = $notification_subscription_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid email notification type to unsubscribe from.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }

    public function testInvalidActionEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'invalid',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Unknown email reaction action: invalid.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }

    public function testInvalidTokenEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode('')]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid email reaction token: \"\"",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }

    public function testResetPasswordEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 2,
            'new_password' => 'geeenius',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
        $this->assertSame(0, count($entity_manager->persisted));
        $this->assertSame(0, count($entity_manager->flushed_persisted));
        $this->assertTrue(password_verify('geeenius', $user_repo->admin_user->getPasswordHash()));
    }

    public function testResetPasswordNoSuchUserEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 9999, // inexistent
            'new_password' => 'geeenius',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid user 9999 to reset password.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
    }

    public function testResetButInvalidPasswordEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 2,
            'new_password' => 'genius',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR New password is too short.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
    }

    public function testVerifyEmailReactionEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'verify_email',
            'user' => 1,
            'email' => 'default-user@olzimmerberg.ch',
            'token' => 'defaulttoken',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(true, $entity_manager->flushed);
        $this->assertSame(true, $user_repo->default_user->isEmailVerified());
    }

    public function testVerifyEmailReactionEndpointInvalidToken(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'verify_email',
            'user' => 1,
            'email' => 'default-user@olzimmerberg.ch',
            'token' => 'invalid',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid email verification token invalid for user 1 (token: defaulttoken).",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(false, $entity_manager->flushed);
        $this->assertSame(false, $user_repo->default_user->isEmailVerified());
    }

    public function testVerifyEmailReactionEndpointEmailMismatch(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'verify_email',
            'user' => 1,
            'email' => 'another@email.ch',
            'token' => 'defaulttoken',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Trying to verify email (another@email.ch) for user 1 (email: default-user@olzimmerberg.ch).",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(false, $entity_manager->flushed);
        $this->assertSame(false, $user_repo->default_user->isEmailVerified());
    }

    public function testVerifyEmailReactionEndpointNoSuchUser(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'verify_email',
            'user' => 9999,
            'email' => 'default-user@olzimmerberg.ch',
            'token' => 'defaulttoken',
        ])]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Invalid user 9999 to verify email.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(false, $entity_manager->flushed);
    }
}
