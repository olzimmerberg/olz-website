<?php

namespace Olz\Command;

use Doctrine\Common\Collections\Criteria;
use Olz\Apps\Members\Utils\MembersUtilsTrait;
use Olz\Command\Common\OlzCommand;
use Olz\Entity\Measurement;
use Olz\Entity\Members\Member;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\SolvEvent;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminReaction;
use Olz\Entity\Users\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:update-measurements')]
class UpdateMeasurementsCommand extends OlzCommand {
    use MembersUtilsTrait;

    /** Bump to trigger a backfill whenever the measurement calculation changes. */
    public const CALCULATION_VERSION = 2;

    private const EARLIEST_YEAR = 2006;
    private const EARLIEST_MONTH = 1;

    /** Metrics which describe a state and can be calculated for any month. */
    private const SNAPSHOT_NAMES = [
        'num_users',
        'num_members',
        'num_member_users',
        'num_termine',
    ];

    /** Metrics which accumulate during a month; meaningless for the current (incomplete) month. */
    private const EVENT_NAMES = [
        'num_solv_participations',
        'avg_training_participants',
        'num_news',
    ];

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $month = intval($this->dateUtils()->getCurrentDateInFormat('m'));
        $prev_year = $month > 1 ? $year : $year - 1;
        $prev_month = $month > 1 ? $month - 1 : 12;

        $this->updateMeasurementsForMonth($prev_year, $prev_month);
        $this->updateMeasurementsForMonth($year, $month);

        $measurement_repo = $this->entityManager()->getRepository(Measurement::class);
        // Anything not calculated with the current version is stale.
        $stale_criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->in('name', array_merge(self::SNAPSHOT_NAMES, self::EVENT_NAMES)),
                Criteria::expr()->neq('version', self::CALCULATION_VERSION),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;
        $is_stale = count($measurement_repo->matching($stale_criteria)) > 0;
        if ($is_stale) {
            $this->backfill(self::EARLIEST_YEAR, self::EARLIEST_MONTH, $year, $month);
        }

        $this->entityManager()->flush();
        return Command::SUCCESS;
    }

    private function backfill(int $from_year, int $from_month, int $to_year, int $to_month): void {
        $year = $from_year;
        $month = $from_month;
        while ($year < $to_year || ($year === $to_year && $month <= $to_month)) {
            $this->updateMeasurementsForMonth($year, $month);
            if ($month >= 12) {
                $month = 1;
                $year++;
            } else {
                $month++;
            }
        }
    }

    private function updateMeasurementsForMonth(int $year, int $month): void {
        [$start, $next] = $this->getMonthRange($year, $month);
        $month_key = sprintf('%04d-%02d', $year, $month);
        $current_year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $current_month = intval($this->dateUtils()->getCurrentDateInFormat('m'));
        $is_current_month = ($year === $current_year && $month === $current_month);

        $user_repo = $this->entityManager()->getRepository(User::class);
        $member_repo = $this->entityManager()->getRepository(Member::class);
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin_reaction_repo = $this->entityManager()->getRepository(TerminReaction::class);
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);
        $solv_person_repo = $this->entityManager()->getRepository(SolvPerson::class);
        $solv_result_repo = $this->entityManager()->getRepository(SolvResult::class);

        // Benutzer & Mitglieder. Entities which were deleted later still count for the
        // months before their deletion, which is why there is no `deleted_at` filter.
        $num_users = 0;
        $num_member_users = 0;
        foreach ($user_repo->findAll() as $user) {
            if ($this->membersUtils()->isUserActiveInMonth($user, $year, $month)) {
                $num_users++;
            }
            if ($this->membersUtils()->isUserMembershipActiveInMonth($user, $year, $month)) {
                $num_member_users++;
            }
        }

        // Mitglieder: `Eintritt` / `Austritt` from the member data.
        $num_members = 0;
        foreach ($member_repo->findAll() as $member) {
            if ($this->membersUtils()->isMemberActiveInMonth($member, $year, $month)) {
                $num_members++;
            }
        }

        // Termine.
        $termine = [...$termin_repo->matching($this->getMonthCriteria('start_date', $start, $next))];
        $num_termine = count($termine);

        // Trainings (Kartentrainings): Durchschnitt Teilnehmer pro Training.
        $training_termin_ids = [];
        foreach ($termine as $termin) {
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
            foreach ($termin_reaction_repo->findBy(['termin' => $termin_id]) as $reaction) {
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

        // SOLV-Anlässe: Teilnahmen unserer Mitglieder (solv_people.member).
        $member_person_ids = [];
        foreach ($solv_person_repo->findBy([]) as $solv_person) {
            $person_id = $solv_person->getId();
            if ($person_id !== null && $solv_person->getMember() > 0) {
                $member_person_ids[$person_id] = true;
            }
        }
        $num_solv_participations = 0;
        foreach ($solv_event_repo->getSolvEventsForYear($year) as $solv_event) {
            if ($solv_event->getDate()->format('Y-m') !== $month_key) {
                continue;
            }
            foreach ($solv_result_repo->findBy(['event' => $solv_event->getSolvUid()]) as $solv_result) {
                if (isset($member_person_ids[$solv_result->getPerson()])) {
                    $num_solv_participations++;
                }
            }
        }
        // Website-Aktivität.
        $num_news = count($news_repo->matching($this->getMonthCriteria('published_date', $start, $next)));

        $values = [
            'num_users' => (float) $num_users,
            'num_members' => (float) $num_members,
            'num_member_users' => (float) $num_member_users,
            'num_termine' => (float) $num_termine,
            'num_solv_participations' => (float) $num_solv_participations,
            'avg_training_participants' => (float) $avg_training_participants,
            'num_news' => (float) $num_news,
        ];

        $measurement_repo = $this->entityManager()->getRepository(Measurement::class);
        foreach (self::SNAPSHOT_NAMES as $name) {
            $measurement_repo->set($name, $year, $month, $values[$name], self::CALCULATION_VERSION);
        }
        foreach (self::EVENT_NAMES as $name) {
            if ($is_current_month) {
                // The current month is still in progress: it doesn't have a value for these yet,
                // so a stale row of an earlier calculation must not linger around.
                $measurement_repo->remove($name, $year, $month);
            } else {
                $measurement_repo->set($name, $year, $month, $values[$name], self::CALCULATION_VERSION);
            }
        }

        $this->logAndOutput("Updated measurements for {$month_key}");
    }

    private function getMonthCriteria(string $date_field, \DateTime $start, \DateTime $end): Criteria {
        return Criteria::create()
            ->where(Criteria::expr()->andX(
                // Entities which were deleted later still count for past months.
                Criteria::expr()->orX(
                    Criteria::expr()->isNull('deleted_at'),
                    Criteria::expr()->gte('deleted_at', $start),
                ),
                Criteria::expr()->gte($date_field, $start),
                Criteria::expr()->lt($date_field, $end),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
    }

    /** @return array{0: \DateTime, 1: \DateTime} */
    private function getMonthRange(int $year, int $month): array {
        $month_start = new \DateTime(sprintf('%04d-%02d-01 00:00:00', $year, $month));
        return [$month_start, (clone $month_start)->modify('+1 month')];
    }
}
