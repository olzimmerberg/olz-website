<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter;
use Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter;
use Olz\Command\SendDailyNotificationsCommand\EmailConfigurationReminderGetter;
use Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter;
use Olz\Command\SendDailyNotificationsCommand\Notification;
use Olz\Command\SendDailyNotificationsCommand\TelegramConfigurationReminderGetter;
use Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter;
use Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Olz\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Email;

// require_once __DIR__.'/../OlzInit.php';

#[AsCommand(name: 'olz:send-daily-notifications')]
class SendDailyNotificationsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected DailySummaryGetter $dailySummaryGetter;
    protected DeadlineWarningGetter $deadlineWarningGetter;
    protected EmailConfigurationReminderGetter $emailConfigurationReminderGetter;
    protected MonthlyPreviewGetter $monthlyPreviewGetter;
    protected TelegramConfigurationReminderGetter $telegramConfigurationReminderGetter;
    protected WeeklyPreviewGetter $weeklyPreviewGetter;
    protected WeeklySummaryGetter $weeklySummaryGetter;

    public function setDailySummaryGetter(DailySummaryGetter $dailySummaryGetter): void {
        $this->dailySummaryGetter = $dailySummaryGetter;
    }

    public function setDeadlineWarningGetter(DeadlineWarningGetter $deadlineWarningGetter): void {
        $this->deadlineWarningGetter = $deadlineWarningGetter;
    }

    public function setEmailConfigurationReminderGetter(EmailConfigurationReminderGetter $emailConfigurationReminderGetter): void {
        $this->emailConfigurationReminderGetter = $emailConfigurationReminderGetter;
    }

    public function setMonthlyPreviewGetter(MonthlyPreviewGetter $monthlyPreviewGetter): void {
        $this->monthlyPreviewGetter = $monthlyPreviewGetter;
    }

    public function setTelegramConfigurationReminderGetter(TelegramConfigurationReminderGetter $telegramConfigurationReminderGetter): void {
        $this->telegramConfigurationReminderGetter = $telegramConfigurationReminderGetter;
    }

    public function setWeeklyPreviewGetter(WeeklyPreviewGetter $weeklyPreviewGetter): void {
        $this->weeklyPreviewGetter = $weeklyPreviewGetter;
    }

    public function setWeeklySummaryGetter(WeeklySummaryGetter $weeklySummaryGetter): void {
        $this->weeklySummaryGetter = $weeklySummaryGetter;
    }

    public function __construct() {
        parent::__construct();
        $this->setDailySummaryGetter(new DailySummaryGetter());
        $this->setDeadlineWarningGetter(new DeadlineWarningGetter());
        $this->setEmailConfigurationReminderGetter(
            new EmailConfigurationReminderGetter()
        );
        $this->setMonthlyPreviewGetter(new MonthlyPreviewGetter());
        $this->setTelegramConfigurationReminderGetter(
            new TelegramConfigurationReminderGetter()
        );
        $this->setWeeklyPreviewGetter(new WeeklyPreviewGetter());
        $this->setWeeklySummaryGetter(new WeeklySummaryGetter());
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->log()->info("Autogenerating notifications...");
        $this->autoupdateNotificationSubscriptions();

        $subscriptions_by_type_and_args = $this->getNotificationSubscriptions();
        foreach ($subscriptions_by_type_and_args as $notification_type => $subscriptions_by_args) {
            $this->log()->info("Sending '{$notification_type}' notifications...");
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
                    $this->log()->critical("Unknown notification type '{$notification_type}'");
                    break;
            }
        }

        return Command::SUCCESS;
    }

    protected function autoupdateNotificationSubscriptions(): void {
        $this->autoupdateEmailNotificationSubscriptions();
        $this->autoupdateTelegramNotificationSubscriptions();
        $this->entityManager()->flush();
    }

    protected function autoupdateEmailNotificationSubscriptions(): void {
        $email_notifications_state = $this->getEmailConfigReminderState();

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $user_repo = $this->entityManager()->getRepository(User::class);
        foreach ($email_notifications_state as $user_id => $state) {
            $has_reminder = $state['has_reminder'] ?? false;
            $needs_reminder = $state['needs_reminder'] ?? false;
            if ($needs_reminder && !$has_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->log()->info("Generating email configuration reminder subscription for '{$user}'...");
                $generated_subscription = new NotificationSubscription();
                $generated_subscription->setUser($user);
                $generated_subscription->setDeliveryType(
                    NotificationSubscription::DELIVERY_EMAIL
                );
                $generated_subscription->setNotificationType(
                    NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER
                );
                $generated_subscription->setNotificationTypeArgs(
                    json_encode(['cancelled' => false])
                );
                $generated_subscription->setCreatedAt($now_datetime);
                $this->entityManager()->persist($generated_subscription);
            }
            if ($has_reminder && !$needs_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->log()->info("Removing email configuration reminder subscription for '{$user}'...");
                $subscriptions = $notification_subscription_repo->findBy([
                    'user' => $user,
                    'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                ]);
                foreach ($subscriptions as $subscription) {
                    $this->entityManager()->remove($subscription);
                }
            }
        }
    }

    /** @return array<int, array{has_reminder?: bool, needs_reminder?: bool}> */
    protected function getEmailConfigReminderState(): array {
        $email_notifications_state = [];

        // Find users with existing email config reminder notification subscriptions.
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
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
        $user_repo = $this->entityManager()->getRepository(User::class);
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

    /** @return array<string> */
    protected function getNonConfigReminderNotificationTypes(): array {
        return array_filter(
            NotificationSubscription::ALL_NOTIFICATION_TYPES,
            function ($notification_type) {
                return
                    $notification_type !== NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER
                    && $notification_type !== NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER;
            }
        );
    }

    private function autoupdateTelegramNotificationSubscriptions(): void {
        $telegram_notifications_state = $this->getTelegramConfigReminderState();

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
        $user_repo = $this->entityManager()->getRepository(User::class);
        foreach ($telegram_notifications_state as $user_id => $state) {
            $has_reminder = $state['has_reminder'] ?? false;
            $needs_reminder = $state['needs_reminder'] ?? false;
            if ($needs_reminder && !$has_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->log()->info("Generating telegram configuration reminder subscription for '{$user}'...");
                $generated_subscription = new NotificationSubscription();
                $generated_subscription->setUser($user);
                $generated_subscription->setDeliveryType(
                    NotificationSubscription::DELIVERY_TELEGRAM
                );
                $generated_subscription->setNotificationType(
                    NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER
                );
                $generated_subscription->setNotificationTypeArgs(
                    json_encode(['cancelled' => false])
                );
                $generated_subscription->setCreatedAt($now_datetime);
                $this->entityManager()->persist($generated_subscription);
            }
            if ($has_reminder && !$needs_reminder) {
                $user = $user_repo->findOneBy(['id' => $user_id]);
                $this->log()->info("Removing telegram configuration reminder subscription for '{$user}'...");
                $subscriptions = $notification_subscription_repo->findBy([
                    'user' => $user,
                    'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
                ]);
                foreach ($subscriptions as $subscription) {
                    $this->entityManager()->remove($subscription);
                }
            }
        }
    }

    /** @return array<int, array{has_reminder?: bool, needs_reminder?: bool}> */
    protected function getTelegramConfigReminderState(): array {
        $telegram_notifications_state = [];

        // Find users with existing telegram config reminder notification subscriptions.
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
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
        $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
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

    /** @return array<string, array<?string, array<NotificationSubscription>>> */
    private function getNotificationSubscriptions(): array {
        $notification_subscription_repo = $this->entityManager()->getRepository(NotificationSubscription::class);
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

    /** @param array<?string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendDailySummaryNotifications(array $subscriptions_by_args): void {
        $daily_summary_getter = $this->dailySummaryGetter;
        $daily_summary_getter->setAllUtils($this->getAllUtils());

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $daily_summary_getter->getDailySummaryNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->log()->info("Nothing to send.");
            }
        }
    }

    /** @param array<?string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendDeadlineWarningNotifications(array $subscriptions_by_args): void {
        $deadline_warning_getter = $this->deadlineWarningGetter;
        $deadline_warning_getter->setAllUtils($this->getAllUtils());

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $deadline_warning_getter->getDeadlineWarningNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->log()->info("Nothing to send.");
            }
        }
    }

    /** @param array<?string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendEmailConfigurationReminderNotifications(array $subscriptions_by_args): void {
        $configuration_reminder_getter = $this->emailConfigurationReminderGetter;
        $configuration_reminder_getter->setAllUtils($this->getAllUtils());

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $configuration_reminder_getter->getNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->log()->info("Nothing to send.");
            }
        }
    }

    /** @param array<?string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendMonthlyPreviewNotifications(array $subscriptions_by_args): void {
        $monthly_preview_getter = $this->monthlyPreviewGetter;
        $monthly_preview_getter->setAllUtils($this->getAllUtils());

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $monthly_preview_getter->getMonthlyPreviewNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->log()->info("Nothing to send.");
            }
        }
    }

    /** @param array<?string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendTelegramConfigurationReminderNotifications(array $subscriptions_by_args): void {
        $configuration_reminder_getter = $this->telegramConfigurationReminderGetter;
        $configuration_reminder_getter->setAllUtils($this->getAllUtils());

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $configuration_reminder_getter->getNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->log()->info("Nothing to send.");
            }
        }
    }

    /** @param array<?string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendWeeklyPreviewNotifications(array $subscriptions_by_args): void {
        $weekly_preview_getter = $this->weeklyPreviewGetter;
        $weekly_preview_getter->setAllUtils($this->getAllUtils());

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $weekly_preview_getter->getWeeklyPreviewNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->log()->info("Nothing to send.");
            }
        }
    }

    /** @param array<?string, array<NotificationSubscription>> $subscriptions_by_args */
    private function sendWeeklySummaryNotifications(array $subscriptions_by_args): void {
        $weekly_summary_getter = $this->weeklySummaryGetter;
        $weekly_summary_getter->setAllUtils($this->getAllUtils());

        foreach ($subscriptions_by_args as $args_json => $subscriptions) {
            $this->log()->info("Getting notification for '{$args_json}'...");
            $args = json_decode($args_json, true);
            $notification = $weekly_summary_getter->getWeeklySummaryNotification($args);
            if ($notification) {
                foreach ($subscriptions as $subscription) {
                    $this->sendNotificationToSubscription($notification, $subscription);
                }
            } else {
                $this->log()->info("Nothing to send.");
            }
        }
    }

    private function sendNotificationToSubscription(Notification $notification, NotificationSubscription $subscription): void {
        $user = $subscription->getUser();
        $title = $notification->title;
        $text = $notification->getTextForUser($user);
        $config = $notification->config;
        $subscription_id = $subscription->getId();
        $delivery_type = $subscription->getDeliveryType();
        $user_id = $user->getId();
        $this->log()->info("Sending notification {$title} over {$delivery_type} to user ({$user_id})...");
        switch ($delivery_type) {
            case NotificationSubscription::DELIVERY_EMAIL:
                try {
                    $email = (new Email())->subject("[OLZ] {$title}");
                    $email = $this->emailUtils()->buildOlzEmail($email, $user, $text, $config);
                    $this->mailer->send($email);
                    $this->log()->info("Email sent to user ({$user_id}): {$title}");
                } catch (\Throwable $th) {
                    $th_class = get_class($th);
                    $message = $th->getMessage();
                    $this->log()->critical("Error sending email to user ({$user_id}): [{$th_class}] {$message}", []);
                }
                break;
            case NotificationSubscription::DELIVERY_TELEGRAM:
                $telegram_link_repo = $this->entityManager()->getRepository(TelegramLink::class);
                $telegram_link = $telegram_link_repo->findOneBy(['user' => $user]);
                if (!$telegram_link) {
                    $this->log()->notice("User ({$user_id}) has no telegram link, but a subscription ({$subscription_id})");
                    return;
                }
                $user_chat_id = $telegram_link->getTelegramChatId();
                if (!$user_chat_id) {
                    $this->log()->critical("User ({$user_id}) has a telegram link without chat ID, but a subscription ({$subscription_id})");
                    return;
                }
                $html_title = $this->telegramUtils()->renderMarkdown($title);
                $html_text = $this->telegramUtils()->renderMarkdown($text);
                try {
                    $this->telegramUtils()->callTelegramApi('sendMessage', [
                        'chat_id' => $user_chat_id,
                        'parse_mode' => 'HTML',
                        'text' => "<b>{$html_title}</b>\n\n{$html_text}",
                        'disable_web_page_preview' => true,
                    ]);
                    $this->log()->info("Telegram sent to user ({$user_id}): {$title}");
                } catch (\Throwable $th) {
                    $th_class = get_class($th);
                    $message = $th->getMessage();
                    $this->log()->notice("Error sending telegram to user ({$user_id}): [{$th_class}] {$message}", []);
                }
                break;
            default:
                $this->log()->critical("Unknown delivery type '{$delivery_type}'");
                break;
        }
    }
}
