<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:onDaily')]
class OnDailyCommand extends OlzCommand {
    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->callCommand(
            'olz:test',
            new ArrayInput([]),
            $output,
        );
        $this->callCommand(
            'olz:cleanTempDirectory',
            new ArrayInput([]),
            $output,
        );

        return Command::SUCCESS;
    }
}
