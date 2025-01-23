<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter
 */
final class MonthlyPreviewGetterIntegrationTest extends IntegrationTestCase {
    public function testMonthlyPreviewGetter(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-07-18 16:00:00'); // the second last Saturday of the month
        $user = FakeUser::defaultUser();

        $job = new MonthlyPreviewGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Im August haben wir Folgendes auf dem Programm:


            **Termine**

            - 04.08.: [Training -1](http://integration-test.host/termine/9)
            - 11.08.: [Training 0](http://integration-test.host/termine/8)
            - 16.08. - 17.08.: [24h-OL](http://integration-test.host/termine/12)
            - 18.08.: [Training 1](http://integration-test.host/termine/3)
            - 22.08.: [Grossanlass](http://integration-test.host/termine/10)
            - 25.08.: [Training 2](http://integration-test.host/termine/4)
            - 26.08.: [Milchsuppen-Cup, OLZ Trophy 4. Lauf](http://integration-test.host/termine/5)


            **Meldeschlüsse**

            - 17.08.: Meldeschluss für '[Training 1](http://integration-test.host/termine/3)'
            - 24.08.: Meldeschluss für '[Training 2](http://integration-test.host/termine/4)'
            - 31.08.: Meldeschluss für '[Training 3](http://integration-test.host/termine/6)'


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Monatsvorschau August', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
