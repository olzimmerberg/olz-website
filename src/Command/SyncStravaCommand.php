<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\Anniversary\RunRecord;
use Olz\Entity\StravaLink;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:sync-strava')]
class SyncStravaCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function configure(): void {
        $this->addArgument('year', InputArgument::REQUIRED, 'Year (YYYY)');
        $this->addArgument('user', InputArgument::OPTIONAL, 'User ID');
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $year = $input->getArgument('year');
        if (!preg_match('/^[0-9]{4}$/', $year) || intval($year) < 1996) {
            $this->logAndOutput("Invalid year: {$year}. Must be in format YYYY and 1996 or later.", level: 'notice');
            return Command::INVALID;
        }
        $year = intval($year);
        $user_id = $input->getArgument('user');
        if ($user_id === null) {
            $this->syncStravaForYear($year);
            return Command::SUCCESS;
        }
        $int_user_id = $user_id ? intval($user_id) : null;
        if (!preg_match('/^[0-9]+$/', $user_id) || intval($user_id) < 1 || !$int_user_id) {
            $this->logAndOutput("Invalid user: {$user_id}. Must be a positive integer.", level: 'notice');
            return Command::INVALID;
        }
        $this->syncStravaForUserForYear($int_user_id, $year);
        return Command::SUCCESS;
    }

    protected function syncStravaForYear(int $year): void {
        $this->logAndOutput("Syncing Strava for {$year}...");

        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
        $strava_links = $strava_link_repo->findAll();

        $this->syncStravaLinks($strava_links);
    }

    protected function syncStravaForUserForYear(?int $user_id, int $year): void {
        $this->logAndOutput("Syncing Strava for user {$user_id} for {$year}...");

        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
        $strava_links = $strava_link_repo->findBy(['user' => $user_id]);

        $this->syncStravaLinks($strava_links);
    }

    /** @param array<StravaLink> $strava_links */
    protected function syncStravaLinks(array $strava_links): void {
        $min_date_iso = '2025-11-01T00:00:00Z';
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $runs_repo = $this->entityManager()->getRepository(RunRecord::class);
        foreach ($strava_links as $strava_link) {
            $this->logAndOutput("Syncing {$strava_link}...");
            $user = $strava_link->getUser();
            $access_token = $this->stravaUtils()->getAccessToken($strava_link);
            if (!$access_token) {
                $this->logAndOutput("{$strava_link} has no access token...", level: 'debug');
                continue;
            }
            $activities = $this->stravaUtils()->callStravaApi('GET', '/athlete/activities', [], $access_token);
            foreach ($activities as $activity) {
                if ($activity['start_date_local'] < $min_date_iso) {
                    continue;
                }
                $source = "strava-{$activity['id']}";
                $existing = $runs_repo->findOneBy(['source' => $source]);
                if ($existing !== null) {
                    continue;
                }
                $run = new RunRecord();
                $run->setUser($user);
                $run->setRunAt(new \DateTime($activity['start_date_local']));
                $run->setDistanceMeters(intval($activity['distance']));
                $run->setElevationMeters(intval($activity['total_elevation_gain']));
                $run->setSource($source);
                $run->setCreatedAt($now);
                $this->entityManager()->persist($run);
                $this->entityManager()->flush();
            }
        }
    }
}
