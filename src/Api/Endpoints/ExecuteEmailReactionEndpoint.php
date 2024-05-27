<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;

class ExecuteEmailReactionEndpoint extends OlzEndpoint {
    protected $reaction_data;

    public static function getIdent(): string {
        return 'ExecuteEmailReactionEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'INVALID_TOKEN',
                'OK',
            ]]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'token' => new FieldTypes\StringField([]),
        ]]);
    }

    protected function handle($input) {
        $token = $input['token'];
        $this->reaction_data = $this->emailUtils()->decryptEmailReactionToken($token);

        if (!$this->reaction_data) {
            $this->log()->error("Invalid email reaction token: {$token}", [$this->reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }

        $action = $this->reaction_data['action'] ?? null;
        switch ($action) {
            case 'unsubscribe':
                return $this->actionUnsubscribe();
            case 'reset_password':
                return $this->actionResetPassword();
            case 'verify_email':
                return $this->actionVerifyEmail();
            case 'delete_news':
                return $this->actionDeleteNews();
            default:
                $this->log()->error("Unknown email reaction action: {$action}.", [$this->reaction_data]);
                return ['status' => 'INVALID_TOKEN'];
        }
    }

    protected function actionUnsubscribe() {
        $user = intval($this->reaction_data['user'] ?? '0');
        if ($user <= 0) {
            $this->log()->error("Invalid user {$user} to unsubscribe from email notifications.", [$this->reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        if (isset($this->reaction_data['notification_type'])) {
            $notification_type = $this->reaction_data['notification_type'];
            $subscriptions = $notification_subscription_repo->findBy([
                'delivery_type' => NotificationSubscription::DELIVERY_EMAIL,
                'notification_type' => $notification_type,
                'user' => $user,
            ]);
            $num_subscriptions = count($subscriptions);
            if ($num_subscriptions > 1) {
                $this->log()->warning("This is odd: Multiple email notification subscriptions will be deleted for just one notification type: {$notification_type}.", [$this->reaction_data, $subscriptions]);
            }
            foreach ($subscriptions as $subscription) {
                $this->log()->notice("Removing email subscription: {$subscription}.");
                $this->removeNotificationSubscription($subscription);
            }
            $this->entityManager()->flush();
            $this->log()->notice("{$num_subscriptions} email notification subscriptions removed.", [$this->reaction_data]);
            return ['status' => 'OK'];
        }
        if (isset($this->reaction_data['notification_type_all'])) {
            $subscriptions = $notification_subscription_repo->findBy([
                'delivery_type' => NotificationSubscription::DELIVERY_EMAIL,
                'user' => $user,
            ]);
            $num_subscriptions = count($subscriptions);
            foreach ($subscriptions as $subscription) {
                $this->log()->notice("Removing email subscription: {$subscription}.", [$this->reaction_data]);
                $this->removeNotificationSubscription($subscription);
            }
            $this->entityManager()->flush();
            $this->log()->notice("{$num_subscriptions} email notification subscriptions removed.", [$this->reaction_data]);
            return ['status' => 'OK'];
        }
        $this->log()->error("Invalid email notification type to unsubscribe from.", [$this->reaction_data]);
        return ['status' => 'INVALID_TOKEN'];
    }

    protected function removeNotificationSubscription($subscription) {
        // If it is an email config reminder subscription, just mark it cancelled.
        if ($subscription->getNotificationType() === NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER) {
            $subscription->setNotificationTypeArgs(json_encode(['cancelled' => true]));
        } else {
            $this->entityManager()->remove($subscription);
        }
    }

    protected function actionResetPassword() {
        $user_id = intval($this->reaction_data['user'] ?? '0');
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $user_id]);
        if (!$user) {
            $this->log()->error("Invalid user {$user_id} to reset password.", [$this->reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }
        $new_password = $this->reaction_data['new_password'];
        if (strlen($new_password) < 8) {
            $this->log()->error("New password is too short.", [$this->reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }
        $user->setPasswordHash($this->authUtils()->hashPassword($new_password));
        $this->entityManager()->flush();
        return ['status' => 'OK'];
    }

    protected function actionVerifyEmail() {
        $user_id = intval($this->reaction_data['user'] ?? '0');
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $user_id]);
        if (!$user) {
            $this->log()->error("Invalid user {$user_id} to verify email.", [$this->reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }
        $verify_email = $this->reaction_data['email'] ?? '';
        $user_email = $user->getEmail();
        if ($verify_email !== $user_email) {
            $this->log()->error("Trying to verify email ({$verify_email}) for user {$user_id} (email: {$user_email}).", [$this->reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }
        $verify_token = $this->reaction_data['token'];
        $user_token = $user->getEmailVerificationToken();
        if ($verify_token !== $user_token) {
            $this->log()->error("Invalid email verification token {$verify_token} for user {$user_id} (token: {$user_token}).", [$this->reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }
        $user->setEmailIsVerified(true);
        $user->setEmailVerificationToken(null);
        $user->addPermission('verified_email');
        $this->entityManager()->flush();
        return ['status' => 'OK'];
    }

    protected function actionDeleteNews() {
        $news_id = $this->reaction_data['news_id'] ?? null;
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $news_id]);
        if (!$news_id || !$news_entry) {
            $this->log()->error("Trying to delete inexistent news entry: {$news_id}.", [$this->reaction_data]);
            return ['status' => 'INVALID_TOKEN'];
        }
        $news_entry->setOnOff(false);
        $this->entityManager()->flush();
        return ['status' => 'OK'];
    }
}
