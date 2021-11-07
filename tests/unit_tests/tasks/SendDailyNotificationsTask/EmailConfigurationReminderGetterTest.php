<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/SolvEvent.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/EmailConfigurationReminderGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \EmailConfigurationReminderGetter
 */
final class EmailConfigurationReminderGetterTest extends UnitTestCase {
    public function testEmailConfigurationReminderGetterOnWrongDay(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00'); // not the first day of the month
        $logger = new Logger('EmailConfigurationReminderGetterTest');

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getNotification(['cancelled' => false]);

        $this->assertSame(null, $notification);
    }

    public function testEmailConfigurationReminderGetterCancelled(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-01 19:00:00'); // the first day of the month
        $logger = new Logger('EmailConfigurationReminderGetterTest');

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getNotification(['cancelled' => true]);

        $this->assertSame(null, $notification);
    }

    public function testEmailConfigurationReminderGetter(): void {
        $date_utils = new FixedDateUtils('2020-03-01 19:00:00'); // the first day of the month
        $env_utils = new FakeEnvUtils();
        $logger = new Logger('EmailConfigurationReminderGetterTest');
        $user = new User();
        $user->setFirstName('First');

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getNotification(['cancelled' => false]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Du hast bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.


        **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**
        
        In diesem Fall musst du dich auf der Website *einloggen*, und unter ["Service"](http://fake-base-url/_/service.php) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.


        **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**

        Dann ignoriere dieses E-Mail. Wenn du dieses E-Mail nicht deaktivierst, wird es dir nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".


        ZZZZZZZZZZ;
        $this->assertSame('Kein Newsletter abonniert', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
