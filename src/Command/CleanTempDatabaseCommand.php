<?php

namespace Olz\Command;

use Doctrine\Common\Collections\Criteria;
use Olz\Command\Common\OlzCommand;
use Olz\Entity\AccessToken;
use Olz\Entity\AuthRequest;
use Olz\Entity\Counter;
use Olz\Entity\ForwardedEmail;
use Olz\Entity\Throttling;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:clean-temp-database')]
class CleanTempDatabaseCommand extends OlzCommand {
    protected string $temp_realpath;

    protected \DateTime $three_years_ago;

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $minus_three_years = \DateInterval::createFromDateString("-3 years");
        $this->three_years_ago = $now->add($minus_three_years);

        $this->cleanAccessTokens();
        $this->cleanAuthRequests();
        $this->cleanCounter();
        $this->cleanForwardedEmails();
        $this->cleanThrottlings();

        return Command::SUCCESS;
    }

    protected function cleanAccessTokens(): void {
        $repo = $this->entityManager()->getRepository(AccessToken::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('expires_at', $this->three_years_ago),
            ))
        ;
        $entries = $repo->matching($criteria);
        $num_entries = $entries->count();
        $this->log()->info("Cleaning up {$num_entries} access token entries...");
        foreach ($entries as $entry) {
            $this->entityManager()->remove($entry);
        }
        $this->entityManager()->flush();
    }

    protected function cleanAuthRequests(): void {
        $repo = $this->entityManager()->getRepository(AuthRequest::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('timestamp', $this->three_years_ago),
            ))
        ;
        $entries = $repo->matching($criteria);
        $num_entries = $entries->count();
        $this->log()->info("Cleaning up {$num_entries} auth request entries...");
        foreach ($entries as $entry) {
            $this->entityManager()->remove($entry);
        }
        $this->entityManager()->flush();
    }

    protected function cleanCounter(): void {
        $repo = $this->entityManager()->getRepository(Counter::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('date_range', $this->three_years_ago->format('Y-m-d')),
            ))
        ;
        $entries = $repo->matching($criteria);
        $num_entries = $entries->count();
        $this->log()->info("Cleaning up {$num_entries} counter entries...");
        foreach ($entries as $entry) {
            $this->entityManager()->remove($entry);
        }
        $this->entityManager()->flush();
    }

    protected function cleanForwardedEmails(): void {
        $repo = $this->entityManager()->getRepository(ForwardedEmail::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('forwarded_at', $this->three_years_ago),
            ))
        ;
        $entries = $repo->matching($criteria);
        $num_entries = $entries->count();
        $this->log()->info("Cleaning up {$num_entries} forwarded email entries...");
        foreach ($entries as $entry) {
            $this->entityManager()->remove($entry);
        }
        $this->entityManager()->flush();
    }

    protected function cleanThrottlings(): void {
        $repo = $this->entityManager()->getRepository(Throttling::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('last_occurrence', $this->three_years_ago),
            ))
        ;
        $entries = $repo->matching($criteria);
        $num_entries = $entries->count();
        $this->log()->info("Cleaning up {$num_entries} throttling entries...");
        foreach ($entries as $entry) {
            $this->entityManager()->remove($entry);
        }
        $this->entityManager()->flush();
    }

    protected function unlink(string $path): void {
        unlink($path);
    }

    // @codeCoverageIgnoreEnd
}
