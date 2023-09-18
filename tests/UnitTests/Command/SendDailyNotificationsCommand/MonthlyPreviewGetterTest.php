<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;

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
        if (preg_match('/deadline/', var_export($criteria, true))) {
            $termin = new Termin();
            $termin->setId(3);
            $termin->setStartDate(new \DateTime('2020-04-13 19:30:00'));
            $termin->setDeadline(new \DateTime('2020-04-14 23:59:59'));
            $termin->setTitle('Test Termin mit OLZ-Meldeschluss');
            $range_termin = new Termin();
            $range_termin->setId(4);
            $range_termin->setStartDate(new \DateTime('2020-04-20'));
            $range_termin->setEndDate(new \DateTime('2020-04-30'));
            $range_termin->setDeadline(new \DateTime('2020-04-15 23:59:59'));
            $range_termin->setTitle('End of Month mit OLZ-Meldeschluss');
            return [$termin, $range_termin];
        }
        $termin = new Termin();
        $termin->setId(1);
        $termin->setStartDate(new \DateTime('2020-04-13 19:30:00'));
        $termin->setTitle('Test Termin');
        $range_termin = new Termin();
        $range_termin->setId(2);
        $range_termin->setStartDate(new \DateTime('2020-04-20'));
        $range_termin->setEndDate(new \DateTime('2020-04-30'));
        $range_termin->setTitle('End of Month');
        return [$termin, $range_termin];
    }

    public function findOneBy($where) {
        if ($where == ['solv_uid' => 123, 'on_off' => 1]) {
            $termin = new Termin();
            $termin->setId(3);
            $termin->setStartDate(new \DateTime('2020-04-18 19:30:00'));
            $termin->setTitle('Termin mit Meldeschluss');
            return $termin;
        }
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter
 */
final class MonthlyPreviewGetterTest extends UnitTestCase {
    public function testMonthlyPreviewGetterOnWrongWeekday(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00'); // a Friday

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame(null, $notification);
    }

    public function testMonthlyPreviewGetterTooEarlyInMonth(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $date_utils = new FixedDateUtils('2020-03-14 16:00:00'); // a Saturday, but not yet the second last

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame(null, $notification);
    }

    public function testMonthlyPreviewGetterTooLateInMonth(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $date_utils = new FixedDateUtils('2020-03-28 16:00:00'); // a Saturday, but already the last

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame(null, $notification);
    }

    public function testMonthlyPreviewGetter(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $solv_event_repo = new FakeMonthlyPreviewGetterSolvEventRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $termin_repo = new FakeMonthlyPreviewGetterTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-21 16:00:00'); // the second last Saturday of the month
        $user = new User();
        $user->setFirstName('First');

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getMonthlyPreviewNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Im April haben wir Folgendes auf dem Programm:


        **Termine**
        
        - 13.04.: [Test Termin](http://fake-base-url/_/termine/1)
        - 20.04. - 30.04.: [End of Month](http://fake-base-url/_/termine/2)


        **Meldeschlüsse**

        - 13.04.: Meldeschluss für '[Termin mit Meldeschluss](http://fake-base-url/_/termine/3)'
        - 14.04.: Meldeschluss für '[Test Termin mit OLZ-Meldeschluss](http://fake-base-url/_/termine/3)'
        - 15.04.: Meldeschluss für '[End of Month mit OLZ-Meldeschluss](http://fake-base-url/_/termine/4)'
        

        ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Monatsvorschau April', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptyMonthlyPreviewGetter(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $solv_event_repo = new FakeMonthlyPreviewGetterSolvEventRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $termin_repo = new FakeMonthlyPreviewGetterTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $date_utils = new FixedDateUtils('2021-03-20 16:00:00'); // the second last Saturday of the month
        $user = new User();
        $user->setFirstName('First');

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame(null, $notification);
    }
}
