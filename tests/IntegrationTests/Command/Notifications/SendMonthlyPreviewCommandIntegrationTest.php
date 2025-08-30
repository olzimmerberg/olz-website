<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Notifications;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\Notifications\SendMonthlyPreviewCommand;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\EnvUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendMonthlyPreviewCommand
 */
final class SendMonthlyPreviewCommandIntegrationTest extends IntegrationTestCase {
    public function testSendMonthlyPreviewCommand(): void {
        $entityManager = $this->getEntityManager();
        $date_utils = new DateUtils('2020-07-18 16:00:00'); // the second last Saturday of the month
        $user = FakeUser::defaultUser();

        $job = new SendMonthlyPreviewCommand();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Im August haben wir Folgendes auf dem Programm:


            **Termine**

            - Di, 04.08.: [Training -1](http://integration-test.host/termine/9)
            - Di, 11.08.: [Trainingsstart](http://integration-test.host/termine/8)
            - So, 16.08. - Mo, 17.08.: [24h-OL](http://integration-test.host/termine/12)
            - Di, 18.08.: [Training 1](http://integration-test.host/termine/3)
            - Sa, 22.08.: [Grossanlass](http://integration-test.host/termine/10)
            - Di, 25.08.: [Training 2](http://integration-test.host/termine/4)
            - Mi, 26.08.: [Milchsuppen-Cup, OLZ Trophy 4. Lauf](http://integration-test.host/termine/5)


            **Meldeschlüsse**

            - Mo, 17.08.: Meldeschluss für '[Training 1](http://integration-test.host/termine/3)'
            - Mo, 24.08.: Meldeschluss für '[Training 2](http://integration-test.host/termine/4)'
            - Mo, 31.08.: Meldeschluss für '[Training 3](http://integration-test.host/termine/6)'


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Monatsvorschau August', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    protected function getEntityManager(): EntityManagerInterface {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
