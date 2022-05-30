<?php

require_once __DIR__.'/Notification.php';
require_once __DIR__.'/../../model/NotificationSubscription.php';

class EmailConfigurationReminderGetter {
    use \Psr\Log\LoggerAwareTrait;

    public const DAY_OF_MONTH = 22;

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getNotification($args) {
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $day_of_month = intval($this->dateUtils->getCurrentDateInFormat('j'));
        $total_days_of_month = intval($this->dateUtils->getCurrentDateInFormat('t'));
        if ($day_of_month !== self::DAY_OF_MONTH) {
            return null;
        }

        $base_href = $this->envUtils->getBaseHref();
        $code_href = $this->envUtils->getCodeHref();
        $service_url = "{$base_href}{$code_href}service.php";

        $title = "Kein Newsletter abonniert";
        $text = <<<ZZZZZZZZZZ
        Hallo %%userFirstName%%,
        
        Leider hast du bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.
        
        
        **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**
        
        In diesem Fall musst du dich auf der Website [*einloggen*]({$service_url}#login-dialog), und unter ["Service"]({$service_url}) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.

        Falls du dein Passwort vergessen hast, kannst du es im Login-Dialog bei "Passwort vergessen?" zurücksetzen. Du bist mit der E-Mail Adresse `%%userEmail%%` registriert.
        
        
        **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**
        
        Dann ignoriere dieses E-Mail. Wenn du es nicht deaktivierst, wird dir dieses E-Mail nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".
        
        
        ZZZZZZZZZZ;

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ]);
    }
}
