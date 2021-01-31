<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/WeeklySummaryGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';

class FakeWeeklySummaryGetterEntityManager {
    public $repositories = [];

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }
}

/**
 * @internal
 * @covers \WeeklySummaryGetter
 */
final class WeeklySummaryGetterTest extends TestCase {
    public function testWeeklySummaryGetter(): void {
        $entity_manager = new FakeWeeklySummaryGetterEntityManager();
        $date_utils = new FixedDateUtils('2020-03-21 16:00:00'); // a Saturday
        $logger = new Logger('WeeklySummaryGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getWeeklySummaryNotification([]);

        $this->assertSame(null, $notification);
    }
}
