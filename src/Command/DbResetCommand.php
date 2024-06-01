<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:db-reset')]
class DbResetCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging'];
    }

    protected function configure(): void {
        $this->addArgument(
            'mode',
            InputArgument::REQUIRED,
            'Mode (`content`, `structure` or `full`)'
        );
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $mode = $input->getArgument('mode');
        if ($mode === 'content') {
            $this->devDataUtils()->resetDbContent();
        } elseif ($mode === 'structure') {
            $this->devDataUtils()->resetDbStructure();
        } elseif ($mode === 'full') {
            $this->devDataUtils()->fullResetDb();
        } else {
            $output->writeln("Invalid mode: {$mode}.");
            return Command::INVALID;
        }
        $output->writeln("Database {$mode} reset successful.");
        return Command::SUCCESS;
    }
}
