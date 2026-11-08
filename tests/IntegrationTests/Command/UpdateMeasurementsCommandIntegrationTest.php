<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\UpdateMeasurementsCommand;
use Olz\Common\Utils\DateUtils;
use Olz\Entity\Measurement;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\UpdateMeasurementsCommand
 */
final class UpdateMeasurementsCommandIntegrationTest extends IntegrationTestCase {
    public function testRun(): void {
        $this->withLockedDb(function () {
            $entity_manager = $this->getEntityManager();
            $measurement_repo = $entity_manager->getRepository(Measurement::class);
            foreach ($measurement_repo->findAll() as $measurement) {
                $measurement->setVersion(UpdateMeasurementsCommand::CALCULATION_VERSION);
            }
            $entity_manager->flush();

            $job = $this->getSut();
            $job->setDateUtils(new DateUtils('2020-04-15 12:00:00'));
            $input = new ArrayInput([]);
            $output = new BufferedOutput();

            $result = $job->run($input, $output);

            $this->assertSame([
                'INFO Running command Olz\Command\UpdateMeasurementsCommand...',
                'INFO Updated measurements for 2020-03',
                'INFO Updated measurements for 2020-04',
                'INFO Successfully ran command Olz\Command\UpdateMeasurementsCommand.',
            ], $this->getLogs());
            $this->assertSame(Command::SUCCESS, $result);
            $this->assertSame(
                "Running command Olz\\Command\\UpdateMeasurementsCommand...\n".
                "Updated measurements for 2020-03\n".
                "Updated measurements for 2020-04\n".
                "Successfully ran command Olz\\Command\\UpdateMeasurementsCommand.\n",
                $output->fetch(),
            );

            $entity_manager->flush();

            // The previous month is complete: all measurements are stored.
            // Note: In the fixtures, all users were created on 2021-12-01 (migration) and no
            // user has `joined_on` set, so there are no users in 2020, but there are members.
            $this->assertSame(0.0, $measurement_repo->get('num_users', 2020, 3)?->getValue());
            $this->assertSame(8.0, $measurement_repo->get('num_members', 2020, 3)?->getValue());
            $this->assertSame(0.0, $measurement_repo->get('num_member_users', 2020, 3)?->getValue());
            $this->assertSame(0.0, $measurement_repo->get('num_termine', 2020, 3)?->getValue());
            $this->assertSame(0.0, $measurement_repo->get('num_solv_participations', 2020, 3)?->getValue());
            $this->assertSame(0.0, $measurement_repo->get('avg_training_participants', 2020, 3)?->getValue());
            $this->assertSame(1.0, $measurement_repo->get('num_news', 2020, 3)?->getValue());

            // The current month is still in progress: only snapshot measurements are stored.
            $this->assertSame(0.0, $measurement_repo->get('num_users', 2020, 4)?->getValue());
            $this->assertSame(8.0, $measurement_repo->get('num_members', 2020, 4)?->getValue());
            $this->assertSame(0.0, $measurement_repo->get('num_member_users', 2020, 4)?->getValue());
            $this->assertSame(0.0, $measurement_repo->get('num_termine', 2020, 4)?->getValue());
            $this->assertNull($measurement_repo->get('num_solv_participations', 2020, 4));
            $this->assertNull($measurement_repo->get('avg_training_participants', 2020, 4));
            $this->assertNull($measurement_repo->get('num_news', 2020, 4));
            $this->assertSame(
                UpdateMeasurementsCommand::CALCULATION_VERSION,
                $measurement_repo->get('num_users', 2020, 4)->getVersion(),
            );
        });
    }

    protected function getSut(): UpdateMeasurementsCommand {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(UpdateMeasurementsCommand::class);
    }

    protected function getEntityManager(): EntityManagerInterface {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
