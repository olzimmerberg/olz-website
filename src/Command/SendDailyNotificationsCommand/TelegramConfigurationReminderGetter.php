<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Olz\Entity\NotificationSubscription;
use Olz\Utils\WithUtilsTrait;

class TelegramConfigurationReminderGetter {
    use WithUtilsTrait;

    public const DAY_OF_MONTH = 22;

    public function getNotification($args) {
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $day_of_month = intval($this->dateUtils()->getCurrentDateInFormat('j'));
        $total_days_of_month = intval($this->dateUtils()->getCurrentDateInFormat('t'));
        if ($day_of_month !== self::DAY_OF_MONTH) {
            return null;
        }

        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $service_url = "{$base_href}{$code_href}service.php";

        $title = "Keine Push-Nachrichten abonniert";
        $text = <<<ZZZZZZZZZZ
        Hallo %%userFirstName%%,
        
        Du hast bisher keinerlei Push-Nachrichten für Telegram abonniert.
        
        
        **Du möchtest eigentlich Push-Nachrichten erhalten?**
        
        In diesem Fall musst du dich auf der Website *einloggen*, und unter ["Service"]({$service_url}) bei "Nachrichten-Push" die gewünschten Benachrichtigungen auswählen.
        
        
        **Du möchtest gar keine Push-Nachrichten erhalten?**
        
        Dann lösche einfach diesen Chat.


        ZZZZZZZZZZ;

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        ]);
    }
}
