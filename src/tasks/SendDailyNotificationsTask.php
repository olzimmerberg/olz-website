<?php

require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../model/NotificationSubscription.php';
require_once __DIR__.'/../model/TelegramLink.php';
require_once __DIR__.'/common/BackgroundTask.php';
require_once __DIR__.'/SendDailyNotificationsTask/DailySummaryGetter.php';
require_once __DIR__.'/SendDailyNotificationsTask/DeadlineWarningGetter.php';
require_once __DIR__.'/SendDailyNotificationsTask/MonthlyPreviewGetter.php';
require_once __DIR__.'/SendDailyNotificationsTask/WeeklyPreviewGetter.php';
require_once __DIR__.'/SendDailyNotificationsTask/WeeklySummaryGetter.php';

class SendDailyNotificationsTask extends BackgroundTask {
    public function __construct($entityManager, $emailUtils, $telegramUtils, $dateUtils, $envUtils) {
        parent::__construct($dateUtils, $envUtils);
        $this->entityManager = $entityManager;
        $this->emailUtils = $emailUtils;
        $this->telegramUtils = $telegramUtils;
        $this->setDailySummaryGetter(new DailySummaryGetter());
        $this->setDeadlineWarningGetter(new DeadlineWarningGetter());
        $this->setMonthlyPreviewGetter(new MonthlyPreviewGetter());
        $this->setWeeklyPreviewGetter(new WeeklyPreviewGetter());
        $this->setWeeklySummaryGetter(new WeeklySummaryGetter());
    }

    public function setDailySummaryGetter($dailySummaryGetter) {
        $this->dailySummaryGetter = $dailySummaryGetter;
    }

    public function setDeadlineWarningGetter($deadlineWarningGetter) {
        $this->deadlineWarningGetter = $deadlineWarningGetter;
    }

    public function setMonthlyPreviewGetter($monthlyPreviewGetter) {
        $this->monthlyPreviewGetter = $monthlyPreviewGetter;
    }

    public function setWeeklyPreviewGetter($weeklyPreviewGetter) {
        $this->weeklyPreviewGetter = $weeklyPreviewGetter;
    }

    public function setWeeklySummaryGetter($weeklySummaryGetter) {
        $this->weeklySummaryGetter = $weeklySummaryGetter;
    }

    protected static function getIdent() {
        return "SendDailyNotifications";
    }

    protected function runSpecificTask() {
        $subscriptions_by_type_and_args = $this->getNotificationSubscriptions();
        foreach ($subscriptions_by_type_and_args as $notification_type => $subscriptions_by_args) {
            $this->logger->info("Sending '{$notification_type}' notifications...");
            switch ($notification_type) {
                case NotificationSubscription::TYPE_DAILY_SUMMARY:
                    $this->sendDailySummaryNotifications($subscriptions_by_args);
                    break;
                case NotificationSubscription::TYPE_DEADLINE_WARNING:
                    $this->sendDeadlineWarningNotifications($subscriptions_by_args);
                    break;
                case NotificationSubscription::TYPE_MONTHLY_PREVIEW:
                    $this->sendMonthlyPreviewNotifications($subscriptions_by_args);
                    break;
                case NotificationSubscription::TYPE_WEEKLY_PREVIEW:
                    $this->sendWeeklyPreviewNotifications($subscriptions_by_args);
                    break;
                case NotificationSubscription::TYPE_WEEKLY_SUMMARY:
                    $this->sendWeeklySummaryNotifications($subscriptions_by_args);
                    break;
                default:
                    $this->logger->critical("Unknown notification type '{$notification_type}'");
                    break;
            }
        }
    }

    private function getNotificationSubscriptions() {
        $daily_notification_types = [
            NotificationSubscription::TYPE_DAILY_SUMMARY,
            NotificationSubscription::TYPE_DEADLINE_WARNING,
            NotificationSubscription::TYPE_MONTHLY_PREVIEW,
            NotificationSubscription::TYPE_WEEKLY_PREVIEW,
            NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        ];
        $notification_subscription_repo = $this->entityManager->getRepository(NotificationSubscription::class);

        $subscriptions = $notification_subscription_repo->findBy(['notification_type' => $daily_notification_types]);

        $subscriptions_by_type_and_args = [];
        foreach ($subscriptions as $subscription) {
            $notification_type = $subscription->getNotificationType();
            $notification_args = $subscription->getNotificationTypeArgs();
            $this->logger->info("Found notification subscription for '{$notification_type}', '{$notification_args}'...");
            $subscriptions_by_args_of_type = $subscriptions_by_type_and_args[$notification_type] ?? [];
            $subscriptions_of_type_and_args = $subscriptions_by_args_of_type[$notification_args] ?? [];
            $subscriptions_of_type_and_args[] = $subscription;
            $subscriptions_by_args_of_type[$notification_args] = $subscriptions_of_type_and_args;
            $subscriptions_by_type_and_args[$notification_type] = $subscriptions_by_args_of_type;
        }
        return $subscriptions_by_type_and_args;
    }

