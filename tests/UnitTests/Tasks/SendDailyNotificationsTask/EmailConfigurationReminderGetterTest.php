<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Tasks\SendDailyNotificationsTask;

use Monolog\Logger;
use Olz\Entity\User;
use Olz\Tasks\SendDailyNotificationsTask\EmailConfigurationReminderGetter;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 * @covers \Olz\Tasks\SendDailyNotificationsTask\EmailConfigurationReminderGetter
 */
final class EmailConfigurationReminderGetterTest extends UnitTestCase {
    public function testEmailConfigurationReminderGetterOnWrongDay(): void {
        $entity_manager = new FakeEntityManager();
        $not_the_day = EmailConfigurationReminderGetter::DAY_OF_MONTH + 1;
        $not_the_day_str = str_pad("{$not_the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$not_the_day_str} 19:00:00");
        $logger = new Logger('EmailConfigurationReminderGetterTest');

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getNotification(['cancelled' => false]);

        $this->assertSame(null, $notification);
    }

    public function testEmailConfigurationReminderGetterCancelled(): void {
        $entity_manager = new FakeEntityManager();
        $the_day = EmailConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$the_day_str} 19:00:00");
        $logger = new Logger('EmailConfigurationReminderGetterTest');

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getNotification(['cancelled' => true]);

        $this->assertSame(null, $notification);
    }

    public function testEmailConfigurationReminderGetter(): void {
        $the_day = EmailConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$the_day_str} 19:00:00");
        $env_utils = new FakeEnvUtils();
        $logger = new Logger('EmailConfigurationReminderGetterTest');
        $user = new User();
        $user->setFirstName('First');
        $user->setEmail('first-user@test.olzimmerberg.ch');

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getNotification(['cancelled' => false]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Leider hast du bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.


        **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**
        
        In diesem Fall musst du dich auf der Website [*einloggen*](http://fake-base-url/_/service.php#login-dialog), und unter ["Service"](http://fake-base-url/_/service.php) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.

        Falls du dein Passwort vergessen hast, kannst du es im Login-Dialog bei "Passwort vergessen?" zurücksetzen. Du bist mit der E-Mail Adresse `first-user@test.olzimmerberg.ch` registriert.


        **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**

        Dann ignoriere dieses E-Mail. Wenn du es nicht deaktivierst, wird dir dieses E-Mail nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".


        ZZZZZZZZZZ;
        $this->assertSame('Kein Newsletter abonniert', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
