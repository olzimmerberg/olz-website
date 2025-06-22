<?php

namespace Olz\Command;

use Olz\Apps\Logs\Utils\DailyFileLogsChannel;
use Olz\Apps\Logs\Utils\LogsDefinitions;
use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:clean-logs')]
class CleanLogsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $channels = LogsDefinitions::getLogsChannels();
        foreach ($channels as $channel) {
            if ($channel instanceof DailyFileLogsChannel) {
                $this->log()->info("Cleaning logs channel {$channel->getName()} ({$channel->getId()})...");
                $channel->optimizeHybridFiles();
                $channel->cleanUpOldFiles();
            } else {
                $this->log()->info("Nothing to do cleaning logs channel {$channel->getName()} ({$channel->getId()}).");
            }
        }
        return Command::SUCCESS;
    }
}