    private function sendDailySummaryNotifications($subscriptions_by_args) {
        $daily_summary_getter = $this->dailySummaryGetter;
        $daily_summary_getter->setEntityManager($this->entityManager);
        $daily_summary_getter->setDateUtils($this->dateUtils);
        $daily_summary_getter->setEnvUtils($this->envUtils);
        $daily_summary_getter->setLogger($this->logger);

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->logger->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $daily_summary_getter->getDailySummaryNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->logger->info("Nothing to send.");
            }
        }
    }

    private function sendDeadlineWarningNotifications($subscriptions_by_args) {
        $deadline_warning_getter = $this->deadlineWarningGetter;
        $deadline_warning_getter->setEntityManager($this->entityManager);
        $deadline_warning_getter->setDateUtils($this->dateUtils);
        $deadline_warning_getter->setEnvUtils($this->envUtils);
        $deadline_warning_getter->setLogger($this->logger);

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->logger->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $deadline_warning_getter->getDeadlineWarningNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->logger->info("Nothing to send.");
            }
        }
    }

    private function sendMonthlyPreviewNotifications($subscriptions_by_args) {
        $monthly_preview_getter = $this->monthlyPreviewGetter;
        $monthly_preview_getter->setEntityManager($this->entityManager);
        $monthly_preview_getter->setDateUtils($this->dateUtils);
        $monthly_preview_getter->setEnvUtils($this->envUtils);
        $monthly_preview_getter->setLogger($this->logger);

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->logger->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $monthly_preview_getter->getMonthlyPreviewNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->logger->info("Nothing to send.");
            }
        }
    }

    private function sendWeeklyPreviewNotifications($subscriptions_by_args) {
        $weekly_preview_getter = $this->weeklyPreviewGetter;
        $weekly_preview_getter->setEntityManager($this->entityManager);
        $weekly_preview_getter->setDateUtils($this->dateUtils);
        $weekly_preview_getter->setEnvUtils($this->envUtils);
        $weekly_preview_getter->setLogger($this->logger);

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->logger->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $weekly_preview_getter->getWeeklyPreviewNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->logger->info("Nothing to send.");
            }
        }
    }

    private function sendWeeklySummaryNotifications($subscriptions_by_args) {
        $weekly_summary_getter = $this->weeklySummaryGetter;
        $weekly_summary_getter->setEntityManager($this->entityManager);
        $weekly_summary_getter->setDateUtils($this->dateUtils);
        $weekly_summary_getter->setEnvUtils($this->envUtils);
        $weekly_summary_getter->setLogger($this->logger);

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->logger->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $weekly_summary_getter->getWeeklySummaryNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->logger->info("Nothing to send.");
            }
        }
    }

    private function sendNotificationToSubscription($notification, $subscription) {
        $user = $subscription->getUser();
        $title = $notification->title;
        $text = $notification->getTextForUser($user);
        $config = $notification->config;
        $subscription_id = $subscription->getId();
        $delivery_type = $subscription->getDeliveryType();
        $user_id = $user->getId();
        $this->logger->info("Sending notification {$title} over {$delivery_type} to user ({$user_id})...");
        switch ($delivery_type) {
            case NotificationSubscription::DELIVERY_EMAIL:
                try {
                    $this->emailUtils->setLogger($this->logger);
                    $email = $this->emailUtils->createEmail();
                    $email->configure($user, "[OLZ] {$title}", $text, $config);
                    $email->send();
                    $this->logger->info("Email sent to user ({$user_id}): {$title}");
                } catch (\Exception $exc) {
                    $message = $exc->getMessage();
                    $this->logger->critical("Error sending email to user ({$user_id}): {$message}");
                }
                break;
            case NotificationSubscription::DELIVERY_TELEGRAM:
                $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
                $telegram_link = $telegram_link_repo->findOneBy(['user' => $user]);
                if (!$telegram_link) {
                    $this->logger->critical("User ({$user_id}) has no telegram link, but a subscription ({$subscription_id})");
                    return;
                }
                $user_chat_id = $telegram_link->getTelegramChatId();
                if (!$user_chat_id) {
                    $this->logger->critical("User ({$user_id}) has a telegram link without chat ID, but a subscription ({$subscription_id})");
                    return;
                }
                $html_title = $this->telegramUtils->renderMarkdown($title);
                $html_text = $this->telegramUtils->renderMarkdown($text);
                $this->telegramUtils->callTelegramApi('sendMessage', [
                    'chat_id' => $user_chat_id,
                    'parse_mode' => 'HTML',
                    'text' => "<b>{$html_title}</b>\n\n{$html_text}",
                    'disable_web_page_preview' => true,
                ]);
                $this->logger->info("Telegram sent to user ({$user_id}): {$title}");
                break;
            default:
                $this->logger->critical("Unknown delivery type '{$delivery_type}'");
                break;
        }
    }
}
