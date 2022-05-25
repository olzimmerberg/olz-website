<?php

use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Olz\Entity\User;

require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/common/BackgroundTask.php';
require_once __DIR__.'/SendDailyNotificationsTask/DailySummaryGetter.php';
require_once __DIR__.'/SendDailyNotificationsTask/DeadlineWarningGetter.php';
require_once __DIR__.'/SendDailyNotificationsTask/EmailConfigurationReminderGetter.php';
require_once __DIR__.'/SendDailyNotificationsTask/MonthlyPreviewGetter.php';
require_once __DIR__.'/SendDailyNotificationsTask/TelegramConfigurationReminderGetter.php';
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
        $this->setEmailConfigurationReminderGetter(
            new EmailConfigurationReminderGetter());
        $this->setMonthlyPreviewGetter(new MonthlyPreviewGetter());
        $this->setTelegramConfigurationReminderGetter(
            new TelegramConfigurationReminderGetter());
        $this->setWeeklyPreviewGetter(new WeeklyPreviewGetter());
        $this->setWeeklySummaryGetter(new WeeklySummaryGetter());
    }

    public function setDailySummaryGetter($dailySummaryGetter) {
        $this->dailySummaryGetter = $dailySummaryGetter;
    }

    public function setDeadlineWarningGetter($deadlineWarningGetter) {
        $this->deadlineWarningGetter = $deadlineWarningGetter;
    }

    public function setEmailConfigurationReminderGetter($emailConfigurationReminderGetter) {
        $this->emailConfigurationReminderGetter = $emailConfigurationReminderGetter;
    }

    public function setMonthlyPreviewGetter($monthlyPreviewGetter) {
        $this->monthlyPreviewGetter = $monthlyPreviewGetter;
    }

    public function setTelegramConfigurationReminderGetter($telegramConfigurationReminderGetter) {
        $this->telegramConfigurationReminderGetter = $telegramConfigurationReminderGetter;
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
        $this->logger->info("Autogenerating notifications...");
        $this->autoupdateNotificationSubscriptions();

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
                case NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER:
                    $this->sendEmailConfigurationReminderNotifications($subscriptions_by_args);
                    break;
                case NotificationSubscription::TYPE_MONTHLY_PREVIEW:
                    $this->sendMonthlyPreviewNotifications($subscriptions_by_args);
                    break;
                case NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER:
                    $this->sendTelegramConfigurationReminderNotifications($subscriptions_by_args);
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

    protected function autoupdateNotificationSubscriptions() {
        $this->autoupdateEmailNotificationSubscriptions();
        $this->autoupdateTelegramNotificationSubscriptions();
        $this->entityManager->flush();
    }

    protected function autoupdateEmailNotificationSubscriptions() {
        $email_notifications_state = $this->getEmailConfigReminderState();

        $now_datetime = new \DateTime($this->dateUtils->getIsoNow());
        $notification_subscription_repo = $this->entityManager->getRepository(NotificationSubscription::class);
        $user_repo = $this->entityManager->getRepository(User::class);
        foreach ($email_notifications_state as $user_id => $state) {
            $has_reminder = $state['has_reminder'] ?? false;
            $needs_reminder = $state['needs_reminder'] ?? false;
            if ($needs_reminder && !$has_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->logger->info("Generating email configuration reminder subscription for '{$user}'...");
                $generated_subscription = new NotificationSubscription();
                $generated_subscription->setUser($user);
                $generated_subscription->setDeliveryType(
                    NotificationSubscription::DELIVERY_EMAIL);
                $generated_subscription->setNotificationType(
                    NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
                $generated_subscription->setNotificationTypeArgs(
                    json_encode(['cancelled' => false]));
                $generated_subscription->setCreatedAt($now_datetime);
                $this->entityManager->persist($generated_subscription);
            }
            if ($has_reminder && !$needs_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->logger->info("Removing email configuration reminder subscription for '{$user}'...");
                $subscriptions = $notification_subscription_repo->findBy([
                    'user' => $user,
                    'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                ]);
                foreach ($subscriptions as $subscription) {
                    $this->entityManager->remove($subscription);
                }
            }
        }
    }

    protected function getEmailConfigReminderState() {
        $email_notifications_state = [];

        // Find users with existing email config reminder notification subscriptions.
        $notification_subscription_repo = $this->entityManager->getRepository(NotificationSubscription::class);
        $email_notification_subscriptions = $notification_subscription_repo->findBy([
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ]);
        foreach ($email_notification_subscriptions as $subscription) {
            $user_id = $subscription->getUser()->getId();
            $user_state = $email_notifications_state[$user_id] ?? [];
            $user_state['has_reminder'] = true;
            $email_notifications_state[$user_id] = $user_state;
        }

        // Find users who should have email config reminder notification subscriptions.
        $user_repo = $this->entityManager->getRepository(User::class);
        $users_with_email = $user_repo->getUsersWithLogin();
        $non_config_reminder_notification_types = $this->getNonConfigReminderNotificationTypes();
        foreach ($users_with_email as $user_with_email) {
            $subscription = $notification_subscription_repo->findOneBy([
                'user' => $user_with_email,
                'notification_type' => $non_config_reminder_notification_types,
            ]);
            if (!$subscription) {
                $user_id = $user_with_email->getId();
                $user_state = $email_notifications_state[$user_id] ?? [];
                $user_state['needs_reminder'] = true;
                $email_notifications_state[$user_id] = $user_state;
            }
        }

        return $email_notifications_state;
    }

    protected function getNonConfigReminderNotificationTypes() {
        return array_filter(
            NotificationSubscription::ALL_NOTIFICATION_TYPES,
            function ($notification_type) {
                return
                    $notification_type !== NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER
                    && $notification_type !== NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER
                ;
            }
        );
    }

    private function autoupdateTelegramNotificationSubscriptions() {
        $telegram_notifications_state = $this->getTelegramConfigReminderState();

        $now_datetime = new \DateTime($this->dateUtils->getIsoNow());
        $notification_subscription_repo = $this->entityManager->getRepository(NotificationSubscription::class);
        $user_repo = $this->entityManager->getRepository(User::class);
        foreach ($telegram_notifications_state as $user_id => $state) {
            $has_reminder = $state['has_reminder'] ?? false;
            $needs_reminder = $state['needs_reminder'] ?? false;
            if ($needs_reminder && !$has_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->logger->info("Generating telegram configuration reminder subscription for '{$user}'...");
                $generated_subscription = new NotificationSubscription();
                $generated_subscription->setUser($user);
                $generated_subscription->setDeliveryType(
                    NotificationSubscription::DELIVERY_TELEGRAM);
                $generated_subscription->setNotificationType(
                    NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
                $generated_subscription->setNotificationTypeArgs(
                    json_encode(['cancelled' => false]));
                $generated_subscription->setCreatedAt($now_datetime);
                $this->entityManager->persist($generated_subscription);
            }
            if ($has_reminder && !$needs_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->logger->info("Removing telegram configuration reminder subscription for '{$user}'...");
                $subscriptions = $notification_subscription_repo->findBy([
                    'user' => $user,
                    'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
                ]);
                foreach ($subscriptions as $subscription) {
                    $this->entityManager->remove($subscription);
                }
            }
        }
    }

    protected function getTelegramConfigReminderState() {
        $telegram_notifications_state = [];

        // Find users with existing telegram config reminder notification subscriptions.
        $notification_subscription_repo = $this->entityManager->getRepository(NotificationSubscription::class);
        $telegram_notification_subscriptions = $notification_subscription_repo->findBy([
            'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        ]);
        foreach ($telegram_notification_subscriptions as $subscription) {
            $user_id = $subscription->getUser()->getId();
            $user_state = $telegram_notifications_state[$user_id] ?? [];
            $user_state['has_reminder'] = true;
            $telegram_notifications_state[$user_id] = $user_state;
        }

        // Find users who should have telegram config reminder notification subscriptions.
        $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
        $telegram_links = $telegram_link_repo->getActivatedTelegramLinks();
        $non_config_reminder_notification_types = $this->getNonConfigReminderNotificationTypes();
        foreach ($telegram_links as $telegram_link) {
            $user = $telegram_link->getUser();
            $subscription = $notification_subscription_repo->findOneBy([
                'user' => $user,
                'delivery_type' => NotificationSubscription::DELIVERY_TELEGRAM,
                'notification_type' => $non_config_reminder_notification_types,
            ]);
            if (!$subscription) {
                $user_id = $user->getId();
                $user_state = $telegram_notifications_state[$user_id] ?? [];
                $user_state['needs_reminder'] = true;
                $telegram_notifications_state[$user_id] = $user_state;
            }
        }

        return $telegram_notifications_state;
    }

    private function getNotificationSubscriptions() {
        $notification_subscription_repo = $this->entityManager->getRepository(NotificationSubscription::class);
        $subscriptions = $notification_subscription_repo->findAll();

        $subscriptions_by_type_and_args = [];
        foreach ($subscriptions as $subscription) {
            $notification_type = $subscription->getNotificationType();
            $notification_args = $subscription->getNotificationTypeArgs();
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

    private function sendEmailConfigurationReminderNotifications($subscriptions_by_args) {
        $configuration_reminder_getter = $this->emailConfigurationReminderGetter;
        $configuration_reminder_getter->setDateUtils($this->dateUtils);
        $configuration_reminder_getter->setEnvUtils($this->envUtils);
        $configuration_reminder_getter->setLogger($this->logger);

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->logger->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $configuration_reminder_getter->getNotification($args);
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

    private function sendTelegramConfigurationReminderNotifications($subscriptions_by_args) {
        $configuration_reminder_getter = $this->telegramConfigurationReminderGetter;
        $configuration_reminder_getter->setDateUtils($this->dateUtils);
        $configuration_reminder_getter->setEnvUtils($this->envUtils);
        $configuration_reminder_getter->setLogger($this->logger);

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->logger->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $configuration_reminder_getter->getNotification($args);
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
                } catch (\Throwable $th) {
                    $th_class = get_class($th);
                    $message = $th->getMessage();
                    $this->logger->critical("Error sending email to user ({$user_id}): [{$th_class}] {$message}", []);
                }
                break;
            case NotificationSubscription::DELIVERY_TELEGRAM:
                $telegram_link_repo = $this->entityManager->getRepository(TelegramLink::class);
                $telegram_link = $telegram_link_repo->findOneBy(['user' => $user]);
                if (!$telegram_link) {
                    $this->logger->notice("User ({$user_id}) has no telegram link, but a subscription ({$subscription_id})");
                    return;
                }
                $user_chat_id = $telegram_link->getTelegramChatId();
                if (!$user_chat_id) {
                    $this->logger->critical("User ({$user_id}) has a telegram link without chat ID, but a subscription ({$subscription_id})");
                    return;
                }
                $html_title = $this->telegramUtils->renderMarkdown($title);
                $html_text = $this->telegramUtils->renderMarkdown($text);
                try {
                    $this->telegramUtils->callTelegramApi('sendMessage', [
                        'chat_id' => $user_chat_id,
                        'parse_mode' => 'HTML',
                        'text' => "<b>{$html_title}</b>\n\n{$html_text}",
                        'disable_web_page_preview' => true,
                    ]);
                    $this->logger->info("Telegram sent to user ({$user_id}): {$title}");
                } catch (\Throwable $th) {
                    $th_class = get_class($th);
                    $message = $th->getMessage();
                    $this->logger->error("Error sending telegram to user ({$user_id}): [{$th_class}] {$message}", []);
                }
                break;
            default:
                $this->logger->critical("Unknown delivery type '{$delivery_type}'");
                break;
        }
    }
}
