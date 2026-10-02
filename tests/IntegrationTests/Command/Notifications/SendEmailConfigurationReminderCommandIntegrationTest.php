<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Notifications;

use Olz\Command\Notifications\SendEmailConfigurationReminderCommand;
use Olz\Constants\NotificationType;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendEmailConfigurationReminderCommand
 */
final class SendEmailConfigurationReminderCommandIntegrationTest extends IntegrationTestCase {
    public function testSendEmailConfigurationReminderCommandAutogenerateSubscriptions(): void {
        $job = $this->getSut();
        $job->autogenerateSubscriptions();

        $this->assertSame([
            'INFO Generating email configuration reminder subscriptions...',
            "INFO Generating email configuration reminder subscription for 'vorstand (User ID: 2)'...",
            "INFO Generating email configuration reminder subscription for 'karten (User ID: 3)'...",
            "INFO Generating email configuration reminder subscription for 'benutzer (User ID: 5)'...",
            "INFO Generating email configuration reminder subscription for 'parent (User ID: 6)'...",
            "INFO Generating email configuration reminder subscription for 'kaderlaeufer (User ID: 9)'...",
        ], $this->getLogs());
    }

    public function testSendEmailConfigurationReminderCommand(): void {
        $the_day = SendEmailConfigurationReminderCommand::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new DateUtils("2020-07-{$the_day_str} 16:00:00");
        $user = FakeUser::defaultUser();

        $job = $this->getSut();
        $job->setDateUtils($date_utils);
        $notifications = $job->getNotifications([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Leider hast du bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.


            **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**

            In diesem Fall musst du dich auf der Website [*einloggen*](http://integration-test.host/apps/newsletter#login-dialog), und im ["Newsletter"-App](http://integration-test.host/apps/newsletter) (ist auch unter "Service" zu finden) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.

            Falls du dein Passwort vergessen hast, kannst du es im Login-Dialog bei "Passwort vergessen?" zurücksetzen. Du bist mit der E-Mail Adresse `default-user@staging.olzimmerberg.ch` registriert.


            **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**

            Dann ignoriere dieses E-Mail. Wenn du es nicht deaktivierst, wird dir dieses E-Mail nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".


            ZZZZZZZZZZ;
        $this->assertSame([], $this->getLogs());
        $this->assertCount(1, $notifications);
        $this->assertSame('Kein Newsletter abonniert', $notifications[0]->title);
        $this->assertSame($expected_text, $notifications[0]->getTextForUser($user));
        $this->assertSame(
            NotificationType::EMAIL_CONFIG_REMINDER,
            $notifications[0]->config['notification_type'] ?? null,
        );
        $this->assertNull($notifications[0]->config['recipient_user_ids'] ?? null);
    }

    protected function getSut(): SendEmailConfigurationReminderCommand {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SendEmailConfigurationReminderCommand::class);
    }
}
