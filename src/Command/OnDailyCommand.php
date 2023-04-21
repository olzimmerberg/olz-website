<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:on-daily')]
class OnDailyCommand extends OlzCommand {
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        set_time_limit(4000);
        ignore_user_abort(true);

        $this->callCommand(
            'olz:clean-temp-directory',
            new ArrayInput([]),
            $output,
        );
        $this->callCommand(
            'olz:sync-solv',
            new ArrayInput([]),
            $output,
        );

        return Command::SUCCESS;
    }
}
