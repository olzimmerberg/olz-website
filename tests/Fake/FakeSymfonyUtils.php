<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FakeSymfonyUtils extends SymfonyUtils {
    public $error;
    public $output;
    public $commandsCalled = [];

    public function callCommand(
        string $command_name,
        InputInterface $input,
        OutputInterface $output,
    ): void {
        if ($this->error) {
            throw $this->error;
        }
        if ($this->output) {
            $output->writeln($this->output);
        }
        $this->commandsCalled[] = [$command_name, "{$input}"];
    }
}
