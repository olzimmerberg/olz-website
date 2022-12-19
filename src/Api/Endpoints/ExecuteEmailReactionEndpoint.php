<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;

class ExecuteEmailReactionEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'ExecuteEmailReactionEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'INVALID_TOKEN',
                'OK',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'token' => new FieldTypes\StringField([]),
        ]]);
    }

    protected function handle($input) {
        $token = $input['token'];
        $reaction_data = $this->emailUtils()->decryptEmailReactionToken($token);

        if (!$reaction_data) {
            $this->log()->error("Invalid email reaction token: {$token}", [$reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }

        $action = $reaction_data['action'] ?? null;
        switch ($action) {
            case 'unsubscribe':
                $user = intval($reaction_data['user'] ?? '0');
                if ($user <= 0) {
                    $this->log()->error("Invalid user {$user} to unsubscribe from email notifications.", [$reaction_data]);
                    return ['status' => 'INVALID_TOKEN'];
                }
                $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
                if (isset($reaction_data['notification_type'])) {
                    $notification_type = $reaction_data['notification_type'];
                    $subscriptions = $notification_subscription_repo->findBy([
                        'delivery_type' => NotificationSubscription::DELIVERY_EMAIL,
                        'notification_type' => $notification_type,
                        'user' => $user,
                    ]);
                    $num_subscriptions = count($subscriptions);
                    if ($num_subscriptions > 1) {
                        $this->log()->warning("This is odd: Multiple email notification subscriptions will be deleted for just one notification type: {$notification_type}.", [$reaction_data, $subscriptions]);
                    }
                    foreach ($subscriptions as $subscription) {
                        $this->log()->notice("Removing email subscription: {$subscription}.");
                        $this->removeNotificationSubscription($subscription);
                    }
                    $this->entityManager()->flush();
                    $this->log()->notice("{$num_subscriptions} email notification subscriptions removed.", [$reaction_data]);
                    return ['status' => 'OK'];
                }
                if (isset($reaction_data['notification_type_all'])) {
                    $subscriptions = $notification_subscription_repo->findBy([
                        'delivery_type' => NotificationSubscription::DELIVERY_EMAIL,
                        'user' => $user,
                    ]);
                    $num_subscriptions = count($subscriptions);
                    foreach ($subscriptions as $subscription) {
                        $this->log()->notice("Removing email subscription: {$subscription}.", [$reaction_data]);
                        $this->removeNotificationSubscription($subscription);
                    }
                    $this->entityManager()->flush();
                    $this->log()->notice("{$num_subscriptions} email notification subscriptions removed.", [$reaction_data]);
                    return ['status' => 'OK'];
                }
                $this->log()->error("Invalid email notification type to unsubscribe from.", [$reaction_data]);
                return ['status' => 'INVALID_TOKEN'];
            case 'reset_password':
                $user_id = intval($reaction_data['user'] ?? '0');
                $user_repo = $this->entityManager()->getRepository(User::class);
                $user = $user_repo->findOneBy(['id' => $user_id]);
                if (!$user) {
                    $this->log()->error("Invalid user {$user_id} to reset password.", [$reaction_data]);
                    return ['status' => 'INVALID_TOKEN'];
                }
                $new_password = $reaction_data['new_password'];
                if (strlen($new_password) < 8) {
                    $this->log()->error("New password is too short.", [$reaction_data]);
                    return ['status' => 'INVALID_TOKEN'];
                }
                $user->setPasswordHash(password_hash($new_password, PASSWORD_DEFAULT));
                $this->entityManager()->flush();
                return ['status' => 'OK'];
            case 'verify_email':
                $user_id = intval($reaction_data['user'] ?? '0');
                $user_repo = $this->entityManager()->getRepository(User::class);
                $user = $user_repo->findOneBy(['id' => $user_id]);
                if (!$user) {
                    $this->log()->error("Invalid user {$user_id} to verify email.", [$reaction_data]);
                    return ['status' => 'INVALID_TOKEN'];
                }
                $verify_email = $reaction_data['email'] ?? '';
                $user_email = $user->getEmail();
                if ($verify_email !== $user_email) {
                    $this->log()->error("Trying to verify email ({$verify_email}) for user {$user_id} (email: {$user_email}).", [$reaction_data]);
                    return ['status' => 'INVALID_TOKEN'];
                }
                $verify_token = $reaction_data['token'];
                $user_token = $user->getEmailVerificationToken();
                if ($verify_token !== $user_token) {
                    $this->log()->error("Invalid email verification token {$verify_token} for user {$user_id} (token: {$user_token}).", [$reaction_data]);
                    return ['status' => 'INVALID_TOKEN'];
                }
                $user->setEmailIsVerified(true);
                $this->entityManager()->flush();
                return ['status' => 'OK'];
            default:
                $this->log()->error("Unknown email reaction action: {$action}.", [$reaction_data]);
                return ['status' => 'INVALID_TOKEN'];
        }
    }

    protected function removeNotificationSubscription($subscription) {
        // If it is an email config reminder subscription, just mark it cancelled.
        if ($subscription->getNotificationType() === NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER) {
            $subscription->setNotificationTypeArgs(json_encode(['cancelled' => true]));
        } else {
            $this->entityManager()->remove($subscription);
        }
    }
}
