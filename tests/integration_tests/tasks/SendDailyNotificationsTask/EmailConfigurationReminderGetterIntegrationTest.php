<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/EmailConfigurationReminderGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \EmailConfigurationReminderGetter
 */
final class EmailConfigurationReminderGetterIntegrationTest extends IntegrationTestCase {
    public function testEmailConfigurationReminderGetter(): void {
        $the_day = EmailConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-07-{$the_day_str} 16:00:00");
        $logger = new Logger('EmailConfigurationReminderGetterIntegrationTest');
        $user = new User();
        $user->setFirstName('First');
        $user->setEmail('first-user@test.olzimmerberg.ch');

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Leider hast du bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.


        **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**
        
        In diesem Fall musst du dich auf der Website [*einloggen*](http://integration-test.host/_/service.php#login-dialog), und unter ["Service"](http://integration-test.host/_/service.php) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.

        Falls du dein Passwort vergessen hast, kannst du es im Login-Dialog bei "Passwort vergessen?" zurücksetzen. Du bist mit der E-Mail Adresse `first-user@test.olzimmerberg.ch` registriert.


        **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**

        Dann ignoriere dieses E-Mail. Wenn du es nicht deaktivierst, wird dir dieses E-Mail nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".


        ZZZZZZZZZZ;
        $this->assertSame('Kein Newsletter abonniert', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
