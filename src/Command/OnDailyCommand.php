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
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        set_time_limit(4000);
        ignore_user_abort(true);

        $this->symfonyUtils()->callCommand(
            'olz:clean-temp-directory',
            new ArrayInput([]),
            $output,
        );
        $this->symfonyUtils()->callCommand(
            'olz:send-telegram-configuration',
            new ArrayInput([]),
            $output,
        );
        $this->symfonyUtils()->callCommand(
            'olz:sync-solv',
            new ArrayInput([]),
            $output,
        );

        // TODO: Remove this again!
        $this->symfonyUtils()->callCommand(
            'olz:send-test-email',
            new ArrayInput([]),
            $output,
        );

        return Command::SUCCESS;
    }
}
