<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Common;

use Olz\Command\Common\OlzCommand;
use Olz\Kernel;
use Olz\Tests\Fake;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class OlzCommandForIntegrationTest extends OlzCommand {
    public $returnCode;
    public $failWithError;

    protected function handle(InputInterface $input, OutputInterface $output): int {
        throw new \Exception('not implemented');
    }

    public function testOnlyCallCommand(
        string $command_name,
        InputInterface $input,
        OutputInterface $output,
    ): void {
        $this->callCommand($command_name, $input, $output);
    }

    public function getApplication(): Application {
        return new Application(new Kernel('dev', true));
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\Common\OlzCommand
 */
final class OlzCommandIntegrationTest extends IntegrationTestCase {
    public function testOlzCommandCallCommand(): void {
        $logger = Fake\FakeLogger::create();
        $command = new OlzCommandForIntegrationTest();
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command->testOnlyCallCommand('olz:test', $input, $output);

        $this->assertSame([], $logger->handler->getPrettyRecords());
        $this->assertMatchesRegularExpression(
            '/^Data path\: .*\/IntegrationTests\/document-root\//',
            $output->fetch()
        );
    }
}
