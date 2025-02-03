<?php

namespace Olz\Repository\Termine;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Repository\Common\OlzRepository;
use Olz\Termine\Utils\TermineFilterUtils;

/**
 * @extends OlzRepository<Termin>
 */
class TerminRepository extends OlzRepository {
    /** @return Collection<int, Termin>&iterable<Termin> */
    public function getAllActive(): Collection {
        $termine_utils = TermineFilterUtils::fromEnv();
        $is_not_archived = $termine_utils->getIsNotArchivedCriteria();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('on_off', 1),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }

    public function updateTerminFromSolvEvent(Termin $termin, ?SolvEvent $solv_event_arg = null): void {
        $solv_id = $termin->getSolvId();
        if (!$solv_id) {
            $this->log()->warning("Update termin {$termin->getId()} from SOLV: no SOLV ID.");
            return;
        }
        $solv_event = $solv_event_arg;
        if ($solv_event_arg === null) {
            $solv_event_repo = $this->getEntityManager()->getRepository(SolvEvent::class);
            $solv_event = $solv_event_repo->findOneBy(['solv_uid' => $solv_id]);
        } else {
            if ($solv_id !== $solv_event_arg->getSolvUid()) {
                $this->log()->warning("Update termin {$termin->getId()} from SOLV: SOLV ID mismatch ({$solv_id} vs. {$solv_event_arg->getSolvUid()}).");
                return;
            }
        }

        $duration_days = $solv_event->getDuration() - 1;
        $duration = new \DateInterval("P{$duration_days}D");
        $end_date = (clone $solv_event->getDate())->add($duration);
        $deadline = $solv_event->getDeadline()
            ? (clone $solv_event->getDeadline())->setTime(23, 59, 59) : null;
        $link = $solv_event->getLink() ?: '-';
        $club = $solv_event->getClub() ?: '-';
        $map = $solv_event->getMap() ?: '-';
        $location = $solv_event->getLocation() ?: '-';
        $text = <<<ZZZZZZZZZZ
            Link: {$link}

            Organisator: {$club}

            Karte: {$map}

            Ort: {$location}
            ZZZZZZZZZZ;

        $termin->setStartDate($solv_event->getDate());
        $termin->setStartTime(null);
        $termin->setEndDate($end_date);
        $termin->setEndTime(null);
        $termin->setDeadline($deadline);
        $termin->setTitle($solv_event->getName());
        $termin->setText($text);
        $termin->setNewsletter(false); // TODO: Enable Newsletter for SOLV Termine
        $termin->setLocation(null);
        $termin->setCoordinateX($solv_event->getCoordX());
        $termin->setCoordinateY($solv_event->getCoordY());
        $this->log()->info("Termin {$termin->getId()} updated from SOLV.");
    }
}
