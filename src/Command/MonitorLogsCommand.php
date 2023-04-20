<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:get-id-algos')]
class MonitorLogsCommand extends OlzCommand {
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        require_once __DIR__.'/../../_/tools/monitoring/logs_monitoring.php';
        ob_start();
        logs_monitoring();
        $contents = ob_get_contents();
        ob_end_clean();
        $output->writeln($contents);
        return Command::SUCCESS;
    }
}
