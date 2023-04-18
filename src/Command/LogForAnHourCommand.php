<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:log-for-an-hour')]
class LogForAnHourCommand extends OlzCommand {
    protected function handle(InputInterface $input, OutputInterface $output): int {
        $success = set_time_limit(4000);
        if ($success) {
            $this->log()->info("Successfully set time limit");
        } else {
            $this->log()->warning("Could not set time limit. Let's hope for the best :/");
        }
        for ($i = 0; $i < 360; $i++) {
            $time = $this->dateUtils->getCurrentDateInFormat('H:i:s');
            $this->log()->info("It is {$time}");
            sleep(10);
        }
        $this->log()->info("Successfully wasted an hour!");

        return Command::SUCCESS;
    }
}
