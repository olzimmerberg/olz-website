<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter
 */
final class WeeklyPreviewGetterIntegrationTest extends IntegrationTestCase {
    public function testWeeklyPreviewGetter(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-08-13 16:00:00'); // a Thursday
        $logger = Fake\FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklyPreviewGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getWeeklyPreviewNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


        **Termine**
        
        - 16.08. - 17.08.: [24h-OL](http://integration-test.host/termine/12)
        - 18.08.: [Training 1](http://integration-test.host/termine/3)
        - 22.08.: [Grossanlass](http://integration-test.host/termine/10)

        
        **Meldeschlüsse**

        - 17.08.: Meldeschluss für '[Grossanlass](http://integration-test.host/termine/10)'
        - 17.08.: Meldeschluss für '[Training 1](http://integration-test.host/termine/3)'


        ZZZZZZZZZZ;
        $this->assertSame([
        ], $logger->handler->getPrettyRecords());
        $this->assertSame('Vorschau auf die Woche vom 17. August', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
