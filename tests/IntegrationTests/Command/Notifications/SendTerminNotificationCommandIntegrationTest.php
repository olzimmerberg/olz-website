<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Notifications;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\Notifications\SendTerminNotificationCommand;
use Olz\Constants\NotificationType;
use Olz\Entity\Throttling;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendTerminNotificationCommand
 */
final class SendTerminNotificationCommandIntegrationTest extends IntegrationTestCase {
    public function testSendTerminNotificationCommandAutogenerateSubscriptions(): void {
        $job = $this->getSut();
        $job->autogenerateSubscriptions();

        $this->assertSame([
            "INFO Generating termin notification subscriptions...",
            "INFO Generating termin notification subscription for 'admin (User ID: 1)' via 'email'...",
            "INFO Generating termin notification subscription for 'admin (User ID: 1)' via 'telegram'...",
            "INFO Generating termin notification subscription for 'vorstand (User ID: 2)' via 'email'...",
            "INFO Generating termin notification subscription for 'karten (User ID: 3)' via 'email'...",
            "INFO Generating termin notification subscription for 'hackerman (User ID: 4)' via 'email'...",
            "INFO Generating termin notification subscription for 'benutzer (User ID: 5)' via 'email'...",
            "INFO Generating termin notification subscription for 'parent (User ID: 6)' via 'email'...",
            "INFO Generating termin notification subscription for 'kaderlaeufer (User ID: 9)' via 'email'...",
        ], $this->getLogs());
    }

    public function testSendTerminNotificationCommand1(): void {
        $throttling_repo = $this->getEntityManager()->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('send-termin-notifications', "2020-08-18 18:00:00");
        $date_utils = new DateUtils("2020-08-18 19:00:00");
        $user = FakeUser::defaultUser();

        $job = $this->getSut();
        $job->setDateUtils($date_utils);
        $notifications = $job->getNotifications([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Dies ist eine Termin-Erinnerung für den Termin [Training 4](http://integration-test.host/termine/7) vom Di, 08.09.:



            ZZZZZZZZZZ;
        $this->assertSame([], $this->getLogs());
        $this->assertCount(1, $notifications);
        $this->assertSame('Termin-Erinnerung: In drei Wochen ist dein Training', $notifications[0]->title);
        $this->assertSame($expected_text, $notifications[0]->getTextForUser($user));
        $this->assertSame(
            NotificationType::TERMIN_NOTIFICATION,
            $notifications[0]->config['notification_type'] ?? null,
        );
        $this->assertSame([6 => true], $notifications[0]->config['recipient_user_ids'] ?? null);
    }

    public function testSendTerminNotificationCommand2(): void {
        $throttling_repo = $this->getEntityManager()->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('send-termin-notifications', "2020-08-25 18:00:00");
        $date_utils = new DateUtils("2020-08-25 19:00:00");
        $user = FakeUser::defaultUser();

        $job = $this->getSut();
        $job->setDateUtils($date_utils);
        $notifications = $job->getNotifications([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Dies ist eine Termin-Erinnerung für den Termin [Training 4](http://integration-test.host/termine/7) vom Di, 08.09.:



            ZZZZZZZZZZ;
        $this->assertSame([], $this->getLogs());
        $this->assertCount(1, $notifications);
        $this->assertSame('Termin-Erinnerung: Fortschritt Trainingsvorbereitung', $notifications[0]->title);
        $this->assertSame($expected_text, $notifications[0]->getTextForUser($user));
        $this->assertSame(
            NotificationType::TERMIN_NOTIFICATION,
            $notifications[0]->config['notification_type'] ?? null,
        );
        $this->assertSame([2 => true, 6 => true], $notifications[0]->config['recipient_user_ids'] ?? null);
    }

    public function testSendTerminNotificationCommand3(): void {
        $throttling_repo = $this->getEntityManager()->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('send-termin-notifications', "2020-09-01 18:00:00");
        $date_utils = new DateUtils("2020-09-01 19:00:00");
        $user = FakeUser::defaultUser();

        $job = $this->getSut();
        $job->setDateUtils($date_utils);
        $notifications = $job->getNotifications([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Dies ist eine Termin-Erinnerung für den Termin [Training 4](http://integration-test.host/termine/7) vom Di, 08.09.:


            
            ZZZZZZZZZZ;
        $this->assertSame([], $this->getLogs());
        $this->assertCount(1, $notifications);
        $this->assertSame('Termin-Erinnerung: Kartendruck für Training', $notifications[0]->title);
        $this->assertSame($expected_text, $notifications[0]->getTextForUser($user));
        $this->assertSame(
            NotificationType::TERMIN_NOTIFICATION,
            $notifications[0]->config['notification_type'] ?? null,
        );
        $this->assertSame([3 => true, 6 => true], $notifications[0]->config['recipient_user_ids'] ?? null);
    }

    protected function getSut(): SendTerminNotificationCommand {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SendTerminNotificationCommand::class);
    }

    protected function getEntityManager(): EntityManagerInterface {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
