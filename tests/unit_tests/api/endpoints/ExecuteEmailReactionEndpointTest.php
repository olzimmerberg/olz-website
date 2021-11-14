<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/api/endpoints/ExecuteEmailReactionEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/NotificationSubscription.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../fake/FakeEmailUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeUserRepository.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

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
 * @covers \ExecuteEmailReactionEndpoint
 */
final class ExecuteEmailReactionEndpointTest extends UnitTestCase {
    public function testExecuteEmailReactionEndpointIdent(): void {
        $endpoint = new ExecuteEmailReactionEndpoint();
        $this->assertSame('ExecuteEmailReactionEndpoint', $endpoint->getIdent());
    }

    public function testUnsubscribeFromNotificationEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $notification_subscription_repo = new FakeExecuteEmailReactionEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories['NotificationSubscription'] = $notification_subscription_repo;
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type' => NotificationSubscription::TYPE_DAILY_SUMMARY,
        ])]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(2, count($entity_manager->removed));
        $this->assertSame(1, $entity_manager->removed[0]->getId());
        $this->assertSame(2, $entity_manager->removed[1]->getId());
        $this->assertSame(2, count($entity_manager->flushed_removed));
        $this->assertSame(1, $entity_manager->flushed_removed[0]->getId());
        $this->assertSame(2, $entity_manager->flushed_removed[1]->getId());
    }

    public function testCancelEmailConfigReminderEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $notification_subscription_repo = new FakeExecuteEmailReactionEndpointNotificationSubscriptionRepository();
        $user = new User();
        $subscription = new NotificationSubscription();
        $subscription->setId(3);
        $subscription->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
        $subscription->setUser($user);
        $subscription->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
        $subscription->setNotificationTypeArgs('{"cancelled":false}');
        $notification_subscription_repo->subscriptions_to_find = [$subscription];
        $entity_manager->repositories['NotificationSubscription'] = $notification_subscription_repo;
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ])]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
        $this->assertSame('{"cancelled":true}', $subscription->getNotificationTypeArgs());
    }

    public function testUnsubscribeFromAllNotificationsEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $notification_subscription_repo = new FakeExecuteEmailReactionEndpointNotificationSubscriptionRepository();
        $entity_manager->repositories['NotificationSubscription'] = $notification_subscription_repo;
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
            'notification_type_all' => 1,
        ])]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(2, count($entity_manager->removed));
        $this->assertSame(1, $entity_manager->removed[0]->getId());
        $this->assertSame(2, $entity_manager->removed[1]->getId());
        $this->assertSame(2, count($entity_manager->flushed_removed));
        $this->assertSame(1, $entity_manager->flushed_removed[0]->getId());
        $this->assertSame(2, $entity_manager->flushed_removed[1]->getId());
    }

    public function testUnsubscribeButNotUserGivenEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'notification_type_all' => 1,
        ])]);

        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }

    public function testUnsubscribeButNoNotificationTypeGivenEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'unsubscribe',
            'user' => 1,
        ])]);

        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }

    public function testInvalidActionEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'invalid',
        ])]);

        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }

    public function testInvalidTokenEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode('')]);

        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }

    public function testResetPasswordEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 2,
            'new_password' => 'geeenius',
        ])]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
        $this->assertSame(0, count($entity_manager->persisted));
        $this->assertSame(0, count($entity_manager->flushed_persisted));
        $this->assertTrue(password_verify('geeenius', $user_repo->admin_user->getPasswordHash()));
    }

    public function testResetPasswordNoSuchUserEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 9999, // inexistent
            'new_password' => 'geeenius',
        ])]);

        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
    }

    public function testResetButInvalidPasswordEmailReactionEndpoint(): void {
        $logger = new Logger('ExecuteEmailReactionEndpointTest');
        $endpoint = new ExecuteEmailReactionEndpoint();
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call(['token' => json_encode([
            'action' => 'reset_password',
            'user' => 2,
            'new_password' => 'genius',
        ])]);

        $this->assertSame(['status' => 'INVALID_TOKEN'], $result);
    }
}
