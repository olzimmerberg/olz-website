<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Common;

use Olz\Command\Common\OlzCommand;
use Olz\Kernel;
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
    protected function getAllowedAppEnvs(): array {
        return ['test'];
    }

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
        $this->symfonyUtils()->callCommand($command_name, $input, $output);
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
        $command = new OlzCommandForIntegrationTest();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command->testOnlyCallCommand('olz:test', $input, $output);

        $this->assertSame(
            'INFO Running command Olz\Command\TestCommand...',
            $this->getLogs()[0]
        );
        $this->assertMatchesRegularExpression(
            '/^INFO Data path\: .*\/IntegrationTests\/document-root\//',
            $this->getLogs()[1]
        );
        $this->assertSame(
            'INFO Successfully ran command Olz\Command\TestCommand.',
            $this->getLogs()[2]
        );
        $output_string = $output->fetch();
        $this->assertMatchesRegularExpression(
            '/^Data path\: .*\/IntegrationTests\/document-root\//',
            $output_string
        );
        $this->assertSame(
            <<<ZZZZZZZZZZ
            Running command Olz\\Command\\TestCommand...
            {$output_string}Successfully ran command Olz\\Command\\TestCommand.
            ZZZZZZZZZZ,
            str_replace('INFO ', '', implode("\n", $this->getLogs()))
        );
    }
}
