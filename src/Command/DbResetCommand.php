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
    protected function configure(): void {
        $this->addArgument('mode', InputArgument::REQUIRED,
            'Mode (`content`, `structure` or `full`)');
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $base_href = $this->envUtils()->getBaseHref();
        $olz_app_env = $this->envUtils()->getAppEnv();
        $symfony_app_env = $_ENV['APP_ENV'] ?? 'prod';
        if (
            $base_href === 'https://olzimmerberg.ch'
            || $olz_app_env === 'prod'
            || $symfony_app_env === 'prod'
        ) {
            $this->log()->notice('Tried to reset prod database!');
            $output->writeln("Do NOT reset the prod database!");
            return Command::INVALID;
        }
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
