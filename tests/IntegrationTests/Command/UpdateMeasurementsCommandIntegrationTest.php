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
            $job = $this->getSut();
            $job->setDateUtils(new DateUtils('2020-03-13 12:00:00'));
            $input = new ArrayInput([]);
            $output = new BufferedOutput();

            $result = $job->run($input, $output);

            $this->assertSame([
                'INFO Running command Olz\Command\UpdateMeasurementsCommand...',
                'INFO Users & Members measurements updated:',
                'INFO   num_users: 10',
                'INFO   num_members: 8',
                'INFO   num_member_users: 7',
                'INFO Measurements updated.',
                'INFO Successfully ran command Olz\Command\UpdateMeasurementsCommand.',
            ], $this->getLogs());
            $this->assertSame(Command::SUCCESS, $result);
            $this->assertSame(
                "Running command Olz\\Command\\UpdateMeasurementsCommand...\n".
                "Users & Members measurements updated:\n".
                "  num_users: 10\n".
                "  num_members: 8\n".
                "  num_member_users: 7\n".
                "Measurements updated.\n".
                "Successfully ran command Olz\\Command\\UpdateMeasurementsCommand.\n",
                $output->fetch(),
            );

            $entity_manager = $this->getEntityManager();
            $entity_manager->flush();
            $measurement_repo = $entity_manager->getRepository(Measurement::class);

            $this->assertSame(10.0, $measurement_repo->get('num_users', 2020, 3)?->getValue());
            $this->assertSame(8.0, $measurement_repo->get('num_members', 2020, 3)?->getValue());
            $this->assertSame(7.0, $measurement_repo->get('num_member_users', 2020, 3)?->getValue());
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
