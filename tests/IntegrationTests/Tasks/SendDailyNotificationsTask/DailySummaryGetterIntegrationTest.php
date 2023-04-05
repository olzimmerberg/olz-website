<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter;
use Olz\Entity\User;
use Olz\Tests\Fake;
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
        $logger = Fake\FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):
        
        
        **Aktuell**
        
        - 01.01. 00:00: [Frohes neues Jahr! 🎆](http://integration-test.host/aktuell.php?id=3)
        - 01.01. 15:15: [Saisonstart 2020!](http://integration-test.host/aktuell.php?id=6403)
        
        
        **Kaderblog**
        
        - 01.01. 15:15: [Saisonstart 2020!](http://integration-test.host/blog.php#id3)
        

        **Aktualisierte Termine**
        
        - 06.06.: [Brunch OL](http://integration-test.host/termine.php?id=2)
        
        
        ZZZZZZZZZZ;
        $this->assertSame([
        ], $logger->handler->getPrettyRecords());
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDailySummaryGetterDay2(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-01-02 12:51:00');
        $logger = Fake\FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):
        
        
        **Aktuell**
        
        - 01.01. 21:45: [Guets Nois! 🎉](http://integration-test.host/aktuell.php?id=2901)

        
        **Forum**
        
        - 01.01. 21:45: [Guets Nois! 🎉](http://integration-test.host/forum.php#id1)

        
        **Aktualisierte Termine**

        - 02.01.: [Berchtoldstag 🥈](http://integration-test.host/termine.php?id=1)


        ZZZZZZZZZZ;
        $this->assertSame([
        ], $logger->handler->getPrettyRecords());
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDailySummaryGetterDay3(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-01-03 12:51:00');
        $logger = Fake\FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $this->assertSame([
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(null, $notification);
    }
}
