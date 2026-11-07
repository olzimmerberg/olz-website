<?php

namespace Olz\Command;

use Doctrine\Common\Collections\Criteria;
use Olz\Command\Common\OlzCommand;
use Olz\Entity\Measurement;
use Olz\Entity\Members\Member;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\SolvEvent;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminReaction;
use Olz\Entity\Users\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:update-measurements')]
class UpdateMeasurementsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $member_repo = $this->entityManager()->getRepository(Member::class);
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin_reaction_repo = $this->entityManager()->getRepository(TerminReaction::class);
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $weekly_picture_repo = $this->entityManager()->getRepository(WeeklyPicture::class);

        $num_users = count($user_repo->findBy(['on_off' => 1]));

        $active_members = $member_repo->findBy(['on_off' => 1]);
        $num_members = count($active_members);
        $num_member_users = 0;
        foreach ($active_members as $member) {
            $user = $member->getUser();
            if ($user !== null && $user->getOnOff() === 1) {
                $num_member_users++;
            }
        }

        $year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $month = intval($this->dateUtils()->getCurrentDateInFormat('m'));
        $prev_year = $month > 1 ? $year : $year - 1;
        $prev_month = $month > 1 ? $month - 1 : 12;
        $next_year = $month < 12 ? $year : $year + 1;
        $next_month = $month < 12 ? $month + 1 : 1;

        $current_start = new \DateTime(sprintf('%04d-%02d-01 00:00:00', $year, $month));
        $next_start = new \DateTime(sprintf('%04d-%02d-01 00:00:00', $next_year, $next_month));
        $prev_start = new \DateTime(sprintf('%04d-%02d-01 00:00:00', $prev_year, $prev_month));

        // Termine: aktueller Monat (Snapshot, unter aktuellem Monat).
        $termine = [...$termin_repo->matching($this->getMonthCriteria('start_date', $current_start, $next_start))];
        $num_termine = count($termine);

        // Trainings (Kartentrainings): Vormonat, Durchschnitt Teilnehmer/Training.
        $prev_termine = [...$termin_repo->matching($this->getMonthCriteria('start_date', $prev_start, $current_start))];
        $training_termin_ids = [];
        foreach ($prev_termine as $termin) {
            foreach ($termin->getLabels() as $label) {
                if ($label->getIdent() === 'training') {
                    $termin_id = $termin->getId();
                    if ($termin_id !== null) {
                        $training_termin_ids[] = $termin_id;
                    }
                    break;
                }
            }
        }
        $num_trainings = count($training_termin_ids);
        $sum_training_participants = 0;
        foreach ($training_termin_ids as $termin_id) {
            $user_ids = [];
            $reactions = $termin_reaction_repo->findBy(['termin' => $termin_id]);
            foreach ($reactions as $reaction) {
                if (in_array($reaction->getEmoji(), TerminReaction::ACCEPT_EMOJIS, true)) {
                    $user_id = $reaction->getUser()->getId();
                    if ($user_id !== null) {
                        $user_ids[$user_id] = true;
                    }
                }
            }
            $sum_training_participants += count($user_ids);
        }
        $avg_training_participants = $num_trainings > 0
            ? $sum_training_participants / $num_trainings
            : 0.0;

        // SOLV-Anlässe: Vormonat, Teilnahmen unserer Mitglieder (solv_people.member).
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);
        $solv_person_repo = $this->entityManager()->getRepository(SolvPerson::class);
        $solv_result_repo = $this->entityManager()->getRepository(SolvResult::class);
        $member_person_ids = [];
        foreach ($solv_person_repo->findBy([]) as $solv_person) {
            $person_id = $solv_person->getId();
            if ($person_id !== null && $solv_person->getMember() > 0) {
                $member_person_ids[$person_id] = true;
            }
        }
        $prev_month_key = sprintf('%04d-%02d', $prev_year, $prev_month);
        $solv_num = 0;
        $solv_distance = 0;
        $solv_elevation = 0;
        foreach ($solv_event_repo->getSolvEventsForYear($prev_year) as $solv_event) {
            if ($solv_event->getDate()->format('Y-m') !== $prev_month_key) {
                continue;
            }
            $solv_results = $solv_result_repo->findBy(['event' => $solv_event->getSolvUid()]);
            foreach ($solv_results as $solv_result) {
                if (!isset($member_person_ids[$solv_result->getPerson()])) {
                    continue;
                }
                $solv_num++;
                $solv_distance += $solv_result->getClassDistance();
                $solv_elevation += $solv_result->getClassElevation();
            }
        }

        // Website-Aktivität: Vormonat.
        $num_news = count($news_repo->matching($this->getMonthCriteria('published_date', $prev_start, $current_start)));
        $num_weekly_pictures = count($weekly_picture_repo->matching($this->getMonthCriteria('datum', $prev_start, $current_start)));

        $measurement_repo = $this->entityManager()->getRepository(Measurement::class);
        $measurement_repo->set('num_users', $year, $month, $num_users);
        $measurement_repo->set('num_members', $year, $month, $num_members);
        $measurement_repo->set('num_member_users', $year, $month, $num_member_users);
        $measurement_repo->set('num_termine', $year, $month, $num_termine);
        $measurement_repo->set('num_solv_participations', $prev_year, $prev_month, $solv_num);
        $measurement_repo->set('sum_solv_distance', $prev_year, $prev_month, $solv_distance);
        $measurement_repo->set('sum_solv_elevation', $prev_year, $prev_month, $solv_elevation);
        $measurement_repo->set('avg_training_participants', $prev_year, $prev_month, $avg_training_participants);
        $measurement_repo->set('num_news', $prev_year, $prev_month, $num_news);
        $measurement_repo->set('num_weekly_pictures', $prev_year, $prev_month, $num_weekly_pictures);

        $this->logAndOutput("Users & Members measurements updated:");
        $this->logAndOutput("  num_users: {$num_users}");
        $this->logAndOutput("  num_members: {$num_members}");
        $this->logAndOutput("  num_member_users: {$num_member_users}");
        $this->logAndOutput("Termine & Anlässe measurements updated:");
        $this->logAndOutput("  num_termine: {$num_termine}");
        $this->logAndOutput("  num_solv_participations: {$solv_num}");
        $this->logAndOutput("  sum_solv_distance: {$solv_distance}");
        $this->logAndOutput("  sum_solv_elevation: {$solv_elevation}");
        $this->logAndOutput("  avg_training_participants: {$avg_training_participants}");
        $this->logAndOutput("Website-Aktivität measurements updated:");
        $this->logAndOutput("  num_news: {$num_news}");
        $this->logAndOutput("  num_weekly_pictures: {$num_weekly_pictures}");
        $this->logAndOutput("Measurements updated.");

        return Command::SUCCESS;
    }

    private function getMonthCriteria(string $date_field, \DateTime $start, \DateTime $end): Criteria {
        return Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->eq('on_off', 1),
                Criteria::expr()->gte($date_field, $start),
                Criteria::expr()->lt($date_field, $end),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
    }
}
