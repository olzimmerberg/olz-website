<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/SolvEvent.php';
require_once __DIR__.'/../../../../src/termine/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/DeadlineWarningGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \DeadlineWarningGetter
 */
final class DeadlineWarningGetterIntegrationTest extends IntegrationTestCase {
    public function testDeadlineWarningGetter(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../src/config/doctrine_db.php';

        $date_utils = new FixedDateUtils('2020-08-15 19:30:00');
        $logger = new Logger('DeadlineWarningGetterIntegrationTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification(['days' => 2]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Folgende Meldeschlüsse stehen bevor:
        
        - 17.08.: Meldeschluss für '[Grossanlass](http://integration-test.host/_/termine.php#id10)'

        ZZZZZZZZZZ;
        $this->assertSame('Meldeschlusswarnung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
