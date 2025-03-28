<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Common;

use Olz\Command\TestCommand;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\Common\OlzCommand
 */
final class OlzCommandIntegrationTest extends IntegrationTestCase {
    public function testOlzCommandCallCommand(): void {
        $command = $this->getSut();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command->run($input, $output);

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
            '/Data path\: .*\/IntegrationTests\/document-root\//',
            $output_string
        );
    }

    protected function getSut(): TestCommand {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(TestCommand::class);
    }
}
