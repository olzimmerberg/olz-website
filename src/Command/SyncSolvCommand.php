<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$solv_maintainer_email = 'simon.hatt@olzimmerberg.ch';

#[AsCommand(name: 'olz:sync-solv')]
class SyncSolvCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->syncSolvEvents();
        $this->syncSolvResults();
        $this->assignSolvPeople();
        $this->mergeSolvPeople();
        return Command::SUCCESS;
    }

    private function syncSolvEvents() {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $current_day = intval($this->dateUtils()->getCurrentDateInFormat('d'));
        $this->syncSolvEventForYear($current_year);
        if ($current_day !== 1) { // Only do the following once a month.
            return;
        }
        $this->syncSolvEventForYear($current_year - 1);
        $this->syncSolvEventForYear($current_year + 1);
        $this->syncSolvEventForYear($current_year - 2);
    }

    private function syncSolvEventForYear(int $year): void {
        try {
            $this->symfonyUtils()->callCommand(
                'olz:sync-solv-events',
                new ArrayInput(['year' => strval($year)]),
                $this->output,
            );
        } catch (\Throwable $th) {
            $this->logAndOutput("olz:sync-solv-events({$year}) failed.", [$th], level: 'warning');
        }
    }

    private function syncSolvResults() {
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        try {
            $this->symfonyUtils()->callCommand(
                'olz:sync-solv-results',
                new ArrayInput(['year' => strval($current_year)]),
                $this->output,
            );
        } catch (\Throwable $th) {
            $this->logAndOutput("olz:sync-solv-results({$current_year}) failed. {$th}", [$th], level: 'warning');
        }
    }

    private function assignSolvPeople() {
        try {
            $this->symfonyUtils()->callCommand(
                'olz:sync-solv-assign-people',
                new ArrayInput([]),
                $this->output,
            );
        } catch (\Throwable $th) {
            $this->logAndOutput("olz:sync-solv-assign-people() failed.", [$th], level: 'warning');
        }
    }

    private function mergeSolvPeople() {
        try {
            $this->symfonyUtils()->callCommand(
                'olz:sync-solv-merge-people',
                new ArrayInput([]),
                $this->output,
            );
        } catch (\Throwable $th) {
            $this->logAndOutput("olz:sync-solv-merge-people() failed.", [$th], level: 'warning');
        }
    }
}
