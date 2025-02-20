<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:check-termine-solv-id')]
class CheckTermineSolvIdCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function configure(): void {
        $this->addOption('year', null, InputOption::VALUE_REQUIRED, 'Year');
        $this->addOption(
            'future',
            null,
            InputOption::VALUE_NONE,
            'Only run for future Termine.'
        );
        $this->addOption(
            'recent',
            null,
            InputOption::VALUE_NONE,
            'Only run for recently modified Termine.'
        );
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $conditions = [];
        $year = $input->getOption('year');
        if ($year) {
            $sane_year = intval($year);
            $conditions[] = "YEAR(start_date) = '{$sane_year}'";
        }
        $future = $input->getOption('future');
        if ($future) {
            $now = $this->dateUtils()->getCurrentDateInFormat('Y-m-d');
            $conditions[] = "start_date > '{$now}'";
        }
        $recent = $input->getOption('recent');
        if ($recent) {
            $now = $this->dateUtils()->getCurrentDateInFormat('Y-m-d');
            $recently = date('Y-m-d H:i:s', (strtotime($now) ?: 0) - 86400 - 3600);
            $conditions[] = "last_modified_at > '{$recently}'";
        }

        $db = $this->dbUtils()->getDb();
        $sql_where = implode(' AND ', $conditions);
        $this->logAndOutput("Running with {$sql_where}");
        $result = $db->query("SELECT * FROM termine WHERE {$sql_where} AND solv_uid IS NULL");
        // @phpstan-ignore-next-line
        while ($row = $result->fetch_assoc()) {
            $start_date = $row['start_date']; // TODO: Improve precision for multi-day events?
            $title = $row['title'];
            $id = $row['id'];
            $result_solv = $db->query("SELECT * FROM solv_events WHERE `date` = '{$start_date}'");
            $this->logAndOutput("Termin: {$start_date} {$title} ({$id})");
            // @phpstan-ignore-next-line
            while ($row_solv = $result_solv->fetch_assoc()) {
                $solv_date = $row_solv['date'];
                $solv_name = $row_solv['name'];
                $solv_uid = $row_solv['solv_uid'];
                // @phpstan-ignore-next-line
                $levenshtein = levenshtein($title, $solv_name, 1, 2, 1);
                // @phpstan-ignore-next-line
                $num_same = strlen($title) + strlen($solv_name) - $levenshtein;
                $this->logAndOutput("  SOLV-Event: {$solv_date} {$solv_name} ({$solv_uid}) - Diff: {$num_same} (LEV: {$levenshtein})");
            }
        }
        return Command::SUCCESS;
    }
}
