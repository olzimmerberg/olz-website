<?php

require_once __DIR__.'/Notification.php';

class TelegramConfigurationReminderGetter {
    use Psr\Log\LoggerAwareTrait;

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getNotification($args) {
        $day_of_month = intval($this->dateUtils->getCurrentDateInFormat('j'));
        $total_days_of_month = intval($this->dateUtils->getCurrentDateInFormat('t'));
        if ($day_of_month !== 1) {
            return null;
        }

        $base_href = $this->envUtils->getBaseHref();
        $code_href = $this->envUtils->getCodeHref();
        $service_url = "{$base_href}{$code_href}service.php";

        $title = "Keine Push-Nachrichten abonniert";
        $text = "Hallo %%userFirstName%%,\n\nDu hast bisher keinerlei Push-Nachrichten für Telegram abonniert.\n\n\n**Du möchtest eigentlich Push-Nachrichten erhalten?**\n\nIn diesem Fall musst du dich auf der Website *einloggen*, und unter [\"Service\"]({$service_url}) bei \"Nachrichten-Push\" die gewünschten Benachrichtigungen auswählen.\n\n\n**Du möchtest gar keine Push-Nachrichten erhalten?**\n\nDann lösche einfach diesen Chat.\n\n";

        return new Notification($title, $text, [
            'notification_type' => null,
        ]);
    }
}
