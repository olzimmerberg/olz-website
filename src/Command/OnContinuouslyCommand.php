<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\Throttling;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:on-continuously')]
class OnContinuouslyCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        set_time_limit(4000);
        ignore_user_abort(true);

        $this->logAndOutput("Running continuously...", level: 'debug');
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('on_continuously', $this->dateUtils()->getIsoNow());

        $this->logAndOutput("Continuously processing email...", level: 'debug');
        $this->symfonyUtils()->callCommand(
            'olz:process-email',
            new ArrayInput([]),
            $output,
        );

        $this->daily('01:00:00', 'clean-temp-directory', function () use ($output) {
            $this->symfonyUtils()->callCommand(
                'olz:clean-temp-directory',
                new ArrayInput([]),
                $output,
            );
        });
        $this->daily('01:05:00', 'clean-temp-database', function () use ($output) {
            $this->symfonyUtils()->callCommand(
                'olz:clean-temp-database',
                new ArrayInput([]),
                $output,
            );
        });
        $this->daily('01:10:00', 'clean-logs', function () use ($output) {
            $this->symfonyUtils()->callCommand(
                'olz:clean-logs',
                new ArrayInput([]),
                $output,
            );
        });
        $this->daily('01:15:00', 'send-telegram-configuration', function () use ($output) {
            $this->symfonyUtils()->callCommand(
                'olz:send-telegram-configuration',
                new ArrayInput([]),
                $output,
            );
        });
        $this->daily('01:20:00', 'sync-solv', function () use ($output) {
            $this->symfonyUtils()->callCommand(
                'olz:sync-solv',
                new ArrayInput([]),
                $output,
            );
        });

        // TODO: Remove this again!
        $this->daily('01:25:00', 'send-test-email', function () use ($output) {
            $this->symfonyUtils()->callCommand(
                'olz:send-test-email',
                new ArrayInput([]),
                $output,
            );
        });

        $this->daily('16:27:00', 'send-daily-notifications', function () use ($output) {
            $this->symfonyUtils()->callCommand(
                'olz:send-daily-notifications',
                new ArrayInput([]),
                $output,
            );
        });

        $this->logAndOutput("Stopping workers...", level: 'debug');
        $this->symfonyUtils()->callCommand(
            'messenger:stop-workers',
            new ArrayInput([]),
            $output,
        );
        $this->logAndOutput("Consume messages...", level: 'debug');
        $this->symfonyUtils()->callCommand(
            'messenger:consume',
            new ArrayInput([
                'receivers' => ['async'],
                '--no-reset' => '--no-reset',
            ]),
            $output,
        );

        $this->logAndOutput("Ran continuously.", level: 'debug');
        return Command::SUCCESS;
    }

    /** @param callable(): void $fn */
    public function daily(string $time, string $ident, callable $fn): void {
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $last_occurrence = $throttling_repo->getLastOccurrenceOf($ident);
        $iso_now = $this->dateUtils()->getIsoNow();
        $is_too_soon = false;
        if ($last_occurrence) {
            $now = new \DateTime($iso_now);
            // Consider daylight saving change date => not 23 hours!
            $min_interval = \DateInterval::createFromDateString('+22 hours');
            $min_now = $last_occurrence->add($min_interval);
            $is_too_soon = $now < $min_now;
        }
        $is_right_time_of_day = $this->dateUtils()->getCurrentDateInFormat('H:i:s') >= $time;
        $should_execute_now = !$is_too_soon && $is_right_time_of_day;
        if ($should_execute_now) {
            try {
                $this->logAndOutput("Executing daily ({$time}) {$ident}...", level: 'debug');
                $fn();
                $throttling_repo->recordOccurrenceOf($ident, $this->dateUtils()->getIsoNow());
            } catch (\Throwable $th) {
                $this->logAndOutput("Daily ({$time}) {$ident} failed", level: 'error');
            }
        }
    }
}
