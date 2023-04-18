<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:db-dump')]
class DbDumpCommand extends OlzCommand {
    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->devDataUtils()->dumpDb();
        return Command::SUCCESS;
    }
}
