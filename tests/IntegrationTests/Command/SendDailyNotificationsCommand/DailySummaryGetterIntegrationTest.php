<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter;
use Olz\Entity\User;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter
 */
final class DailySummaryGetterIntegrationTest extends IntegrationTestCase {
    public function testDailySummaryGetterDay1(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-01-01 12:51:00');
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'galerie' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo First,

            Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):


            **Aktuell**

            - 01.01. 00:00: [Frohes neues Jahr! 🎆](http://integration-test.host/news/3)


            **Kaderblog**

            - 01.01. 15:15: [Saisonstart 2020!](http://integration-test.host/news/6403)


            **Aktualisierte Termine**

            - 06.06.: [Brunch OL](http://integration-test.host/termine/2)


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDailySummaryGetterDay2(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-01-02 12:51:00');
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'galerie' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo First,

            Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):


            **Forum**

            - 01.01. 21:45: [Guets Nois! 🎉](http://integration-test.host/news/2901)


            **Aktualisierte Termine**

            - 02.01.: [Berchtoldstag 🥈](http://integration-test.host/termine/1)


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDailySummaryGetterDay3(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-01-03 12:51:00');
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $notification = $job->getDailySummaryNotification([
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
}
