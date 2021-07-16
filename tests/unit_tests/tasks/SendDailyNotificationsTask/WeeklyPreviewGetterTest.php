<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/WeeklyPreviewGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeWeeklyPreviewGetterSolvEventRepository {
    public function matching($criteria) {
        if (preg_match('/2021-03-18/', var_export($criteria, true))) {
            return [];
        }
        $solv_event = new SolvEvent();
        $solv_event->setSolvUid(123);
        $solv_event->setDeadline(new DateTime('2020-04-25 19:30:00'));
        $solv_event_without_termin = new SolvEvent();
        $solv_event_without_termin->setSolvUid(321);
        $solv_event_without_termin->setDeadline(new DateTime('2020-04-24 19:30:00'));
        return [$solv_event, $solv_event_without_termin];
    }
}

class FakeWeeklyPreviewGetterTerminRepository {
    public function matching($criteria) {
        if (preg_match('/2021-03-18/', var_export($criteria, true))) {
            return [];
        }
        $termin = new Termin();
        $termin->setId(1);
        $termin->setStartsOn(new DateTime('2020-04-24 19:30:00'));
        $termin->setTitle('Test Termin');
        $range_termin = new Termin();
        $range_termin->setId(2);
        $range_termin->setStartsOn(new DateTime('2020-04-28'));
        $range_termin->setEndsOn(new DateTime('2020-04-29'));
        $range_termin->setTitle('End of Week');
        return [$termin, $range_termin];
    }

    public function findOneBy($where) {
        if ($where == ['solv_uid' => 123, 'on_off' => 1]) {
            $termin = new Termin();
            $termin->setId(3);
            $termin->setStartsOn(new DateTime('2020-04-18 19:30:00'));
            $termin->setTitle('Termin mit Meldeschluss');
            return $termin;
        }
    }
}

class FakeWeeklyPreviewGetterEnvUtils {
    public function getBaseHref() {
        return 'http://fake-base-url';
    }

    public function getCodeHref() {
        return '/_/';
    }
}

/**
 * @internal
 * @covers \WeeklyPreviewGetter
 */
final class WeeklyPreviewGetterTest extends UnitTestCase {
    public function testWeeklyPreviewGetterOnWrongWeekday(): void {
        $entity_manager = new FakeEntityManager();
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
        $entity_manager = new FakeEntityManager();
        $termin_repo = new FakeWeeklyPreviewGetterTerminRepository();
        $entity_manager->repositories['Termin'] = $termin_repo;
        $solv_event_repo = new FakeWeeklyPreviewGetterSolvEventRepository();
        $entity_manager->repositories['SolvEvent'] = $solv_event_repo;
        $date_utils = new FixedDateUtils('2020-03-19 16:00:00'); // a Thursday
        $env_utils = new FakeWeeklyPreviewGetterEnvUtils();
        $logger = new Logger('WeeklyPreviewGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getWeeklyPreviewNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


        **Termine**
        
        - 24.04.: [Test Termin](http://fake-base-url/_/termine.php#id1)
        - 28.04. - 29.04.: [End of Week](http://fake-base-url/_/termine.php#id2)
        
        
        **Meldeschlüsse**
        
        - 25.04.: Meldeschluss für '[Termin mit Meldeschluss](http://fake-base-url/_/termine.php#id3)'


        ZZZZZZZZZZ;
        $this->assertSame('Vorschau auf die Woche vom 23. März', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptyWeeklyPreviewGetter(): void {
        $entity_manager = new FakeEntityManager();
        $termin_repo = new FakeWeeklyPreviewGetterTerminRepository();
        $entity_manager->repositories['Termin'] = $termin_repo;
        $solv_event_repo = new FakeWeeklyPreviewGetterSolvEventRepository();
        $entity_manager->repositories['SolvEvent'] = $solv_event_repo;
        $date_utils = new FixedDateUtils('2021-03-18 16:00:00'); // a Thursday
        $env_utils = new FakeWeeklyPreviewGetterEnvUtils();
        $logger = new Logger('WeeklyPreviewGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklyPreviewGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getWeeklyPreviewNotification([]);

        $this->assertSame(null, $notification);
    }
}
