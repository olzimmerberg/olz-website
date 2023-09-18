<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeDeadlineWarningGetterSolvEventRepository {
    public $has_no_deadlines = false;

    public function matching($criteria) {
        if ($this->has_no_deadlines) {
            return [];
        }
        $solv_event1 = new SolvEvent();
        $solv_event1->setDeadline(new \DateTime('2020-03-16'));
        $solv_event1->setSolvUid(1111);
        $solv_event2 = new SolvEvent();
        $solv_event2->setDeadline(new \DateTime('2020-03-16'));
        $solv_event2->setSolvUid(2222);
        $solv_event3 = new SolvEvent();
        $solv_event3->setDeadline(new \DateTime('2020-03-16'));
        $solv_event3->setSolvUid(3333);
        return [$solv_event1, $solv_event2, $solv_event3];
    }
}

class FakeDeadlineWarningGetterTerminRepository {
    public $has_no_deadlines = false;

    public function findOneBy($where) {
        if ($where == ['solv_uid' => 1111, 'on_off' => 1]) {
            $termin = new Termin();
            $termin->setId(1);
            $termin->setStartDate(new \DateTime('2020-04-13 19:30:00'));
            $termin->setTitle('Test Termin');
            $termin->setOnOff(1);
            return $termin;
        }
        if ($where == ['solv_uid' => 2222, 'on_off' => 1]) {
            $range_termin = new Termin();
            $range_termin->setId(2);
            $range_termin->setStartDate(new \DateTime('2020-04-20'));
            $range_termin->setEndDate(new \DateTime('2020-04-30'));
            $range_termin->setTitle('End of Month');
            $range_termin->setOnOff(1);
            return $range_termin;
        }
        return null;
    }

    public function matching($criteria) {
        if ($this->has_no_deadlines) {
            return [];
        }
        $termin = new Termin();
        $termin->setId(270);
        $termin->setStartDate(new \DateTime('2020-04-13 19:30:00'));
        $termin->setDeadline(new \DateTime('2020-04-06'));
        $termin->setTitle('OLZ Termin');
        $termin->setOnOff(1);
        return [$termin];
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter
 */
final class DeadlineWarningGetterTest extends UnitTestCase {
    public function testDeadlineWarningGetterWithIncorrectDaysArg(): void {
        $entity_manager = WithUtilsCache::get('entityManager');

        $job = new DeadlineWarningGetter();

        $notification = $job->getDeadlineWarningNotification(['days' => 10]);

        $this->assertSame(null, $notification);
    }

    public function testDeadlineWarningGetterWhenThereIsNoDeadline(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $solv_event_repo = new FakeDeadlineWarningGetterSolvEventRepository();
        $termin_repo = new FakeDeadlineWarningGetterTerminRepository();
        $solv_event_repo->has_no_deadlines = true;
        $termin_repo->has_no_deadlines = true;
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();

        $notification = $job->getDeadlineWarningNotification(['days' => 3]);

        $this->assertSame(null, $notification);
    }

    public function testDeadlineWarningGetter(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $solv_event_repo = new FakeDeadlineWarningGetterSolvEventRepository();
        $termin_repo = new FakeDeadlineWarningGetterTerminRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();

        $notification = $job->getDeadlineWarningNotification(['days' => 3]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Folgende Meldeschlüsse stehen bevor:
        
        - 16.03.: Meldeschluss für '[Test Termin](http://fake-base-url/_/termine/1)'
        - 16.03.: Meldeschluss für '[End of Month](http://fake-base-url/_/termine/2)'
        - 06.04.: Meldeschluss für '[OLZ Termin](http://fake-base-url/_/termine/270)'

        ZZZZZZZZZZ;
        $this->assertSame('Meldeschlusswarnung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
