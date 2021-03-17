<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/WeeklyPreviewGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';

class FakeWeeklyPreviewGetterEntityManager {
    public $repositories = [];

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }
}

class FakeWeeklyPreviewGetterTerminRepository {
    public function matching($criteria) {
        $termin = new Termin();
        $termin->setStartsOn(new DateTime('2020-04-24 19:30:00'));
        $termin->setTitle('Test Termin');
        $range_termin = new Termin();
        $range_termin->setStartsOn(new DateTime('2020-04-28'));
        $range_termin->setEndsOn(new DateTime('2020-04-29'));
        $range_termin->setTitle('End of Week');
        return [$termin, $range_termin];
    }
}

/**
 * @internal
 * @covers \WeeklyPreviewGetter
 */
final class WeeklyPreviewGetterTest extends TestCase {
    public function testWeeklyPreviewGetterOnWrongWeekday(): void {
        $entity_manager = new FakeWeeklyPreviewGetterEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00'); // a Friday
        $logger = new Logger('WeeklyPreviewGetterTest');

        $job = new WeeklyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getWeeklyPreviewNotification([]);

        $this->assertSame(null, $notification);
    }

    public function testWeeklyPreviewGetter(): void {
        $entity_manager = new FakeWeeklyPreviewGetterEntityManager();
        $termin_repo = new FakeWeeklyPreviewGetterTerminRepository();
        $entity_manager->repositories['Termin'] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-19 16:00:00'); // a Thursday
        $logger = new Logger('WeeklyPreviewGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getWeeklyPreviewNotification([]);

        $this->assertSame('Vorschau auf die Woche vom 23. March', $notification->title);
        $this->assertSame("Hallo First,\n\nBis Ende nächster Woche finden folgende Anlässe statt:\n\n- 24.04.: Test Termin\n- 28.04. - 29.04.: End of Week\n", $notification->getTextForUser($user));
    }
}
