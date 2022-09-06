<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Tasks\SendDailyNotificationsTask;

use Monolog\Logger;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use Olz\Tasks\SendDailyNotificationsTask\MonthlyPreviewGetter;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

class FakeMonthlyPreviewGetterSolvEventRepository {
    public function matching($criteria) {
        if (preg_match('/2021-03-20/', var_export($criteria, true))) {
            return [];
        }
        $solv_event = new SolvEvent();
        $solv_event->setSolvUid(123);
        $solv_event->setDeadline(new \DateTime('2020-04-13 19:30:00'));
        $solv_event_without_termin = new SolvEvent();
        $solv_event_without_termin->setSolvUid(321);
        $solv_event_without_termin->setDeadline(new \DateTime('2020-04-15 19:30:00'));
        return [$solv_event, $solv_event_without_termin];
    }
}

class FakeMonthlyPreviewGetterTerminRepository {
    public function matching($criteria) {
        if (preg_match('/2021-03-20/', var_export($criteria, true))) {
            return [];
        }
        $termin = new Termin();
        $termin->setId(1);
        $termin->setStartsOn(new \DateTime('2020-04-13 19:30:00'));
        $termin->setTitle('Test Termin');
        $range_termin = new Termin();
        $range_termin->setId(2);
        $range_termin->setStartsOn(new \DateTime('2020-04-20'));
        $range_termin->setEndsOn(new \DateTime('2020-04-30'));
        $range_termin->setTitle('End of Month');
        return [$termin, $range_termin];
    }

    public function findOneBy($where) {
        if ($where == ['solv_uid' => 123, 'on_off' => 1]) {
            $termin = new Termin();
            $termin->setId(3);
            $termin->setStartsOn(new \DateTime('2020-04-18 19:30:00'));
            $termin->setTitle('Termin mit Meldeschluss');
            return $termin;
        }
    }
}

/**
 * @internal
 *
 * @covers \Olz\Tasks\SendDailyNotificationsTask\MonthlyPreviewGetter
 */
final class MonthlyPreviewGetterTest extends UnitTestCase {
    public function testMonthlyPreviewGetterOnWrongWeekday(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00'); // a Friday
        $logger = new Logger('MonthlyPreviewGetterTest');

        $job = new MonthlyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame(null, $notification);
    }

    public function testMonthlyPreviewGetterTooEarlyInMonth(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-14 16:00:00'); // a Saturday, but not yet the second last
        $logger = new Logger('MonthlyPreviewGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new MonthlyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame(null, $notification);
    }

    public function testMonthlyPreviewGetterTooLateInMonth(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-28 16:00:00'); // a Saturday, but already the last
        $logger = new Logger('MonthlyPreviewGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new MonthlyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame(null, $notification);
    }

    public function testMonthlyPreviewGetter(): void {
        $entity_manager = new FakeEntityManager();
        $solv_event_repo = new FakeMonthlyPreviewGetterSolvEventRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $termin_repo = new FakeMonthlyPreviewGetterTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-21 16:00:00'); // the second last Saturday of the month
        $env_utils = new FakeEnvUtils();
        $logger = new Logger('MonthlyPreviewGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new MonthlyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getMonthlyPreviewNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Im April haben wir Folgendes auf dem Programm:


        **Termine**
        
        - 13.04.: [Test Termin](http://fake-base-url/_/termine.php?id=1)
        - 20.04. - 30.04.: [End of Month](http://fake-base-url/_/termine.php?id=2)


        **Meldeschlüsse**

        - 13.04.: Meldeschluss für '[Termin mit Meldeschluss](http://fake-base-url/_/termine.php?id=3)'


        ZZZZZZZZZZ;
        $this->assertSame('Monatsvorschau April', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptyMonthlyPreviewGetter(): void {
        $entity_manager = new FakeEntityManager();
        $solv_event_repo = new FakeMonthlyPreviewGetterSolvEventRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $termin_repo = new FakeMonthlyPreviewGetterTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $date_utils = new FixedDateUtils('2021-03-20 16:00:00'); // the second last Saturday of the month
        $env_utils = new FakeEnvUtils();
        $logger = new Logger('MonthlyPreviewGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new MonthlyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame(null, $notification);
    }
}
