<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/BooleanField.php';
require_once __DIR__.'/../../model/NotificationSubscription.php';
require_once __DIR__.'/../../model/User.php';

class UpdateNotificationSubscriptionsEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public static function getIdent() {
        return 'UpdateNotificationSubscriptionsEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            'deliveryType' => new EnumField(['allowed_values' => [
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::DELIVERY_TELEGRAM,
            ]]),
            'monthlyPreview' => new BooleanField([]),
            'weeklyPreview' => new BooleanField([]),
            'deadlineWarning' => new BooleanField([]),
            'deadlineWarningDays' => new EnumField(['allowed_values' => [
                '1', '2', '3', '7',
            ]]),
            'dailySummary' => new BooleanField([]),
            'dailySummaryAktuell' => new BooleanField([]),
            'dailySummaryBlog' => new BooleanField([]),
            'dailySummaryForum' => new BooleanField([]),
            'dailySummaryGalerie' => new BooleanField([]),
            'weeklySummary' => new BooleanField([]),
            'weeklySummaryAktuell' => new BooleanField([]),
            'weeklySummaryBlog' => new BooleanField([]),
            'weeklySummaryForum' => new BooleanField([]),
            'weeklySummaryGalerie' => new BooleanField([]),
        ];
    }

    protected function handle($input) {
        $auth_username = $this->session->get('user');
        $now_datetime = new DateTime($this->dateUtils->getIsoNow());

        $delivery_type = $input['deliveryType'];
        $has_monthly_preview = $input['monthlyPreview'];
        $has_weekly_preview = $input['weeklyPreview'];
        $has_deadline_warning = $input['deadlineWarning'];
        $deadline_warning_days = $input['deadlineWarningDays'];
        $has_daily_summary = $input['dailySummary'];
        $daily_summary_aktuell = $input['dailySummaryAktuell'];
        $daily_summary_blog = $input['dailySummaryBlog'];
        $daily_summary_forum = $input['dailySummaryForum'];
        $daily_summary_galerie = $input['dailySummaryGalerie'];
        $has_weekly_summary = $input['weeklySummary'];
        $weekly_summary_aktuell = $input['weeklySummaryAktuell'];
        $weekly_summary_blog = $input['weeklySummaryBlog'];
        $weekly_summary_forum = $input['weeklySummaryForum'];
        $weekly_summary_galerie = $input['weeklySummaryGalerie'];

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $auth_username]);
        $notification_subscription_repo = $this->entityManager->getRepository(NotificationSubscription::class);

        $existing_subscriptions = $notification_subscription_repo->findBy([
            'user' => $user,
            'delivery_type' => $delivery_type,
        ]);
        foreach ($existing_subscriptions as $subscription) {
            $this->entityManager->remove($subscription);
        }

        // TYPE_DAILY_SUMMARY
        if ($has_daily_summary) {
            $args = [];
            if ($daily_summary_aktuell) {
                $args['aktuell'] = true;
            }
            if ($daily_summary_blog) {
                $args['blog'] = true;
            }
            if ($daily_summary_forum) {
                $args['forum'] = true;
            }
            if ($daily_summary_galerie) {
                $args['galerie'] = true;
            }
            $subscription = new NotificationSubscription();
            $subscription->setDeliveryType($delivery_type);
            $subscription->setUser($user);
            $subscription->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
            $subscription->setNotificationTypeArgs(json_encode($args));
            $subscription->setCreatedAt($now_datetime);
            $this->entityManager->persist($subscription);
        }

        // TYPE_DEADLINE_WARNING
        if ($has_deadline_warning) {
            $args = [
                'days' => intval($deadline_warning_days),
            ];
            $subscription = new NotificationSubscription();
            $subscription->setDeliveryType($delivery_type);
            $subscription->setUser($user);
            $subscription->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
            $subscription->setNotificationTypeArgs(json_encode($args));
            $subscription->setCreatedAt($now_datetime);
            $this->entityManager->persist($subscription);
        }

        // TYPE_MONTHLY_PREVIEW
        if ($has_monthly_preview) {
            $args = [];
            $subscription = new NotificationSubscription();
            $subscription->setDeliveryType($delivery_type);
            $subscription->setUser($user);
            $subscription->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
            $subscription->setNotificationTypeArgs(json_encode($args));
            $subscription->setCreatedAt($now_datetime);
            $this->entityManager->persist($subscription);
        }

        // TYPE_WEEKLY_PREVIEW
        if ($has_weekly_preview) {
            $args = [];
            $subscription = new NotificationSubscription();
            $subscription->setDeliveryType($delivery_type);
            $subscription->setUser($user);
            $subscription->setNotificationType(NotificationSubscription::TYPE_WEEKLY_PREVIEW);
            $subscription->setNotificationTypeArgs(json_encode($args));
            $subscription->setCreatedAt($now_datetime);
            $this->entityManager->persist($subscription);
        }

        // TYPE_WEEKLY_SUMMARY
        if ($has_weekly_summary) {
            $args = [];
            if ($weekly_summary_aktuell) {
                $args['aktuell'] = true;
            }
            if ($weekly_summary_blog) {
                $args['blog'] = true;
            }
            if ($weekly_summary_forum) {
                $args['forum'] = true;
            }
            if ($weekly_summary_galerie) {
                $args['galerie'] = true;
            }
            $subscription = new NotificationSubscription();
            $subscription->setDeliveryType($delivery_type);
            $subscription->setUser($user);
            $subscription->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
            $subscription->setNotificationTypeArgs(json_encode($args));
            $subscription->setCreatedAt($now_datetime);
            $this->entityManager->persist($subscription);
        }

        $this->entityManager->flush();

        return ['status' => 'OK'];
    }
}
