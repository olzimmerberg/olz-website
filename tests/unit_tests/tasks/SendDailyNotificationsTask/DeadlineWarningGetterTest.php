<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/SolvEvent.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/DeadlineWarningGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';

class FakeDeadlineWarningGetterEntityManager {
    public $repositories = [];

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }
}

class FakeDeadlineWarningGetterSolvEventRepository {
    public function matching($criteria) {
        $solv_event1 = new SolvEvent();
        $solv_event1->setDeadline(new DateTime('2020-03-16'));
        $solv_event1->setSolvUid(1111);
        $solv_event2 = new SolvEvent();
        $solv_event2->setDeadline(new DateTime('2020-03-16'));
        $solv_event2->setSolvUid(2222);
        $solv_event3 = new SolvEvent();
        $solv_event3->setDeadline(new DateTime('2020-03-16'));
        $solv_event3->setSolvUid(3333);
        return [$solv_event1, $solv_event2, $solv_event3];
    }
}

class FakeDeadlineWarningGetterTerminRepository {
    public function findOneBy($where) {
        if ($where == ['solv_uid' => 1111]) {
            $termin = new Termin();
            $termin->setStartsOn(new DateTime('2020-04-13 19:30:00'));
            $termin->setTitle('Test Termin');
            return $termin;
        }
        if ($where == ['solv_uid' => 2222]) {
            $range_termin = new Termin();
            $range_termin->setStartsOn(new DateTime('2020-04-20'));
            $range_termin->setEndsOn(new DateTime('2020-04-30'));
            $range_termin->setTitle('End of Month');
            return $range_termin;
        }
        return null;
    }
}

/**
 * @internal
 * @covers \DeadlineWarningGetter
 */
final class DeadlineWarningGetterTest extends TestCase {
    public function testDeadlineWarningGetterWithIncorrectDaysArg(): void {
        $entity_manager = new FakeDeadlineWarningGetterEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = new Logger('DeadlineWarningGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification(['days' => 10]);

        $this->assertSame(null, $notification);
    }

    public function testDeadlineWarningGetter(): void {
        $entity_manager = new FakeDeadlineWarningGetterEntityManager();
        $solv_event_repo = new FakeDeadlineWarningGetterSolvEventRepository();
        $termin_repo = new FakeDeadlineWarningGetterTerminRepository();
        $entity_manager->repositories['SolvEvent'] = $solv_event_repo;
        $entity_manager->repositories['Termin'] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = new Logger('DeadlineWarningGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification(['days' => 3]);

        $this->assertSame('Meldeschlusswarnung', $notification->title);
        $this->assertSame("Hallo First,\n\nAchtung:\n\n16.03.: Meldeschluss für 'Test Termin'\n16.03.: Meldeschluss für 'End of Month'\n", $notification->getTextForUser($user));
    }
}
