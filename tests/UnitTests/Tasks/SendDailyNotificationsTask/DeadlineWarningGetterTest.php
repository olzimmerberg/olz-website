<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Tasks\SendDailyNotificationsTask;

use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use Olz\Tasks\SendDailyNotificationsTask\DeadlineWarningGetter;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

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
            $termin->setStartsOn(new \DateTime('2020-04-13 19:30:00'));
            $termin->setTitle('Test Termin');
            $termin->setOnOff(1);
            return $termin;
        }
        if ($where == ['solv_uid' => 2222, 'on_off' => 1]) {
            $range_termin = new Termin();
            $range_termin->setId(2);
            $range_termin->setStartsOn(new \DateTime('2020-04-20'));
            $range_termin->setEndsOn(new \DateTime('2020-04-30'));
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
        $termin->setStartsOn(new \DateTime('2020-04-13 19:30:00'));
        $termin->setDeadline(new \DateTime('2020-04-06'));
        $termin->setTitle('OLZ Termin');
        $termin->setOnOff(1);
        return [$termin];
    }
}

/**
 * @internal
 *
 * @covers \Olz\Tasks\SendDailyNotificationsTask\DeadlineWarningGetter
 */
final class DeadlineWarningGetterTest extends UnitTestCase {
    public function testDeadlineWarningGetterWithIncorrectDaysArg(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification(['days' => 10]);

        $this->assertSame(null, $notification);
    }

    public function testDeadlineWarningGetterWhenThereIsNoDeadline(): void {
        $entity_manager = new FakeEntityManager();
        $solv_event_repo = new FakeDeadlineWarningGetterSolvEventRepository();
        $termin_repo = new FakeDeadlineWarningGetterTerminRepository();
        $solv_event_repo->has_no_deadlines = true;
        $termin_repo->has_no_deadlines = true;
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification(['days' => 3]);

        $this->assertSame(null, $notification);
    }

    public function testDeadlineWarningGetter(): void {
        $entity_manager = new FakeEntityManager();
        $solv_event_repo = new FakeDeadlineWarningGetterSolvEventRepository();
        $termin_repo = new FakeDeadlineWarningGetterTerminRepository();
        $entity_manager->repositories[SolvEvent::class] = $solv_event_repo;
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification(['days' => 3]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Folgende Meldeschlüsse stehen bevor:
        
        - 16.03.: Meldeschluss für '[Test Termin](http://fake-base-url/_/termine.php?id=1)'
        - 16.03.: Meldeschluss für '[End of Month](http://fake-base-url/_/termine.php?id=2)'
        - 06.04.: Meldeschluss für '[OLZ Termin](http://fake-base-url/_/termine.php?id=270)'

        ZZZZZZZZZZ;
        $this->assertSame('Meldeschlusswarnung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
