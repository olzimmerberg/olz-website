<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/Aktuell.php';
require_once __DIR__.'/../../../../src/model/Blog.php';
require_once __DIR__.'/../../../../src/model/Forum.php';
require_once __DIR__.'/../../../../src/model/Galerie.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/DailySummaryGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \DailySummaryGetter
 */
final class DailySummaryGetterIntegrationTest extends IntegrationTestCase {
    public function testDailySummaryGetter(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../src/config/doctrine_db.php';

        $date_utils = new FixedDateUtils('2020-08-15 12:51:00'); // a Saturday
        $logger = new Logger('DailySummaryGetterIntegrationTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
        ]);

        // TODO: Populate test data
        $this->assertSame(null, $notification);
    }
}
