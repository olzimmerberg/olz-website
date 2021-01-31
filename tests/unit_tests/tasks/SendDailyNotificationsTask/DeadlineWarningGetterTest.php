<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
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

class FakeDeadlineWarningGetterTerminRepository {
    public function matching($criteria) {
        $termin = new Termin();
        $termin->setStartsOn(new DateTime('2020-04-13 19:30:00'));
        $termin->setTitle('Test Termin');
        $range_termin = new Termin();
        $range_termin->setStartsOn(new DateTime('2020-04-20'));
        $range_termin->setEndsOn(new DateTime('2020-04-30'));
        $range_termin->setTitle('End of Month');
        return [$termin, $range_termin];
    }
}

/**
 * @internal
 * @covers \DeadlineWarningGetter
 */
final class DeadlineWarningGetterTest extends TestCase {
    public function testDeadlineWarningGetter(): void {
        $entity_manager = new FakeDeadlineWarningGetterEntityManager();
        $termin_repo = new FakeDeadlineWarningGetterTerminRepository();
        $entity_manager->repositories['Termin'] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-21 16:00:00'); // a Saturday
        $logger = new Logger('DeadlineWarningGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification([]);

        $this->assertSame('Meldeschlusswarnung', $notification->title);
        $this->assertSame("Hallo First,\n\nAchtung:\n\n13.04.: Test Termin\n20.04. - 30.04.: End of Month\n", $notification->getTextForUser($user));
    }
}
