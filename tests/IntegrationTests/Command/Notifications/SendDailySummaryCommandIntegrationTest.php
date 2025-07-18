<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Notifications;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\Notifications\SendDailySummaryCommand;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\EnvUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendDailySummaryCommand
 */
final class SendDailySummaryCommandIntegrationTest extends IntegrationTestCase {
    public function testSendDailySummaryCommandDay1(): void {
        $entityManager = $this->getEntityManager();
        $date_utils = new DateUtils('2020-01-01 12:51:00');
        $user = FakeUser::defaultUser();

        $job = new SendDailySummaryCommand();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'galerie' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):


            **Aktuell**

            - Mi, 01.01. 00:00: [Frohes neues Jahr! 🎆](http://integration-test.host/news/3)


            **Kaderblog**

            - Mi, 01.01. 15:15: [Saisonstart 2020!](http://integration-test.host/news/6403)


            **Aktualisierte Termine**

            - Sa, 06.06.: [Brunch OL](http://integration-test.host/termine/2)


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Tageszusammenfassung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testSendDailySummaryCommandDay2(): void {
        $entityManager = $this->getEntityManager();
        $date_utils = new DateUtils('2020-01-02 12:51:00');
        $user = FakeUser::defaultUser();

        $job = new SendDailySummaryCommand();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'galerie' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):


            **Forum**

            - Mi, 01.01. 21:45: [Guets Nois! 🎉](http://integration-test.host/news/2901)


            **Aktualisierte Termine**

            - Do, 02.01.: [Berchtoldstag 🥈](http://integration-test.host/termine/1)


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Tageszusammenfassung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testSendDailySummaryCommandDay3(): void {
        $entityManager = $this->getEntityManager();
        $date_utils = new DateUtils('2020-01-03 12:51:00');

        $job = new SendDailySummaryCommand();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'galerie' => true,
            'termine' => true,
        ]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }

    protected function getEntityManager(): EntityManagerInterface {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
