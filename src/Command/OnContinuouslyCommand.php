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

        if ($this->shouldSendDailyMailNow()) {
            $this->logAndOutput("Sending daily mail at {$this->dateUtils()->getIsoNow()}...", level: 'debug');
            $throttling_repo->recordOccurrenceOf('daily_notifications', $this->dateUtils()->getIsoNow());

            $this->symfonyUtils()->callCommand(
                'olz:send-daily-notifications',
                new ArrayInput([]),
                $output,
            );
        }

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

    public function shouldSendDailyMailNow(): bool {
        $daily_notifications_time = '16:27:00';
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $last_daily_notifications = $throttling_repo->getLastOccurrenceOf('daily_notifications');
        $is_too_soon = false;
        if ($last_daily_notifications) {
            $now = new \DateTime($this->dateUtils()->getIsoNow());
            // Consider daylight saving change date => not 23 hours!
            $min_interval = \DateInterval::createFromDateString('+22 hours');
            $min_now = $last_daily_notifications->add($min_interval);
            $is_too_soon = $now < $min_now;
        }
        $is_right_time_of_day = $this->dateUtils()->getCurrentDateInFormat('H:i:s') >= $daily_notifications_time;
        return !$is_too_soon && $is_right_time_of_day;
    }
}
