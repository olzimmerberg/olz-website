<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;

class FakeWeeklyPreviewGetterSolvEventRepository {
    public function matching($criteria) {
        if (preg_match('/2021-03-18/', var_export($criteria, true))) {
            return [];
        }
        $solv_event = new SolvEvent();
        $solv_event->setSolvUid(123);
        $solv_event->setDeadline(new \DateTime('2020-04-25 19:30:00'));
        $solv_event_without_termin = new SolvEvent();
        $solv_event_without_termin->setSolvUid(321);
        $solv_event_without_termin->setDeadline(new \DateTime('2020-04-24 19:30:00'));
        return [$solv_event, $solv_event_without_termin];
    }
}

class FakeWeeklyPreviewGetterTerminRepository {
    public function matching($criteria) {
        if (preg_match('/2021-03-18/', var_export($criteria, true))) {
            return [];
        }
        if (preg_match('/deadline/', var_export($criteria, true))) {
            $termin = new Termin();
            $termin->setId(1);
            $termin->setStartDate(new \DateTime('2020-04-24 19:30:00'));
            $termin->setDeadline(new \DateTime('2020-04-23 23:59:59'));
            $termin->setTitle('Test Termin');
            $range_termin = new Termin();
            $range_termin->setId(2);
            $range_termin->setStartDate(new \DateTime('2020-04-28'));
            $range_termin->setEndDate(new \DateTime('2020-04-29'));
            $range_termin->setDeadline(new \DateTime('2020-04-20 23:59:59'));
            $range_termin->setTitle('End of Week');
            return [$termin, $range_termin];
        }
        $termin = new Termin();
        $termin->setId(1);
        $termin->setStartDate(new \DateTime('2020-04-24 19:30:00'));
        $termin->setTitle('Test Termin');
        $range_termin = new Termin();
        $range_termin->setId(2);
        $range_termin->setStartDate(new \DateTime('2020-04-28'));
        $range_termin->setEndDate(new \DateTime('2020-04-29'));
        $range_termin->setTitle('End of Week');
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
 * @covers \Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter
 */
final class WeeklyPreviewGetterTest extends UnitTestCase {
    public function testWeeklyPreviewGetterOnWrongWeekday(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00'); // a Friday

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklyPreviewNotification([]);

        $this->assertSame(null, $notification);
    }

    public function testWeeklyPreviewGetter(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_repo = new FakeWeeklyPreviewGetterTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $solv_event_repo = new FakeWeeklyPreviewGetterSolvEventRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $date_utils = new FixedDateUtils('2020-03-19 16:00:00'); // a Thursday
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklyPreviewNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


        **Termine**
        
        - 24.04.: [Test Termin](http://fake-base-url/_/termine/1)
        - 28.04. - 29.04.: [End of Week](http://fake-base-url/_/termine/2)
        
        
        **Meldeschlüsse**
        
        - 25.04.: Meldeschluss für '[Termin mit Meldeschluss](http://fake-base-url/_/termine/3)'
        - 23.04.: Meldeschluss für '[Test Termin](http://fake-base-url/_/termine/1)'
        - 20.04.: Meldeschluss für '[End of Week](http://fake-base-url/_/termine/2)'
        

        ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Vorschau auf die Woche vom 23. März', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptyWeeklyPreviewGetter(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_repo = new FakeWeeklyPreviewGetterTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $solv_event_repo = new FakeWeeklyPreviewGetterSolvEventRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $date_utils = new FixedDateUtils('2021-03-18 16:00:00'); // a Thursday
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklyPreviewNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame(null, $notification);
    }
}
