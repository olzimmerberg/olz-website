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
        $is_sport_type_valid = [
            'Run' => true,
            'TrailRun' => true,
            'Hike' => true,
            'Walk' => true,
        ];
        $name_blocklist = [
            'Michael B.' => true,
            'Kamm M.' => true,
            'Sandro A.' => true,
            'Daniel G.' => true,
        ];
        $iso_now = $this->dateUtils()->getIsoNow();
        $now = new \DateTime($iso_now);
        $minus_one_month = \DateInterval::createFromDateString("-30 days");
        $one_month_ago = (new \DateTime($iso_now))->add($minus_one_month);
        $runs_repo = $this->entityManager()->getRepository(RunRecord::class);
        foreach ($strava_links as $strava_link) {
            $this->logAndOutput("Syncing {$strava_link}...");
            $access_token = $this->stravaUtils()->getAccessToken($strava_link);
            if (!$access_token) {
                $this->logAndOutput("{$strava_link} has no access token...", level: 'debug');
                continue;
            }
            $activities = $this->stravaUtils()->callStravaApi('GET', '/clubs/158910/activities', [], $access_token);
            if (!is_array($activities)) {
                $activities_str = var_export($activities, true);
                $this->logAndOutput("Invalid activities fetched: {$activities_str}", level: 'notice');
            }
            $num_activities = count($activities);
            $this->logAndOutput("Fetched {$num_activities} activities...", level: 'debug');
            foreach ($activities as $activity) {
                $activity_json = json_encode($activity) ?: '';
                $this->logAndOutput("Processing activity {$activity_json}...", level: 'debug');
                $firstname = $activity['athlete']['firstname'] ?? null;
                $lastname = $activity['athlete']['lastname'] ?? null;
                $is_name_blocklisted = $name_blocklist["{$firstname} {$lastname}"] ?? false;
                $distance = $activity['distance'] ?? null;
                $moving_time = $activity['moving_time'] ?? null;
                $elapsed_time = $activity['elapsed_time'] ?? null;
                $total_elevation_gain = $activity['total_elevation_gain'] ?? null;
                $type = $activity['type'] ?? '';
                $sport_type = $activity['sport_type'] ?? '';
                if ($firstname === null || $lastname === null || $distance === null || $moving_time === null || $elapsed_time === null || $total_elevation_gain === null) {
                    $this->logAndOutput("Invalid activity {$activity_json}", level: 'notice');
                    continue;
                }
                $is_counting = $is_sport_type_valid[$sport_type] ?? false;
                $pretty_sport_type = "{$type} / {$sport_type}";
                $old_id = md5("{$firstname}-{$lastname}-{$distance}-{$total_elevation_gain}-{$moving_time}-{$elapsed_time}");
                $id = md5("{$firstname}-{$lastname}-{$distance}-{$moving_time}-{$elapsed_time}");
                $old_source = "strava-{$old_id}";
                $source = "strava-{$id}";
                $old_existing = $runs_repo->findOneBy(['source' => $old_source], ['run_at' => 'DESC']);
                $existing = $runs_repo->findOneBy(['source' => $source], ['run_at' => 'DESC']);
                if (
                    ($old_existing !== null && $old_existing->getRunAt() > $one_month_ago)
                    || ($existing !== null && $existing->getRunAt() > $one_month_ago)
                ) {
                    $this->logAndOutput("Duplicate activity {$activity_json}", level: 'debug');
                    continue;
                }
                $this->logAndOutput("New activity: {$source} by {$firstname} {$lastname}");
                $run = new RunRecord();
                $this->entityUtils()->createOlzEntity($run, ['onOff' => !$is_name_blocklisted]);
                $run->setUser(null);
                $run->setRunnerName("{$firstname} {$lastname}");
                $run->setRunAt($now);
                $run->setIsCounting($is_counting);
                $run->setDistanceMeters(intval($distance));
                $run->setElevationMeters(intval($total_elevation_gain));
                $run->setSportType($pretty_sport_type);
                $run->setSource($source);
                $run->setInfo(json_encode($activity) ?: null);
                $this->entityManager()->persist($run);
                $this->entityManager()->flush();
            }
        }
    }
}
