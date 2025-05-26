<?php

namespace Olz\Command;

use Doctrine\Common\Collections\Criteria;
use Olz\Command\Common\OlzCommand;
use Olz\Entity\AuthRequest;
use Olz\Entity\Counter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:clean-temp-database')]
class CleanTempDatabaseCommand extends OlzCommand {
    protected string $temp_realpath;

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $minus_three_years = \DateInterval::createFromDateString("-3 years");
        $three_years_ago = $now->add($minus_three_years);

        // Remove access tokens
        $auth_request_repo = $this->entityManager()->getRepository(AuthRequest::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('timestamp', $three_years_ago),
            ))
        ;
        $auth_requests = $auth_request_repo->matching($criteria);
        $num_auth_requests = $auth_requests->count();
        $this->log()->info("Cleaning up {$num_auth_requests} auth request entries...");
        foreach ($auth_requests as $auth_request) {
            $this->entityManager()->remove($auth_request);
        }
        $this->entityManager()->flush();

        // Remove counter
        $counter_repo = $this->entityManager()->getRepository(Counter::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('date_range', $three_years_ago->format('Y-m-d')),
            ))
        ;
        $counters = $counter_repo->matching($criteria);
        $num_counters = $counters->count();
        $this->log()->info("Cleaning up {$num_counters} counter entries...");
        foreach ($counters as $counter) {
            $this->entityManager()->remove($counter);
        }
        $this->entityManager()->flush();

        return Command::SUCCESS;
    }

    protected function unlink(string $path): void {
        unlink($path);
    }

    // @codeCoverageIgnoreEnd
}
