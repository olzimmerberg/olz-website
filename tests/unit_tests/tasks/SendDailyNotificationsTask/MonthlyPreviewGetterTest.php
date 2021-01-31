<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/MonthlyPreviewGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';

class FakeMonthlyPreviewGetterEntityManager {
    public $repositories = [];

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }
}

class FakeMonthlyPreviewGetterTerminRepository {
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
 * @covers \MonthlyPreviewGetter
 */
final class MonthlyPreviewGetterTest extends TestCase {
    public function testMonthlyPreviewGetterOnWrongWeekday(): void {
        $entity_manager = new FakeMonthlyPreviewGetterEntityManager();
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
        $entity_manager = new FakeMonthlyPreviewGetterEntityManager();
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
        $entity_manager = new FakeMonthlyPreviewGetterEntityManager();
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
        $entity_manager = new FakeMonthlyPreviewGetterEntityManager();
        $termin_repo = new FakeMonthlyPreviewGetterTerminRepository();
        $entity_manager->repositories['Termin'] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-21 16:00:00'); // a Saturday
        $logger = new Logger('MonthlyPreviewGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new MonthlyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getMonthlyPreviewNotification([]);

        $this->assertSame('Monatsvorschau April', $notification->title);
        $this->assertSame("Hallo First,\n\nIm April finden folgende Anlässe statt:\n\n13.04.: Test Termin\n20.04. - 30.04.: End of Month\n", $notification->getTextForUser($user));
    }
}
